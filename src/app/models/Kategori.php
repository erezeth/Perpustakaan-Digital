<?php
declare(strict_types=1);

final class Kategori
{
    public static function all(): array
    {
        return Database::pdo()->query('SELECT k.*, COUNT(b.id) AS jumlah_buku FROM kategori k LEFT JOIN buku b ON b.kategori_id = k.id GROUP BY k.id ORDER BY k.nama_kategori')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM kategori WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public static function buat(string $nama): void
    {
        $stmt = Database::pdo()->prepare('INSERT INTO kategori (nama_kategori) VALUES (?)');
        $stmt->execute([$nama]);
    }

    public static function update(int $id, string $nama): void
    {
        $stmt = Database::pdo()->prepare('UPDATE kategori SET nama_kategori = ? WHERE id = ?');
        $stmt->execute([$nama, $id]);
    }

    public static function hapus(int $id): void
    {
        $stmt = Database::pdo()->prepare('DELETE FROM kategori WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function dipakai(int $id): bool
    {
        $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM buku WHERE kategori_id = ?');
        $stmt->execute([$id]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function namaDipakai(string $nama, int $kecualiId = 0): bool
    {
        $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM kategori WHERE nama_kategori = ? AND id <> ?');
        $stmt->execute([$nama, $kecualiId]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
