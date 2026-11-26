<section class="stats">
    <article class="kartu stat">
        <span class="stat__label">Anggota Aktif</span>
        <strong class="stat__nilai"><?= (int) $anggotaAktif ?></strong>
        <span class="stat__sub">Terdaftar aktif</span>
    </article>
    <article class="kartu stat">
        <span class="stat__label">Koleksi Buku</span>
        <strong class="stat__nilai"><?= (int) $koleksi ?></strong>
        <span class="stat__sub">Judul di katalog</span>
    </article>
    <article class="kartu stat">
        <span class="stat__label">Pinjaman Aktif</span>
        <strong class="stat__nilai"><?= (int) $aktif ?></strong>
        <span class="stat__sub"><?= (int) $terlambat ?> terlambat</span>
    </article>
    <article class="kartu stat">
        <span class="stat__label">Denda Belum Dibayar</span>
        <strong class="stat__nilai stat__nilai--bahaya"><?= rupiah($dendaBelum) ?></strong>
        <span class="stat__sub"><?= (int) $jumlahDenda ?> catatan</span>
    </article>
</section>

<section class="kartu kartu--pad">
    <div class="kartu-kepala">
        <div>
            <h2>Transaksi Terbaru</h2>
            <p class="stat__sub">Lima transaksi terakhir</p>
        </div>
        <a href="<?= e(url('/admin/transaksi')) ?>">Lihat Semua</a>
    </div>

    <?php if (!$terbaru): ?>
        <div class="kosong">
            <div class="kosong__ikon"><?= ikon('swap', 24) ?></div>
            <h3>Belum ada transaksi</h3>
            <p>Transaksi peminjaman akan muncul di sini.</p>
        </div>
    <?php else: ?>
        <div class="daftar-transaksi">
            <?php foreach ($terbaru as $p): ?>
                <?php [$label, $jenis] = statusPeminjaman($p); ?>
                <div class="daftar-transaksi__baris">
                    <div class="daftar-transaksi__info">
                        <div class="daftar-transaksi__nama"><?= e($p['nama']) ?></div>
                        <div class="daftar-transaksi__meta"><?= e($p['buku'] ?: '—') ?> · <?= e(tanggal($p['tgl_pinjam'])) ?></div>
                    </div>
                    <?= badge($label, $jenis) ?>
                    <a class="btn btn--sm btn--outline" href="<?= e(url('/admin/transaksi/' . $p['id'])) ?>">Detail</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
