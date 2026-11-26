<?php
$flash = Flash::ambil();
$petugas = Auth::user();
$segmen = explode('/', trim(CURRENT_PATH, '/'));
$aktif = $segmen[1] ?? 'dashboard';
$menu = [
    ['dashboard', 'Dashboard', 'grid', '/admin'],
    ['anggota', 'Kelola Anggota', 'users', '/admin/anggota'],
    ['buku', 'Kelola Buku', 'book-open', '/admin/buku'],
    ['kategori', 'Kelola Kategori', 'tag', '/admin/kategori'],
    ['denda', 'Kelola Denda', 'receipt', '/admin/denda'],
    ['transaksi', 'Transaksi', 'swap', '/admin/transaksi'],
    ['riwayat', 'Riwayat', 'history', '/admin/riwayat'],
    ['laporan', 'Laporan', 'chart', '/admin/laporan'],
];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#3D402F">
    <title><?= e($judul) ?> — Litera</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="<?= e(asset('css/tokens.css')) ?>" rel="stylesheet">
    <link href="<?= e(asset('css/app.css')) ?>" rel="stylesheet">
</head>
<body class="admin">
<div class="admin-shell">
    <aside class="sidebar">
        <?= logo(url('/admin'), true) ?>

        <nav class="side-nav" aria-label="Menu petugas">
            <?php foreach ($menu as [$kunci, $label, $namaIkon, $tautan]): ?>
                <a class="side-link <?= $aktif === $kunci ? 'aktif' : '' ?>"
                   href="<?= e(url($tautan)) ?>"<?= $aktif === $kunci ? ' aria-current="page"' : '' ?>>
                    <?= ikon($namaIkon) ?><span><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="side-user">
            <span class="avatar" aria-hidden="true"><?= e(inisial($petugas['nama'] ?? '')) ?></span>
            <div>
                <div class="side-user__nama"><?= e($petugas['nama'] ?? '') ?></div>
                <div class="side-user__peran">Petugas Perpustakaan</div>
            </div>
        </div>
    </aside>

    <div class="admin-main">
        <header class="topbar">
            <h1><?= e($judul) ?></h1>

            <form class="input topbar__cari" action="<?= e(url('/admin/buku')) ?>" role="search">
                <?= ikon('search') ?>
                <input type="search" name="q" placeholder="Cari buku..." aria-label="Cari buku">
            </form>

            <div class="topbar__aksi">
                <form method="post" action="<?= e(url('/logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn--sm btn--outline" type="submit">Keluar</button>
                </form>
            </div>
        </header>

        <main class="content">
            <?php require dirname(__DIR__) . '/partials/flash.php'; ?>
            <?php require $viewFile; ?>
        </main>
    </div>
</div>

<div class="modal" id="modalHapus" hidden>
    <div class="modal__latar" data-tutup></div>
    <div class="modal__kotak" role="dialog" aria-modal="true" aria-labelledby="modalHapusJudul">
        <div class="modal__ikon"><?= ikon('warning', 28) ?></div>
        <h2 id="modalHapusJudul">Hapus data?</h2>
        <p id="modalHapusTeks">Data yang dihapus tidak dapat dikembalikan.</p>
        <div class="modal__aksi">
            <button class="btn btn--outline" type="button" data-tutup>Batal</button>
            <button class="btn btn--danger" type="button" id="tombolHapus">Hapus</button>
        </div>
    </div>
</div>
<form method="post" id="formHapus" hidden><?= csrf_field() ?></form>

<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
