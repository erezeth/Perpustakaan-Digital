<?php
/**
 * Konfigurasi Litera.
 * Jangan ubah langsung nilai sensitif di repo — buat config.local.php (lihat config.example.php).
 */

$config = [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'litera',
        'user' => 'root',
        'pass' => '',
    ],
    'max_buku_aktif' => 3,
    'max_hari_pinjam' => 7,
    'denda_terlambat' => 1000,
];

$lokal = __DIR__ . '/config.local.php';
if (is_file($lokal)) {
    $config = array_replace_recursive($config, require $lokal);
}

return $config;
