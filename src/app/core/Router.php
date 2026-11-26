<?php
declare(strict_types=1);

final class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: array, role: ?string}> */
    private array $routes = [];

    public function get(string $pattern, array $handler, ?string $role = null): void
    {
        $this->add('GET', $pattern, $handler, $role);
    }

    public function post(string $pattern, array $handler, ?string $role = null): void
    {
        $this->add('POST', $pattern, $handler, $role);
    }

    private function add(string $method, string $pattern, array $handler, ?string $role): void
    {
        $this->routes[] = compact('method', 'pattern', 'handler', 'role');
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $regex = '#^' . str_replace(['/', '{id}'], ['\/', '([0-9]+)'], $route['pattern']) . '$#';
            if (!preg_match($regex, $path, $cocok)) {
                continue;
            }

            $this->jagaAkses($route['role']);

            if ($method === 'POST') {
                Csrf::verify();
            }

            [$kelas, $aksi] = $route['handler'];
            $params = array_map('intval', array_slice($cocok, 1));
            (new $kelas())->{$aksi}(...$params);

            return;
        }

        http_response_code(404);
        exit('Halaman tidak ditemukan.');
    }

    private function jagaAkses(?string $role): void
    {
        if ($role === null) {
            return;
        }

        if (!Auth::check()) {
            header('Location: ' . url('/login'));
            exit;
        }

        if (Auth::role() !== $role) {
            http_response_code(403);
            exit('Akses ditolak.');
        }
    }
}
