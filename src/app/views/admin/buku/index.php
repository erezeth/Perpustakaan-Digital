<div class="toolbar">
    <form class="input" action="<?= e(url('/admin/buku')) ?>" role="search">
        <?= ikon('search') ?>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari judul, penulis, atau ISBN" aria-label="Cari buku">
    </form>
    <a class="btn btn--primary" href="<?= e(url('/admin/buku/tambah')) ?>">Tambah Buku</a>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th class="kanan">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$daftar): ?>
                    <tr><td colspan="6" class="redup">Belum ada buku yang cocok.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $b): ?>
                    <tr>
                        <td>
                            <div class="tabel__nama">
                                <?= coverBuku($b, 'cover--sm') ?>
                                <?= e($b['judul']) ?>
                            </div>
                        </td>
                        <td class="redup"><?= e($b['penulis']) ?></td>
                        <td class="redup"><?= e($b['nama_kategori']) ?></td>
                        <td class="angka"><?= (int) $b['stok'] ?></td>
                        <td><?= badge((int) $b['stok'] > 0 ? 'Tersedia' : 'Habis', (int) $b['stok'] > 0 ? 'sukses' : 'bahaya') ?></td>
                        <td>
                            <div class="tabel__aksi">
                                <a class="icon-btn" href="<?= e(url('/admin/buku/' . $b['id'] . '/edit')) ?>" aria-label="Edit <?= e($b['judul']) ?>"><?= ikon('pencil') ?></a>
                                <button class="icon-btn icon-btn--bahaya" type="button"
                                        data-hapus="<?= e($b['judul']) ?>"
                                        data-aksi="<?= e(url('/admin/buku/' . $b['id'] . '/hapus')) ?>"
                                        aria-label="Hapus <?= e($b['judul']) ?>"><?= ikon('trash') ?></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $paginasi ?>
