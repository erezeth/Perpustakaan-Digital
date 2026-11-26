<?php
[$label, $jenis] = statusPeminjaman($p);
$aksiBawah = '<a class="btn btn--outline" href="' . e(url('/riwayat')) . '">Lihat Riwayat</a>'
    . '<a class="btn btn--primary" href="' . e(url('/beranda')) . '">Selesai</a>';
?>
<div class="sukses">
    <div class="sukses__ikon"><?= ikon('check', 32) ?></div>
    <h1 class="judul-l">Peminjaman Berhasil</h1>
    <p class="teks-s redup">Tunjukkan bukti ini ke petugas untuk mengambil buku.</p>
</div>

<div class="kartu bukti">
    <div class="bukti__kepala">
        <div>
            <p class="keterangan redup">Nomor peminjaman</p>
            <strong>PMJ-<?= str_pad((string) $p['id'], 4, '0', STR_PAD_LEFT) ?></strong>
        </div>
        <?= badge($label, $jenis) ?>
    </div>

    <div class="bukti__divider"></div>

    <dl>
        <div class="bukti__baris"><dt>Anggota</dt><dd><?= e($p['nama']) ?> (<?= e($p['username']) ?>)</dd></div>
        <div class="bukti__baris"><dt>Tanggal pinjam</dt><dd><?= e(tanggal($p['tgl_pinjam'])) ?></dd></div>
        <div class="bukti__baris"><dt>Batas kembali</dt><dd><?= e(tanggal($p['tgl_jatuh_tempo'])) ?></dd></div>
    </dl>

    <div class="bukti__divider"></div>

    <?php foreach ($detail as $d): ?>
        <div class="bukti__buku">
            <?= coverBuku($d, 'cover--sm') ?>
            <div>
                <div class="baris-pinjam__judul"><?= e($d['judul']) ?></div>
                <div class="baris-pinjam__meta"><?= e($d['penulis']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="catatan">
    <?= ikon('clock', 18) ?>
    <p>Kembalikan paling lambat <strong><?= e(tanggal($p['tgl_jatuh_tempo'])) ?></strong>. Denda keterlambatan <?= rupiah(DENDA_TERLAMBAT_DEFAULT) ?> per hari.</p>
</div>
