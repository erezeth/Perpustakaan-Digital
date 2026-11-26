<?php /* Butuh: $b */ ?>
<a class="kartu-buku" href="<?= e(url('/buku/' . $b['id'])) ?>">
    <?= coverBuku($b) ?>
    <div class="kartu-buku__judul"><?= e($b['judul']) ?></div>
    <div class="kartu-buku__penulis"><?= e($b['penulis']) ?></div>
    <?php if ((int) $b['stok'] > 1): ?>
        <?= badge('Tersedia', 'sukses') ?>
    <?php elseif ((int) $b['stok'] === 1): ?>
        <?= badge('Stok 1', 'peringatan') ?>
    <?php else: ?>
        <?= badge('Habis', 'bahaya') ?>
    <?php endif; ?>
</a>
