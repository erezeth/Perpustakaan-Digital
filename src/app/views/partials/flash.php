<?php if ($flash): ?>
    <div class="flash flash--<?= e($flash['jenis']) ?>" role="status">
        <?= ikon($flash['jenis'] === 'sukses' ? 'check' : 'warning', 18) ?>
        <span><?= e($flash['pesan']) ?></span>
    </div>
<?php endif; ?>
