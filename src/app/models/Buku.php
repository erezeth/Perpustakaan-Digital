<?php
declare(strict_types=1);

final class Buku
{
    private const SELECT = 'SELECT b.*, k.nama_kategori FROM buku b JOIN kategori k ON k.id = b.kategori_id';

    public static function cari(string $q, int $kategoriId, int $limit, int $offset): array
    {
        [$where, $params] = self::filter($q, $kategoriId);
        $sql = self::SELECT . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY b.judul LIMIT ' . $limit . ' OFFSET ' . $offset;
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function hitung(string $q, int $kategoriId): int
    {
        [$where, $params] = self::filter($q, $kategoriId);
        $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM buku b WHERE ' . implode(' AND ', $where));
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    private static function filter(string $q, int $kategoriId): array
    {
        $where = ['1 = 1'];
        $params = [];
        if ($q !== '') {
            $where[] = '(b.judul LIKE ? OR b.penulis LIKE ? OR b.isbn LIKE ?)';
            $params = array_fill(0, 3, '%' . $q . '%');
        }
        if ($kategoriId > 0) {
            $where[] = 'b.kategori_id = ?';
            $params[] = $kategoriId;
        }

        return [$where, $params];
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(self::SELECT . ' WHERE b.id = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public static function rekomendasi(int $limit = 4): array
    {
        return Database::pdo()->query(self::SELECT . ' ORDER BY b.id DESC LIMIT ' . $limit)->fetchAll();
    }

    public static function tersedia(): array
    {
        return Database::pdo()->query(self::SELECT . ' WHERE b.stok > 0 ORDER BY b.judul')->fetchAll();
    }

    public static function hitungSemua(): int
    {
        return (int) Database::pdo()->query('SELECT COUNT(*) FROM buku')->fetchColumn();
    }

    public static function buat(array $d): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO buku (kategori_id, judul, penulis, penerbit, tahun_terbit, isbn, sinopsis, cover, stok) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $d['kategori_id'], $d['judul'], $d['penulis'], $d['penerbit'] ?: null,
            $d['tahun_terbit'] ?: null, $d['isbn'] ?: null, $d['sinopsis'] ?: null,
            $d['cover'] ?: null, $d['stok'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $d): void
    {
        $stmt = Database::pdo()->prepare('UPDATE buku SET kategori_id = ?, judul = ?, penulis = ?, penerbit = ?, tahun_terbit = ?, isbn = ?, sinopsis = ?, cover = ?, stok = ? WHERE id = ?');
        $stmt->execute([
            $d['kategori_id'], $d['judul'], $d['penulis'], $d['penerbit'] ?: null,
            $d['tahun_terbit'] ?: null, $d['isbn'] ?: null, $d['sinopsis'] ?: null,
            $d['cover'] ?: null, $d['stok'], $id,
        ]);
    }

    public static function hapus(int $id): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM buku WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function dipakai(int $id): bool
    {
        $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM detail_peminjaman WHERE buku_id = ?');
        $stmt->execute([$id]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function stokTersedia(): int
    {
        return (int) Database::pdo()->query('SELECT COALESCE(SUM(stok), 0) FROM buku')->fetchColumn();
    }
}
