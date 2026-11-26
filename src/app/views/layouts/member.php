<?php
$flash = Flash::ambil();
$segmen = explode('/', trim(CURRENT_PATH, '/'))[0] ?? '';
$nav = [
    ['/beranda', 'Beranda', 'home'],
    ['/buku', 'Buku', 'books'],
    ['/riwayat', 'Riwayat', 'history'],
    ['/denda', 'Denda', 'receipt'],
    ['/profil', 'Profil', 'user'],
];
$aktif = match ($segmen) {
    'buku', 'pinjam' => '/buku',
    'kembali', 'riwayat' => '/riwayat',
    'denda' => '/denda',
    'profil' => '/profil',
    default => '/beranda',
};
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
<body class="member">
<div class="app <?= !empty($aksiBawah) ? 'app--aksi' : '' ?>">
    <main class="app-main">
        <?php require dirname(__DIR__) . '/partials/flash.php'; ?>
        <?php require $viewFile; ?>
    </main>

    <?php if (!empty($aksiBawah)): ?>
        <div class="aksi-bawah"><?= $aksiBawah ?></div>
    <?php elseif (empty($tanpaNav)): ?>
        <nav class="nav-bawah" aria-label="Navigasi utama">
            <?php foreach ($nav as [$url, $label, $namaIkon]): ?>
                <a class="<?= $aktif === $url ? 'aktif' : '' ?>" href="<?= e(url($url)) ?>"<?= $aktif === $url ? ' aria-current="page"' : '' ?>>
                    <?= ikon($namaIkon) ?><span><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
</div>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
