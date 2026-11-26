<?php
/**
 * Contoh override. Salin menjadi config.local.php lalu sesuaikan.
 * config.local.php tidak perlu di-commit.
 */
return [
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
