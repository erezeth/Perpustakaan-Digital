<?php
declare(strict_types=1);

final class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function role(): ?string
    {
        return self::user()['role'] ?? null;
    }

    public static function id(): ?int
    {
        return isset(self::user()['id']) ? (int) self::user()['id'] : null;
    }

    public static function loginBerhasil(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'nama' => $user['nama'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];
    }

    public static function verifikasiPassword(array $user, string $password): bool
    {
        return password_verify($password, (string) $user['password_hash']);
    }

    public static function logout(): void
    {
        self::hapusSesi();
    }

    public static function hapusSesi(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $param = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $param['path'], $param['domain'] ?? '', (bool) $param['secure'], (bool) $param['httponly']);
        }
        session_destroy();
    }
}
