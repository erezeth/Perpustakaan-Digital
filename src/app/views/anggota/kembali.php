<div class="halaman-kepala">
    <a class="icon-btn" href="<?= e(url('/beranda')) ?>" aria-label="Kembali"><?= ikon('arrow-left') ?></a>
    <h1 class="judul-xl">Pengembalian</h1>
</div>

<div class="catatan">
    <?= ikon('info', 18) ?>
    <p>Setelah diajukan, serahkan buku ke petugas. Petugas akan memeriksa kondisi buku dan menghitung denda jika ada.</p>
</div>

<section class="tumpuk">
    <h2 class="judul-m">Pinjaman aktif</h2>

    <?php if (!$pinjaman): ?>
        <div class="kartu kosong">
            <div class="kosong__ikon"><?= ikon('swap', 24) ?></div>
            <h3>Tidak ada pinjaman aktif</h3>
            <p>Semua buku sudah dikembalikan.</p>
            <a class="btn btn--primary btn--sm" href="<?= e(url('/buku')) ?>">Pinjam buku</a>
        </div>
    <?php endif; ?>

    <?php foreach ($pinjaman as $p): ?>
        <?php [$label, $jenis] = statusPeminjaman($p); ?>
        <article class="kartu kartu--isi tumpuk">
            <div class="baris-pinjam" style="border:0;padding:0;background:transparent">
                <div class="thumb cover--sage"><?= ikon('book-open', 22) ?></div>
                <div class="baris-pinjam__info">
                    <div class="baris-pinjam__judul"><?= e($p['buku'] ?: '—') ?></div>
                    <div class="baris-pinjam__meta">Jatuh tempo <?= e(tanggal($p['tgl_jatuh_tempo'])) ?></div>
                </div>
                <?= badge($label, $jenis) ?>
            </div>

            <?php if ($p['status'] === 'dipinjam'): ?>
                <form method="post" action="<?= e(url('/kembali')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button class="btn btn--outline btn--block" type="submit">Ajukan Pengembalian</button>
                </form>
            <?php else: ?>
                <p class="teks-s redup">Menunggu petugas memeriksa buku.</p>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>
