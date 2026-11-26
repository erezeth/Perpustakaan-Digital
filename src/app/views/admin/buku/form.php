<?php
$edit = $buku && isset($buku['id']);
$nilai = static fn (string $k, string $d = '') => e($buku[$k] ?? $d);
$galat = static fn (string $k) => isset($errors[$k]) ? '<span class="field__error">' . e($errors[$k]) . '</span>' : '';
?>
<form class="kartu form-card" method="post" enctype="multipart/form-data"
      action="<?= e($edit ? url('/admin/buku/' . $buku['id']) : url('/admin/buku')) ?>">
    <?= csrf_field() ?>
    <div class="form-grid">
        <div class="field span-2">
            <label for="judul">Judul</label>
            <div class="input <?= isset($errors['judul']) ? 'input--error' : '' ?>"><input id="judul" type="text" name="judul" value="<?= $nilai('judul') ?>" required></div>
            <?= $galat('judul') ?>
        </div>

        <div class="field span-2">
            <label for="penulis">Penulis</label>
            <div class="input <?= isset($errors['penulis']) ? 'input--error' : '' ?>"><input id="penulis" type="text" name="penulis" value="<?= $nilai('penulis') ?>" required></div>
            <?= $galat('penulis') ?>
        </div>

        <div class="field">
            <label for="kategori_id">Kategori</label>
            <div class="input <?= isset($errors['kategori_id']) ? 'input--error' : '' ?>">
                <select id="kategori_id" name="kategori_id" required>
                    <option value="">Pilih kategori</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= (int) $k['id'] ?>" <?= (int) ($buku['kategori_id'] ?? 0) === (int) $k['id'] ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?= $galat('kategori_id') ?>
        </div>

        <div class="field">
            <label for="tahun_terbit">Tahun terbit</label>
            <div class="input <?= isset($errors['tahun_terbit']) ? 'input--error' : '' ?>"><input id="tahun_terbit" type="number" name="tahun_terbit" value="<?= $nilai('tahun_terbit') ?>" min="1000" max="<?= (int) date('Y') + 1 ?>"></div>
            <?= $galat('tahun_terbit') ?>
        </div>

        <div class="field">
            <label for="penerbit">Penerbit</label>
            <div class="input <?= isset($errors['penerbit']) ? 'input--error' : '' ?>"><input id="penerbit" type="text" name="penerbit" value="<?= $nilai('penerbit') ?>"></div>
            <?= $galat('penerbit') ?>
        </div>

        <div class="field">
            <label for="isbn">ISBN</label>
            <div class="input <?= isset($errors['isbn']) ? 'input--error' : '' ?>"><input id="isbn" type="text" name="isbn" value="<?= $nilai('isbn') ?>"></div>
            <?= $galat('isbn') ?>
        </div>

        <div class="field">
            <label for="stok">Stok tersedia</label>
            <div class="input <?= isset($errors['stok']) ? 'input--error' : '' ?>"><input id="stok" type="number" name="stok" value="<?= $nilai('stok', '1') ?>" min="0" required></div>
            <?= $galat('stok') ?>
        </div>

        <div class="field">
            <label for="cover">Cover buku</label>
            <div class="input <?= isset($errors['cover']) ? 'input--error' : '' ?>"><input id="cover" type="file" name="cover" accept="image/jpeg,image/png,image/webp"></div>
            <span class="form__bantuan">JPG, PNG, atau WEBP. Maksimal 2 MB.<?= $edit && $buku['cover'] ? ' Kosongkan untuk mempertahankan cover sekarang.' : '' ?></span>
            <?= $galat('cover') ?>
        </div>

        <div class="field span-2">
            <label for="sinopsis">Sinopsis</label>
            <textarea class="textarea" id="sinopsis" name="sinopsis"><?= $nilai('sinopsis') ?></textarea>
        </div>
    </div>

    <?php if ($edit && $buku['cover']): ?>
        <div class="field">
            <span class="form__bantuan">Cover sekarang:</span>
            <img src="<?= e(UPLOAD_URL . '/' . $buku['cover']) ?>" alt="Cover <?= e($buku['judul']) ?>" style="width:120px;border-radius:10px">
        </div>
    <?php endif; ?>

    <div class="form-aksi">
        <button class="btn btn--primary" type="submit">Simpan</button>
        <a class="btn btn--outline" href="<?= e(url('/admin/buku')) ?>">Batal</a>
    </div>
</form>
