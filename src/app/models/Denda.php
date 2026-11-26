<?php
declare(strict_types=1);

final class Denda
{
    public static function masterAll(): array
    {
        return Database::pdo()->query('SELECT * FROM denda_master ORDER BY FIELD(jenis, \'terlambat\', \'rusak\', \'hilang\')')->fetchAll();
    }

    public static function masterFind(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM denda_master WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public static function masterBuat(array $d): void
    {
        $stmt = Database::pdo()->prepare('INSERT INTO denda_master (jenis, tipe_hitung, nominal, keterangan) VALUES (?, ?, ?, ?)');
        $stmt->execute([$d['jenis'], $d['tipe_hitung'], $d['nominal'], $d['keterangan'] ?: null]);
    }

    public static function masterUpdate(int $id, array $d): void
    {
        $stmt = Database::pdo()->prepare('UPDATE denda_master SET jenis = ?, tipe_hitung = ?, nominal = ?, keterangan = ? WHERE id = ?');
        $stmt->execute([$d['jenis'], $d['tipe_hitung'], $d['nominal'], $d['keterangan'] ?: null, $id]);
    }

    public static function masterHapus(int $id): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM denda_master WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Tarif dari denda_master; terlambat jatuh ke nilai config bila belum diisi. */
    public static function tarif(string $jenis): float
    {
        $stmt = Database::pdo()->prepare('SELECT nominal FROM denda_master WHERE jenis = ?');
        $stmt->execute([$jenis]);
        $nominal = $stmt->fetchColumn();

        if ($nominal === false) {
            return $jenis === 'terlambat' ? (float) DENDA_TERLAMBAT_DEFAULT : 0.0;
        }

        return (float) $nominal;
    }

    public static function untukUser(int $userId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT dn.*, p.tgl_pinjam, p.tgl_jatuh_tempo,
                (SELECT GROUP_CONCAT(b.judul SEPARATOR ", ") FROM detail_peminjaman d JOIN buku b ON b.id = d.buku_id WHERE d.peminjaman_id = p.id) AS buku
             FROM denda dn
             JOIN peminjaman p ON p.id = dn.peminjaman_id
             WHERE p.user_id = ?
             ORDER BY dn.id DESC'
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public static function totalBelum(?int $userId = null): float
    {
        if ($userId === null) {
            return (float) Database::pdo()->query("SELECT COALESCE(SUM(nominal), 0) FROM denda WHERE status_bayar = 'belum'")->fetchColumn();
        }

        $stmt = Database::pdo()->prepare("SELECT COALESCE(SUM(dn.nominal), 0) FROM denda dn JOIN peminjaman p ON p.id = dn.peminjaman_id WHERE dn.status_bayar = 'belum' AND p.user_id = ?");
        $stmt->execute([$userId]);

        return (float) $stmt->fetchColumn();
    }

    public static function jumlahBelum(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM denda WHERE status_bayar = 'belum'")->fetchColumn();
    }

    public static function lunas(int $id): void
    {
        $stmt = Database::pdo()->prepare("UPDATE denda SET status_bayar = 'lunas', tgl_bayar = CURDATE() WHERE id = ? AND status_bayar = 'belum'");
        $stmt->execute([$id]);
    }

    public static function periode(string $periode): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT dn.*, u.nama,
                (SELECT GROUP_CONCAT(b.judul SEPARATOR ", ") FROM detail_peminjaman d JOIN buku b ON b.id = d.buku_id WHERE d.peminjaman_id = p.id) AS buku
             FROM denda dn
             JOIN peminjaman p ON p.id = dn.peminjaman_id
             JOIN users u ON u.id = p.user_id
             WHERE DATE_FORMAT(dn.created_at, "%Y-%m") = ?
             ORDER BY dn.id DESC'
        );
        $stmt->execute([$periode]);

        return $stmt->fetchAll();
    }

    public static function untukPeminjaman(int $peminjamanId): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT dn.*, b.judul FROM denda dn
             LEFT JOIN detail_peminjaman d ON d.id = dn.detail_id
             LEFT JOIN buku b ON b.id = d.buku_id
             WHERE dn.peminjaman_id = ?
             ORDER BY dn.id'
        );
        $stmt->execute([$peminjamanId]);

        return $stmt->fetchAll();
    }
}
