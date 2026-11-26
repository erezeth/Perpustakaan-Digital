<div class="halaman-kepala">
    <a class="icon-btn" href="<?= e(url('/beranda')) ?>" aria-label="Kembali"><?= ikon('arrow-left') ?></a>
    <h1 class="judul-xl">Denda Saya</h1>
</div>

<div class="ringkas-denda">
    <span>Total denda belum dibayar</span>
    <strong><?= rupiah($totalBelum) ?></strong>
    <small><?= (int) $jumlahBelum ?> denda · <?= (int) $jumlahLunas ?> sudah lunas</small>
</div>

<section class="tumpuk">
    <h2 class="judul-m">Rincian denda</h2>

    <?php if (!$daftar): ?>
        <div class="kartu kosong">
            <div class="kosong__ikon"><?= ikon('receipt', 24) ?></div>
            <h3>Tidak ada denda</h3>
            <p>Terima kasih sudah mengembalikan buku tepat waktu.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($daftar as $d): ?>
        <?php
        $keterangan = match ($d['jenis']) {
            'terlambat' => 'Terlambat ' . (int) $d['jumlah_hari'] . ' hari',
            'rusak' => 'Buku rusak',
            'hilang' => 'Buku hilang',
        };
        ?>
        <article class="baris-denda">
            <span class="baris-denda__ikon"><?= ikon('receipt', 18) ?></span>
            <div class="baris-denda__info">
                <div class="baris-denda__judul"><?= e($keterangan) ?></div>
                <div class="baris-denda__meta"><?= e($d['buku'] ?: '—') ?> · <?= e(tanggal($d['created_at'])) ?></div>
            </div>
            <div class="baris-denda__nominal">
                <strong><?= rupiah($d['nominal']) ?></strong>
                <?= badge($d['status_bayar'] === 'lunas' ? 'Lunas' : 'Belum Bayar', $d['status_bayar'] === 'lunas' ? 'netral' : 'bahaya') ?>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<div class="catatan">
    <?= ikon('info', 18) ?>
    <p>Pembayaran denda dilakukan langsung di meja petugas perpustakaan.</p>
</div>
