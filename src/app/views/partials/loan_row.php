<?php
/* Butuh: $p (baris peminjaman dengan 'buku' hasil GROUP_CONCAT) */
[$label, $jenis] = statusPeminjaman($p);
$sisa = sisaHari($p['tgl_jatuh_tempo']);
$meta = $p['buku'] ?: '—';
if ($p['status'] === 'dipinjam') {
    $meta .= $sisa >= 0 ? ' · sisa ' . $sisa . ' hari' : ' · terlambat ' . abs($sisa) . ' hari';
}
?>
<div class="baris-pinjam">
    <div class="thumb cover--sage"><?= ikon('book-open', 22) ?></div>
    <div class="baris-pinjam__info">
        <div class="baris-pinjam__judul"><?= e($meta) ?></div>
        <div class="baris-pinjam__meta">Jatuh tempo <?= e(tanggal($p['tgl_jatuh_tempo'])) ?></div>
    </div>
    <?= badge($label, $jenis) ?>
</div>
