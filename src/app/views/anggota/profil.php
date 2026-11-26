<div class="halaman-kepala">
    <a class="icon-btn" href="<?= e(url('/beranda')) ?>" aria-label="Kembali"><?= ikon('arrow-left') ?></a>
    <h1 class="judul-xl">Profil</h1>
</div>

<div class="kartu kartu--isi tumpuk">
    <div class="profil-kepala">
        <span class="avatar avatar--lg" aria-hidden="true"><?= e(inisial($user['nama'])) ?></span>
        <div>
            <h1><?= e($user['nama']) ?></h1>
            <p>Anggota · <?= e($user['username']) ?></p>
        </div>
    </div>

    <dl>
        <div class="detail-baris"><dt>Email</dt><dd><?= e($user['email']) ?></dd></div>
        <div class="detail-baris"><dt>No. telepon</dt><dd><?= e($user['no_telp'] ?: '—') ?></dd></div>
        <div class="detail-baris"><dt>Alamat</dt><dd><?= e($user['alamat'] ?: '—') ?></dd></div>
        <div class="detail-baris"><dt>Status</dt><dd><?= e(ucfirst($user['status'])) ?></dd></div>
    </dl>

    <div class="stat-grid">
        <div class="stat-kecil"><strong><?= (int) $dipinjam ?></strong><span>Buku dipinjam</span></div>
        <div class="stat-kecil"><strong><?= (int) $selesai ?></strong><span>Selesai</span></div>
        <div class="stat-kecil stat-kecil--bahaya"><strong><?= rupiah($denda) ?></strong><span>Denda</span></div>
    </div>
</div>

<nav class="kartu menu" aria-label="Menu profil">
    <a href="<?= e(url('/riwayat')) ?>"><?= ikon('history') ?><span>Riwayat peminjaman</span><?= ikon('chevron-right', 18) ?></a>
    <a href="<?= e(url('/denda')) ?>"><?= ikon('receipt') ?><span>Denda saya</span><?= ikon('chevron-right', 18) ?></a>
    <a href="<?= e(url('/kembali')) ?>"><?= ikon('swap') ?><span>Ajukan pengembalian</span><?= ikon('chevron-right', 18) ?></a>
    <form method="post" action="<?= e(url('/logout')) ?>">
        <?= csrf_field() ?>
        <button class="danger" type="submit"><?= ikon('logout') ?><span>Keluar</span></button>
    </form>
</nav>
