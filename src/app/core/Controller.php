<?php
declare(strict_types=1);

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'member'): void
    {
        $viewFile = dirname(__DIR__) . '/views/' . $view . '.php';
        $judul = $data['judul'] ?? 'Litera';

        extract($data, EXTR_SKIP);

        require dirname(__DIR__) . '/views/layouts/' . $layout . '.php';
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function input(string $key, string $default = ''): string
    {
        return trim((string) ($_POST[$key] ?? $default));
    }

    protected function halaman(): int
    {
        return max(1, (int) ($_GET['halaman'] ?? 1));
    }
}
