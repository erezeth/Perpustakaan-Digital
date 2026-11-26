<?php
$sisaKuota = max(0, MAX_BUKU_AKTIF - $pinjamanAktif);
$bisaPinjam = (int) $buku['stok'] > 0 && $sisaKuota > 0;

$aksiBawah = '<a class="btn btn--outline" href="' . e(url('/buku')) . '">Kembali</a>';
if ($bisaPinjam) {
    $aksiBawah .= '<a class="btn btn--primary" href="' . e(url('/pinjam?buku=' . $buku['id'])) . '">Pinjam Buku</a>';
}
?>
<div class="halaman-kepala">
    <a class="icon-btn" href="<?= e(url('/buku')) ?>" aria-label="Kembali ke katalog"><?= ikon('arrow-left') ?></a>
</div>

<?= coverBuku($buku, 'detail-cover') ?>

<div>
    <h1 class="judul-xl"><?= e($buku['judul']) ?></h1>
    <p class="redup"><?= e($buku['penulis']) ?></p>
</div>

<div class="detail-meta">
    <span><?= e($buku['nama_kategori']) ?></span>·<span><?= e($buku['tahun_terbit'] ?: '—') ?></span>
    <?php if ($buku['penerbit']): ?>·<span><?= e($buku['penerbit']) ?></span><?php endif; ?>
</div>

<div class="detail-stok">
    <div>
        <strong>Tersedia <?= (int) $buku['stok'] ?> buku</strong>
        <span>
            <?php if ((int) $buku['stok'] === 0): ?>
                Stok sedang habis.
            <?php elseif ($sisaKuota === 0): ?>
                Kuota pinjam Anda sudah penuh (<?= MAX_BUKU_AKTIF ?> buku).
            <?php else: ?>
                Anda bisa meminjam <?= $sisaKuota ?> buku lagi.
            <?php endif; ?>
        </span>
    </div>
    <?= badge((int) $buku['stok'] > 0 ? 'Tersedia' : 'Habis', (int) $buku['stok'] > 0 ? 'sukses' : 'bahaya') ?>
</div>

<?php if ($buku['sinopsis']): ?>
    <div>
        <h2 class="judul-m">Sinopsis</h2>
        <p class="teks-s redup"><?= nl2br(e($buku['sinopsis'])) ?></p>
    </div>
<?php endif; ?>

<dl>
    <div class="detail-baris"><dt>Penerbit</dt><dd><?= e($buku['penerbit'] ?: '—') ?></dd></div>
    <div class="detail-baris"><dt>Tahun terbit</dt><dd><?= e($buku['tahun_terbit'] ?: '—') ?></dd></div>
    <div class="detail-baris"><dt>ISBN</dt><dd><?= e($buku['isbn'] ?: '—') ?></dd></div>
    <div class="detail-baris"><dt>Lama pinjam</dt><dd><?= MAX_HARI_PINJAM ?> hari</dd></div>
</dl>
