<header class="sapaan">
    <div>
        <p class="keterangan redup">Selamat datang,</p>
        <h1><?= e($user['nama']) ?></h1>
    </div>
    <a class="avatar" href="<?= e(url('/profil')) ?>" aria-label="Buka profil"><?= e(inisial($user['nama'])) ?></a>
</header>

<form class="input" action="<?= e(url('/buku')) ?>" role="search">
    <?= ikon('search') ?>
    <input type="search" name="q" placeholder="Cari judul, penulis, atau ISBN" aria-label="Cari buku">
</form>

<div class="chips">
    <a class="chip chip--aktif" href="<?= e(url('/buku')) ?>">Semua</a>
    <?php foreach ($kategori as $k): ?>
        <a class="chip" href="<?= e(url('/buku?kategori=' . $k['id'])) ?>"><?= e($k['nama_kategori']) ?></a>
    <?php endforeach; ?>
</div>

<section class="tumpuk">
    <div class="bagian-kepala">
        <h2>Pinjaman Aktif</h2>
        <a href="<?= e(url('/riwayat')) ?>">Lihat semua</a>
    </div>

    <?php if (!$pinjaman): ?>
        <div class="kartu kosong">
            <div class="kosong__ikon"><?= ikon('books', 24) ?></div>
            <h3>Belum ada pinjaman</h3>
            <p>Jelajahi katalog dan pinjam buku pertamamu.</p>
            <a class="btn btn--primary btn--sm" href="<?= e(url('/buku')) ?>">Cari buku</a>
        </div>
    <?php else: ?>
        <?php foreach ($pinjaman as $p): ?>
            <?php require dirname(__DIR__) . '/partials/loan_row.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<section class="tumpuk">
    <div class="bagian-kepala">
        <h2>Rekomendasi untukmu</h2>
        <a href="<?= e(url('/buku')) ?>">Lihat semua</a>
    </div>
    <div class="grid-buku">
        <?php foreach ($buku as $b): ?>
            <?php require dirname(__DIR__) . '/partials/buku_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
