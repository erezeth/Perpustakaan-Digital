<?php
declare(strict_types=1);

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf'];
    }

    public static function verify(): void
    {
        $token = (string) ($_POST['csrf'] ?? '');
        if ($token === '' || !hash_equals(self::token(), $token)) {
            http_response_code(419);
            exit('Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.');
        }
    }
}
