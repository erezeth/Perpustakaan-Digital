<?php
declare(strict_types=1);

final class Flash
{
    public static function set(string $jenis, string $pesan): void
    {
        $_SESSION['flash'] = ['jenis' => $jenis, 'pesan' => $pesan];
    }

    public static function ambil(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }
}
