<?php
declare(strict_types=1);

/** Bootstrap Litera: konfigurasi, konstanta, session, autoload kelas, helper. */

$config = require __DIR__ . '/config/config.php';

define('DB_HOST', (string) $config['db']['host']);
define('DB_NAME', (string) $config['db']['name']);
define('DB_USER', (string) $config['db']['user']);
define('DB_PASS', (string) $config['db']['pass']);
define('MAX_BUKU_AKTIF', (int) $config['max_buku_aktif']);
define('MAX_HARI_PINJAM', (int) $config['max_hari_pinjam']);
define('DENDA_TERLAMBAT_DEFAULT', (int) $config['denda_terlambat']);

// BASE_URL: halaman diakses dari root proyek (default XAMPP) atau public/ sebagai docroot.
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$scriptDir = rtrim($scriptDir, '/');
if (str_ends_with($scriptDir, '/public')) {
    $scriptDir = substr($scriptDir, 0, -7);
}
define('BASE_URL', $scriptDir);
define('ASSET_URL', BASE_URL . '/assets');
define('UPLOAD_URL', BASE_URL . '/uploads');
define('UPLOAD_DIR', dirname(__DIR__) . '/public/uploads');

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'path' => BASE_URL === '' ? '/' : BASE_URL,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/core/helpers.php';

foreach (['core', 'models', 'controllers'] as $folder) {
    foreach (glob(__DIR__ . '/' . $folder . '/*.php') as $file) {
        require_once $file;
    }
}
