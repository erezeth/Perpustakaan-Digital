<?php
$filterStatus = [
    '' => 'Semua',
    'dipinjam' => 'Dipinjam',
    'menunggu_verifikasi' => 'Menunggu verifikasi',
    'terlambat' => 'Terlambat',
    'dikembalikan' => 'Dikembalikan',
];
?>
<div class="halaman-kepala">
    <a class="icon-btn" href="<?= e(url('/beranda')) ?>" aria-label="Kembali"><?= ikon('arrow-left') ?></a>
    <h1 class="judul-xl">Riwayat</h1>
</div>

<div class="chips">
    <?php foreach ($filterStatus as $nilai => $teks): ?>
        <a class="chip <?= $status === $nilai ? 'chip--aktif' : '' ?>"
           href="<?= e(url('/riwayat' . ($nilai !== '' ? '?status=' . $nilai : ''))) ?>"><?= e($teks) ?></a>
    <?php endforeach; ?>
</div>

<?php if (!$daftar): ?>
    <div class="kartu kosong">
        <div class="kosong__ikon"><?= ikon('history', 24) ?></div>
        <h3>Belum ada riwayat</h3>
        <p>Belum ada peminjaman dengan status ini.</p>
    </div>
<?php else: ?>
    <div class="tumpuk">
        <?php foreach ($daftar as $p): ?>
            <?php [$label, $jenis] = statusPeminjaman($p); ?>
            <article class="baris-pinjam">
                <div class="thumb cover--sage"><?= ikon('book-open', 22) ?></div>
                <div class="baris-pinjam__info">
                    <div class="baris-pinjam__judul"><?= e($p['buku'] ?: '—') ?></div>
                    <div class="baris-pinjam__meta">Pinjam <?= e(tanggal($p['tgl_pinjam'])) ?> · Batas <?= e(tanggal($p['tgl_jatuh_tempo'])) ?></div>
                    <?php if ($p['tgl_kembali']): ?>
                        <div class="baris-pinjam__meta">Kembali <?= e(tanggal($p['tgl_kembali'])) ?></div>
                    <?php endif; ?>
                </div>
                <?= badge($label, $jenis) ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
