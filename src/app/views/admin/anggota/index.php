<div class="toolbar">
    <form class="input" action="<?= e(url('/admin/anggota')) ?>" role="search">
        <?= ikon('search') ?>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari nama, username, atau email" aria-label="Cari anggota">
    </form>
    <a class="btn btn--primary" href="<?= e(url('/admin/anggota/tambah')) ?>">Tambah Anggota</a>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="kanan">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$daftar): ?>
                    <tr><td colspan="5" class="redup">Belum ada anggota yang cocok.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $a): ?>
                    <tr>
                        <td>
                            <div class="tabel__nama">
                                <span class="avatar avatar--netral" aria-hidden="true"><?= e(inisial($a['nama'])) ?></span>
                                <?= e($a['nama']) ?>
                            </div>
                        </td>
                        <td class="angka"><?= e($a['username']) ?></td>
                        <td><?= e($a['email']) ?></td>
                        <td><?= badge(ucfirst($a['status']), $a['status'] === 'aktif' ? 'sukses' : 'netral') ?></td>
                        <td>
                            <div class="tabel__aksi">
                                <a class="icon-btn" href="<?= e(url('/admin/anggota/' . $a['id'] . '/edit')) ?>" aria-label="Edit <?= e($a['nama']) ?>"><?= ikon('pencil') ?></a>
                                <button class="icon-btn icon-btn--bahaya" type="button"
                                        data-hapus="<?= e($a['nama']) ?>"
                                        data-aksi="<?= e(url('/admin/anggota/' . $a['id'] . '/hapus')) ?>"
                                        aria-label="Hapus <?= e($a['nama']) ?>"><?= ikon('trash') ?></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $paginasi ?>
