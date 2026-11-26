<?= logo(url('/login')) ?>

<div>
    <h1>Selamat datang kembali</h1>
    <p class="redup">Masuk untuk menjelajah dan meminjam buku.</p>
</div>

<form class="form" method="post" action="<?= e(url('/login')) ?>">
    <?= csrf_field() ?>

    <div class="field">
        <label for="username">Username</label>
        <div class="input">
            <?= ikon('user') ?>
            <input id="username" type="text" name="username" placeholder="Username anggota atau petugas" autocomplete="username" required autofocus>
        </div>
    </div>

    <div class="field">
        <label for="sandi">Kata Sandi</label>
        <div class="input">
            <?= ikon('lock') ?>
            <input id="sandi" type="password" name="password" placeholder="Kata sandi" autocomplete="current-password" required>
        </div>
    </div>

    <button class="btn btn--primary btn--block" type="submit">Masuk</button>
</form>

<p class="masuk__bantuan">Belum punya akun? Hubungi petugas perpustakaan.</p>
