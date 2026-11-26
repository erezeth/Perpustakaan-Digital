<?php
declare(strict_types=1);

final class User
{
    public static function cariUser(string $username): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);

        return $stmt->fetch() ?: null;
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public static function anggota(string $q, int $limit, int $offset): array
    {
        $where = ["role = 'anggota'"];
        $params = [];
        if ($q !== '') {
            $where[] = '(nama LIKE ? OR username LIKE ? OR email LIKE ?)';
            $params = array_fill(0, 3, '%' . $q . '%');
        }

        $sql = 'SELECT * FROM users WHERE ' . implode(' AND ', $where) . ' ORDER BY nama LIMIT ' . $limit . ' OFFSET ' . $offset;
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function hitungAnggota(string $q): int
    {
        $where = ["role = 'anggota'"];
        $params = [];
        if ($q !== '') {
            $where[] = '(nama LIKE ? OR username LIKE ? OR email LIKE ?)';
            $params = array_fill(0, 3, '%' . $q . '%');
        }

        $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM users WHERE ' . implode(' AND ', $where));
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public static function hitungAnggotaAktif(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM users WHERE role = 'anggota' AND status = 'aktif'")->fetchColumn();
    }

    public static function bahasaUsername(): string
    {
        $max = (int) Database::pdo()->query("SELECT COALESCE(MAX(CAST(SUBSTRING(username, 3) AS UNSIGNED)), 0) FROM users WHERE role = 'anggota' AND username LIKE 'A-%'")->fetchColumn();

        return 'A-' . str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    public static function buatAnggota(array $d): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO users (nama, username, email, password_hash, role, no_telp, alamat, status) VALUES (?, ?, ?, ?, \'anggota\', ?, ?, ?)');
        $stmt->execute([
            $d['nama'],
            $d['username'],
            $d['email'],
            password_hash($d['password'], PASSWORD_DEFAULT),
            $d['no_telp'] ?: null,
            $d['alamat'] ?: null,
            $d['status'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function buatPetugas(array $d): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("INSERT INTO users (nama, username, email, password_hash, role, status) VALUES (?, ?, ?, ?, 'petugas', ?)");
        $stmt->execute([
            $d['nama'],
            $d['username'],
            $d['email'],
            password_hash($d['password'], PASSWORD_DEFAULT),
            $d['status'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function updateAnggota(int $id, array $d): void
    {
        $sql = 'UPDATE users SET nama = ?, username = ?, email = ?, no_telp = ?, alamat = ?, status = ?';
        $params = [$d['nama'], $d['username'], $d['email'], $d['no_telp'] ?: null, $d['alamat'] ?: null, $d['status']];

        if ($d['password'] !== '') {
            $sql .= ', password_hash = ?';
            $params[] = password_hash($d['password'], PASSWORD_DEFAULT);
        }

        $params[] = $id;
        $stmt = Database::pdo()->prepare($sql . ' WHERE id = ?');
        $stmt->execute($params);
    }

    public static function hapus(int $id): void
    {
        $stmt = Database::pdo()->prepare("DELETE FROM users WHERE id = ? AND role = 'anggota'");
        $stmt->execute([$id]);
    }

    public static function dipakaiTransaksi(int $id): bool
    {
        $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM peminjaman WHERE user_id = ?');
        $stmt->execute([$id]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function usernameDipakai(string $username, int $kecualiId = 0): bool
    {
        $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM users WHERE username = ? AND id <> ?');
        $stmt->execute([$username, $kecualiId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function opsiAnggota(): array
    {
        return Database::pdo()->query("SELECT id, nama, username FROM users WHERE role = 'anggota' AND status = 'aktif' ORDER BY nama")->fetchAll();
    }
}
