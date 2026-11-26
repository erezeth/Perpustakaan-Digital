<?php
declare(strict_types=1);

/** Front controller Litera. */

require dirname(__DIR__) . '/app/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = (string) ($_GET['r'] ?? parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
$path = rawurldecode($path);

if (BASE_URL !== '' && str_starts_with($path, BASE_URL)) {
    $path = substr($path, strlen(BASE_URL));
}
if ($path === '' || $path === '/' || in_array($path, ['/index.php', '/public/index.php', '/public/'], true)) {
    $path = '/';
}
$path = '/' . trim($path, '/');
define('CURRENT_PATH', $path);

$router = new Router();

/* Umum */
$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [AuthController::class, 'form']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

/* Anggota */
$router->get('/beranda', [BerandaController::class, 'index'], 'anggota');
$router->get('/buku', [BukuController::class, 'index'], 'anggota');
$router->get('/buku/{id}', [BukuController::class, 'detail'], 'anggota');
$router->get('/pinjam', [PeminjamanController::class, 'form'], 'anggota');
$router->post('/pinjam', [PeminjamanController::class, 'store'], 'anggota');
$router->get('/pinjam/bukti/{id}', [PeminjamanController::class, 'bukti'], 'anggota');
$router->get('/kembali', [PengembalianController::class, 'index'], 'anggota');
$router->post('/kembali', [PengembalianController::class, 'ajukan'], 'anggota');
$router->get('/riwayat', [RiwayatController::class, 'anggota'], 'anggota');
$router->get('/denda', [DendaController::class, 'saya'], 'anggota');
$router->get('/profil', [ProfilController::class, 'index'], 'anggota');

/* Petugas */
$router->get('/admin', [DashboardController::class, 'index'], 'petugas');

$router->get('/admin/anggota', [AnggotaController::class, 'index'], 'petugas');
$router->get('/admin/anggota/tambah', [AnggotaController::class, 'create'], 'petugas');
$router->post('/admin/anggota', [AnggotaController::class, 'store'], 'petugas');
$router->get('/admin/anggota/{id}/edit', [AnggotaController::class, 'edit'], 'petugas');
$router->post('/admin/anggota/{id}', [AnggotaController::class, 'update'], 'petugas');
$router->post('/admin/anggota/{id}/hapus', [AnggotaController::class, 'destroy'], 'petugas');

$router->get('/admin/buku', [BukuController::class, 'adminIndex'], 'petugas');
$router->get('/admin/buku/tambah', [BukuController::class, 'create'], 'petugas');
$router->post('/admin/buku', [BukuController::class, 'store'], 'petugas');
$router->get('/admin/buku/{id}/edit', [BukuController::class, 'edit'], 'petugas');
$router->post('/admin/buku/{id}', [BukuController::class, 'update'], 'petugas');
$router->post('/admin/buku/{id}/hapus', [BukuController::class, 'destroy'], 'petugas');

$router->get('/admin/kategori', [KategoriController::class, 'index'], 'petugas');
$router->get('/admin/kategori/tambah', [KategoriController::class, 'create'], 'petugas');
$router->post('/admin/kategori', [KategoriController::class, 'store'], 'petugas');
$router->get('/admin/kategori/{id}/edit', [KategoriController::class, 'edit'], 'petugas');
$router->post('/admin/kategori/{id}', [KategoriController::class, 'update'], 'petugas');
$router->post('/admin/kategori/{id}/hapus', [KategoriController::class, 'destroy'], 'petugas');

$router->get('/admin/denda', [DendaController::class, 'index'], 'petugas');
$router->get('/admin/denda/tambah', [DendaController::class, 'create'], 'petugas');
$router->post('/admin/denda', [DendaController::class, 'store'], 'petugas');
$router->get('/admin/denda/{id}/edit', [DendaController::class, 'edit'], 'petugas');
$router->post('/admin/denda/{id}', [DendaController::class, 'update'], 'petugas');
$router->post('/admin/denda/{id}/hapus', [DendaController::class, 'destroy'], 'petugas');
$router->post('/admin/denda/{id}/lunas', [DendaController::class, 'lunas'], 'petugas');

$router->get('/admin/transaksi', [PeminjamanController::class, 'adminIndex'], 'petugas');
$router->get('/admin/transaksi/tambah', [PeminjamanController::class, 'adminCreate'], 'petugas');
$router->post('/admin/transaksi', [PeminjamanController::class, 'adminStore'], 'petugas');
$router->get('/admin/transaksi/{id}', [PeminjamanController::class, 'adminShow'], 'petugas');
$router->post('/admin/transaksi/{id}/kembali', [PengembalianController::class, 'proses'], 'petugas');

$router->get('/admin/riwayat', [RiwayatController::class, 'admin'], 'petugas');
$router->get('/admin/laporan', [LaporanController::class, 'buatLaporan'], 'petugas');

$router->dispatch($method, $path);
