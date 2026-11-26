<?php
$edit = $anggota && isset($anggota['id']);
$nilai = static fn (string $k, string $d = '') => e($anggota[$k] ?? $d);
$galat = static fn (string $k) => isset($errors[$k]) ? '<span class="field__error">' . e($errors[$k]) . '</span>' : '';
?>
<form class="kartu form-card" method="post" action="<?= e($edit ? url('/admin/anggota/' . $anggota['id']) : url('/admin/anggota')) ?>">
    <?= csrf_field() ?>
    <div class="form-grid">
        <div class="field span-2">
            <label>Username</label>
            <div class="input"><input type="text" value="<?= e($edit ? $anggota['username'] : $username) ?>" readonly></div>
            <span class="form__bantuan"><?= $edit ? 'Username tidak dapat diubah.' : 'Username dibuat otomatis saat disimpan.' ?></span>
        </div>

        <div class="field span-2">
            <label for="nama">Nama</label>
            <div class="input <?= isset($errors['nama']) ? 'input--error' : '' ?>"><input id="nama" type="text" name="nama" value="<?= $nilai('nama') ?>" required></div>
            <?= $galat('nama') ?>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <div class="input <?= isset($errors['email']) ? 'input--error' : '' ?>"><input id="email" type="email" name="email" value="<?= $nilai('email') ?>" required></div>
            <?= $galat('email') ?>
        </div>

        <div class="field">
            <label for="no_telp">Telepon</label>
            <div class="input <?= isset($errors['no_telp']) ? 'input--error' : '' ?>"><input id="no_telp" type="text" name="no_telp" value="<?= $nilai('no_telp') ?>"></div>
            <?= $galat('no_telp') ?>
        </div>

        <div class="field span-2">
            <label for="alamat">Alamat</label>
            <div class="input <?= isset($errors['alamat']) ? 'input--error' : '' ?>"><input id="alamat" type="text" name="alamat" value="<?= $nilai('alamat') ?>"></div>
            <?= $galat('alamat') ?>
        </div>

        <div class="field">
            <label for="status">Status</label>
            <div class="input">
                <select id="status" name="status">
                    <option value="aktif" <?= ($anggota['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= ($anggota['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <?= $galat('status') ?>
        </div>

        <div class="field">
            <label for="password"><?= $edit ? 'Password baru' : 'Password' ?></label>
            <div class="input <?= isset($errors['password']) ? 'input--error' : '' ?>"><input id="password" type="password" name="password" autocomplete="new-password" <?= $edit ? '' : 'required' ?>></div>
            <span class="form__bantuan"><?= $edit ? 'Kosongkan jika tidak ingin mengubah password.' : 'Minimal 6 karakter.' ?></span>
            <?= $galat('password') ?>
        </div>
    </div>

    <div class="form-aksi">
        <button class="btn btn--primary" type="submit">Simpan</button>
        <a class="btn btn--outline" href="<?= e(url('/admin/anggota')) ?>">Batal</a>
    </div>
</form>
