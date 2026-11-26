<?php
$edit = $denda && isset($denda['id']);
$galat = static fn (string $k) => isset($errors[$k]) ? '<span class="field__error">' . e($errors[$k]) . '</span>' : '';
?>
<form class="kartu form-card" method="post"
      action="<?= e($edit ? url('/admin/denda/' . $denda['id']) : url('/admin/denda')) ?>">
    <?= csrf_field() ?>
    <div class="form-grid">
        <div class="field">
            <label for="jenis">Jenis denda</label>
            <div class="input <?= isset($errors['jenis']) ? 'input--error' : '' ?>">
                <select id="jenis" name="jenis" required>
                    <?php foreach (['terlambat' => 'Terlambat', 'rusak' => 'Rusak', 'hilang' => 'Hilang'] as $nilai => $teks): ?>
                        <option value="<?= $nilai ?>" <?= ($denda['jenis'] ?? '') === $nilai ? 'selected' : '' ?>><?= $teks ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?= $galat('jenis') ?>
        </div>

        <div class="field">
            <label for="tipe_hitung">Tipe hitung</label>
            <div class="input <?= isset($errors['tipe_hitung']) ? 'input--error' : '' ?>">
                <select id="tipe_hitung" name="tipe_hitung" required>
                    <option value="per_hari" <?= ($denda['tipe_hitung'] ?? '') === 'per_hari' ? 'selected' : '' ?>>Per hari</option>
                    <option value="tetap" <?= ($denda['tipe_hitung'] ?? '') === 'tetap' ? 'selected' : '' ?>>Tetap</option>
                </select>
            </div>
            <?= $galat('tipe_hitung') ?>
        </div>

        <div class="field">
            <label for="nominal">Nominal (Rp)</label>
            <div class="input <?= isset($errors['nominal']) ? 'input--error' : '' ?>">
                <input id="nominal" type="number" name="nominal" min="0" value="<?= e($denda['nominal'] ?? '0') ?>" required>
            </div>
            <?= $galat('nominal') ?>
        </div>

        <div class="field span-2">
            <label for="keterangan">Keterangan</label>
            <div class="input <?= isset($errors['keterangan']) ? 'input--error' : '' ?>">
                <input id="keterangan" type="text" name="keterangan" value="<?= e($denda['keterangan'] ?? '') ?>">
            </div>
            <?= $galat('keterangan') ?>
        </div>
    </div>

    <div class="form-aksi">
        <button class="btn btn--primary" type="submit">Simpan</button>
        <a class="btn btn--outline" href="<?= e(url('/admin/denda')) ?>">Batal</a>
    </div>
</form>
