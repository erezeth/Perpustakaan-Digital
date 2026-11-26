<?php
declare(strict_types=1);

final class Peminjaman
{
    private const SELECT_LIST =
        'SELECT p.*, u.nama, u.username,
            (SELECT GROUP_CONCAT(b.judul SEPARATOR ", ") FROM detail_peminjaman d JOIN buku b ON b.id = d.buku_id WHERE d.peminjaman_id = p.id) AS buku,
            (SELECT COUNT(*) FROM detail_peminjaman d WHERE d.peminjaman_id = p.id) AS jumlah_buku
         FROM peminjaman p JOIN users u ON u.id = p.user_id';

    /**
     * Simpan peminjaman + kurangi stok dalam satu transaksi database.
     * @return array{0: bool, 1: string, 2: ?int}
     */
    public static function ajukanPinjam(int $userId, string $tglPinjam, array $bukuIds, ?int $petugasId = null): array
    {
        $bukuIds = array_values(array_unique(array_filter(array_map('intval', $bukuIds))));
        if ($bukuIds === []) {
            return [false, 'Pilih minimal satu buku.', null];
        }
        if (count($bukuIds) > MAX_BUKU_AKTIF) {
            return [false, 'Maksimal ' . MAX_BUKU_AKTIF . ' buku dalam satu peminjaman.', null];
        }

        $tgl = DateTime::createFromFormat('Y-m-d', $tglPinjam);
        if (!$tgl || $tgl->format('Y-m-d') !== $tglPinjam) {
            return [false, 'Tanggal pinjam tidak valid.', null];
        }
        if ($tglPinjam < date('Y-m-d')) {
            return [false, 'Tanggal pinjam tidak boleh sebelum hari ini.', null];
        }

        $pdo = Database::pdo();
        try {
            $pdo->beginTransaction();

            $in = implode(',', array_fill(0, count($bukuIds), '?'));
            $stmt = $pdo->prepare("SELECT id, judul, stok FROM buku WHERE id IN ($in) FOR UPDATE");
            $stmt->execute($bukuIds);
            $daftar = [];
            foreach ($stmt->fetchAll() as $b) {
                $daftar[(int) $b['id']] = $b;
            }
            if (count($daftar) !== count($bukuIds)) {
                $pdo->rollBack();
                return [false, 'Ada buku yang tidak ditemukan.', null];
            }

            $aktif = self::hitungBukuAktif($userId);
            if ($aktif + count($bukuIds) > MAX_BUKU_AKTIF) {
                $pdo->rollBack();
                return [false, sprintf('Batas %d buku aktif terlampaui. Anda masih meminjam %d buku.', MAX_BUKU_AKTIF, $aktif), null];
            }

            foreach ($bukuIds as $id) {
                if ((int) $daftar[$id]['stok'] < 1) {
                    $pdo->rollBack();
                    return [false, 'Stok habis: ' . $daftar[$id]['judul'] . '.', null];
                }
            }

            $jatuhTempo = (clone $tgl)->modify('+' . MAX_HARI_PINJAM . ' days')->format('Y-m-d');
            $stmt = $pdo->prepare("INSERT INTO peminjaman (user_id, petugas_id, tgl_pinjam, tgl_jatuh_tempo, status) VALUES (?, ?, ?, ?, 'dipinjam')");
            $stmt->execute([$userId, $petugasId, $tglPinjam, $jatuhTempo]);
            $peminjamanId = (int) $pdo->lastInsertId();

            $detail = $pdo->prepare('INSERT INTO detail_peminjaman (peminjaman_id, buku_id) VALUES (?, ?)');
            $kurangi = $pdo->prepare('UPDATE buku SET stok = stok - 1 WHERE id = ?');
            foreach ($bukuIds as $id) {
                $detail->execute([$peminjamanId, $id]);
                $kurangi->execute([$id]);
            }

            $pdo->commit();

            return [true, '', $peminjamanId];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return [false, 'Peminjaman gagal disimpan. Silakan coba lagi.', null];
        }
    }

    public static function hitungBukuAktif(int $userId): int
    {
        $stmt = Database::pdo()->prepare("SELECT COUNT(*) FROM detail_peminjaman d JOIN peminjaman p ON p.id = d.peminjaman_id WHERE p.user_id = ? AND p.status IN ('dipinjam', 'menunggu_verifikasi')");
        $stmt->execute([$userId]);

        return (int) $stmt->fetchColumn();
    }

    public static function cari(array $filter = [], int $limit = 50, int $offset = 0): array
    {
        [$where, $params] = self::filter($filter);
        $sql = self::SELECT_LIST . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY p.id DESC LIMIT ' . $limit . ' OFFSET ' . $offset;
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function hitung(array $filter = []): int
    {
        [$where, $params] = self::filter($filter);
        $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM peminjaman p JOIN users u ON u.id = p.user_id WHERE ' . implode(' AND ', $where));
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /** @return array{0: array<int, string>, 1: array<int, mixed>} */
    private static function filter(array $f): array
    {
        $where = ['1 = 1'];
        $params = [];

        if (!empty($f['user_id'])) {
            $where[] = 'p.user_id = ?';
            $params[] = (int) $f['user_id'];
        }

        $status = $f['status'] ?? '';
        if ($status === 'aktif') {
            $where[] = "p.status IN ('dipinjam', 'menunggu_verifikasi')";
        } elseif ($status === 'terlambat') {
            $where[] = "p.status = 'dipinjam' AND p.tgl_jatuh_tempo < CURDATE()";
        } elseif ($status === 'dipinjam') {
            $where[] = "p.status = 'dipinjam' AND p.tgl_jatuh_tempo >= CURDATE()";
        } elseif (in_array($status, ['menunggu_verifikasi', 'dikembalikan'], true)) {
            $where[] = 'p.status = ?';
            $params[] = $status;
        }

        if (!empty($f['q'])) {
            $where[] = '(u.nama LIKE ? OR u.username LIKE ? OR EXISTS (SELECT 1 FROM detail_peminjaman d JOIN buku b ON b.id = d.buku_id WHERE d.peminjaman_id = p.id AND b.judul LIKE ?))';
            $params[] = '%' . $f['q'] . '%';
            $params[] = '%' . $f['q'] . '%';
            $params[] = '%' . $f['q'] . '%';
        }

        if (!empty($f['periode'])) {
            $where[] = 'DATE_FORMAT(p.tgl_pinjam, "%Y-%m") = ?';
            $params[] = $f['periode'];
        }
        if (!empty($f['dari'])) {
            $where[] = 'p.tgl_pinjam >= ?';
            $params[] = $f['dari'];
        }
        if (!empty($f['sampai'])) {
            $where[] = 'p.tgl_pinjam <= ?';
            $params[] = $f['sampai'];
        }

        return [$where, $params];
    }

    public static function ambilTransaksi(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(self::SELECT_LIST . ' WHERE p.id = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public static function detail(int $peminjamanId): array
    {
        $stmt = Database::pdo()->prepare('SELECT d.*, b.judul, b.penulis, b.cover FROM detail_peminjaman d JOIN buku b ON b.id = d.buku_id WHERE d.peminjaman_id = ? ORDER BY d.id');
        $stmt->execute([$peminjamanId]);

        return $stmt->fetchAll();
    }

    public static function aktifUser(int $userId): array
    {
        return self::cari(['user_id' => $userId, 'status' => 'aktif'], 50);
    }

    /** @return array{0: bool, 1: string} */
    public static function ajukanKembali(int $id, int $userId): array
    {
        $stmt = Database::pdo()->prepare("UPDATE peminjaman SET status = 'menunggu_verifikasi' WHERE id = ? AND user_id = ? AND status = 'dipinjam'");
        $stmt->execute([$id, $userId]);

        return $stmt->rowCount() > 0
            ? [true, 'Pengembalian diajukan. Serahkan buku ke petugas untuk diverifikasi.']
            : [false, 'Pengajuan gagal. Pinjaman mungkin sudah dikembalikan.'];
    }

    /**
     * Verifikasi pengembalian oleh petugas: hitung denda, simpan, update status, tambah stok.
     * @param array<int, string> $kondisi detail_id => baik|rusak|hilang
     * @return array{0: bool, 1: string, 2: float}
     */
    public static function prosesKembali(int $id, array $kondisi, int $petugasId): array
    {
        $pdo = Database::pdo();
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('SELECT * FROM peminjaman WHERE id = ? FOR UPDATE');
            $stmt->execute([$id]);
            $p = $stmt->fetch();
            if (!$p) {
                $pdo->rollBack();
                return [false, 'Transaksi tidak ditemukan.', 0];
            }
            if ($p['status'] === 'dikembalikan') {
                $pdo->rollBack();
                return [false, 'Transaksi ini sudah dikembalikan.', 0];
            }

            $stmt = $pdo->prepare('SELECT * FROM detail_peminjaman WHERE peminjaman_id = ? FOR UPDATE');
            $stmt->execute([$id]);
            $details = $stmt->fetchAll();

            [$hariTerlambat, $nominalTerlambat] = self::hitungDenda($p['tgl_jatuh_tempo']);
            $total = 0.0;
            $simpanDenda = $pdo->prepare('INSERT INTO denda (peminjaman_id, detail_id, jenis, jumlah_hari, nominal) VALUES (?, ?, ?, ?, ?)');

            if ($nominalTerlambat > 0) {
                $simpanDenda->execute([$id, null, 'terlambat', $hariTerlambat, $nominalTerlambat]);
                $total += $nominalTerlambat;
            }

            $updateDetail = $pdo->prepare('UPDATE detail_peminjaman SET kondisi = ? WHERE id = ?');
            $tambahStok = $pdo->prepare('UPDATE buku SET stok = stok + 1 WHERE id = ?');

            foreach ($details as $d) {
                $kondisiBuku = $kondisi[(int) $d['id']] ?? 'baik';
                if (!in_array($kondisiBuku, ['baik', 'rusak', 'hilang'], true)) {
                    $kondisiBuku = 'baik';
                }
                $updateDetail->execute([$kondisiBuku, $d['id']]);

                if ($kondisiBuku !== 'baik') {
                    $nominal = Denda::tarif($kondisiBuku);
                    if ($nominal > 0) {
                        $simpanDenda->execute([$id, $d['id'], $kondisiBuku, null, $nominal]);
                        $total += $nominal;
                    }
                }
                if ($kondisiBuku !== 'hilang') {
                    $tambahStok->execute([$d['buku_id']]);
                }
            }

            $update = $pdo->prepare("UPDATE peminjaman SET status = 'dikembalikan', tgl_kembali = CURDATE(), petugas_id = ? WHERE id = ?");
            $update->execute([$petugasId, $id]);

            $pdo->commit();

            return [true, 'Pengembalian selesai diproses.', $total];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return [false, 'Pengembalian gagal diproses. Silakan coba lagi.', 0];
        }
    }

    /** @return array{0: int, 1: float} [hari terlambat, nominal denda] */
    private static function hitungDenda(string $jatuhTempo): array
    {
        $hari = self::hariTerlambat($jatuhTempo);

        return [$hari, $hari * Denda::tarif('terlambat')];
    }

    private static function hariTerlambat(string $jatuhTempo): int
    {
        $tempo = new DateTime($jatuhTempo);
        $akhir = new DateTime('today');

        return $akhir > $tempo ? (int) $tempo->diff($akhir)->days : 0;
    }

    public static function jumlahAktif(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM peminjaman WHERE status IN ('dipinjam', 'menunggu_verifikasi')")->fetchColumn();
    }

    public static function jumlahTerlambat(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam' AND tgl_jatuh_tempo < CURDATE()")->fetchColumn();
    }

    public static function terbaru(int $limit = 5): array
    {
        return self::cari([], $limit);
    }

    public static function periode(string $periode, bool $pengembalian = false): array
    {
        if ($pengembalian) {
            $stmt = Database::pdo()->prepare(self::SELECT_LIST . ' WHERE p.status = \'dikembalikan\' AND DATE_FORMAT(p.tgl_kembali, "%Y-%m") = ? ORDER BY p.tgl_kembali DESC');
        } else {
            $stmt = Database::pdo()->prepare(self::SELECT_LIST . ' WHERE DATE_FORMAT(p.tgl_pinjam, "%Y-%m") = ? ORDER BY p.tgl_pinjam DESC');
        }
        $stmt->execute([$periode]);

        return $stmt->fetchAll();
    }
}
