<?php
$edit = $kategori && isset($kategori['id']);
$galat = isset($errors['nama_kategori']) ? '<span class="field__error">' . e($errors['nama_kategori']) . '</span>' : '';
?>
<form class="kartu form-card form-card--kecil" method="post"
      action="<?= e($edit ? url('/admin/kategori/' . $kategori['id']) : url('/admin/kategori')) ?>">
    <?= csrf_field() ?>
    <div class="field">
        <label for="nama_kategori">Nama Kategori</label>
        <div class="input <?= isset($errors['nama_kategori']) ? 'input--error' : '' ?>">
            <input id="nama_kategori" type="text" name="nama_kategori" value="<?= e($kategori['nama_kategori'] ?? '') ?>" required autofocus>
        </div>
        <?= $galat ?>
    </div>

    <div class="form-aksi">
        <button class="btn btn--primary" type="submit">Simpan</button>
        <a class="btn btn--outline" href="<?= e(url('/admin/kategori')) ?>">Batal</a>
    </div>
</form>
