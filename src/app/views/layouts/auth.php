<?php
$flash = Flash::ambil();
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
<div class="app app--masuk">
    <main class="masuk">
        <?php require dirname(__DIR__) . '/partials/flash.php'; ?>
        <?php require $viewFile; ?>
    </main>
</div>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
