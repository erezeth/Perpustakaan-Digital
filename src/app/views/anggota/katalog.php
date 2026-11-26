<?php
$tautanKategori = static function (int $id) use ($q): string {
    return url('/buku?' . http_build_query(array_filter(['q' => $q, 'kategori' => $id ?: null])));
};
?>
<div class="halaman-kepala">
    <a class="icon-btn" href="<?= e(url('/beranda')) ?>" aria-label="Kembali"><?= ikon('arrow-left') ?></a>
    <h1 class="judul-xl">Katalog Buku</h1>
</div>

<form class="input" action="<?= e(url('/buku')) ?>" role="search">
    <?= ikon('search') ?>
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari judul, penulis, atau ISBN" aria-label="Cari buku">
</form>

<div class="chips">
    <a class="chip <?= $kategoriId === 0 ? 'chip--aktif' : '' ?>" href="<?= e($tautanKategori(0)) ?>">Semua</a>
    <?php foreach ($kategori as $k): ?>
        <a class="chip <?= $kategoriId === (int) $k['id'] ? 'chip--aktif' : '' ?>" href="<?= e($tautanKategori((int) $k['id'])) ?>"><?= e($k['nama_kategori']) ?></a>
    <?php endforeach; ?>
</div>

<?php if (!$buku): ?>
    <div class="kartu kosong">
        <div class="kosong__ikon"><?= ikon('search', 24) ?></div>
        <h3>Buku tidak ditemukan</h3>
        <p>Coba kata kunci lain atau pilih kategori yang berbeda.</p>
    </div>
<?php else: ?>
    <div class="grid-buku">
        <?php foreach ($buku as $b): ?>
            <?php require dirname(__DIR__) . '/partials/buku_card.php'; ?>
        <?php endforeach; ?>
    </div>
    <?= $paginasi ?>
<?php endif; ?>
