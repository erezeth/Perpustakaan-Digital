<div class="toolbar">
    <p class="redup">Kategori dipakai untuk mengelompokkan koleksi buku.</p>
    <a class="btn btn--primary" href="<?= e(url('/admin/kategori/tambah')) ?>">Tambah Kategori</a>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Jumlah Buku</th>
                    <th class="kanan">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$daftar): ?>
                    <tr><td colspan="3" class="redup">Belum ada kategori.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $k): ?>
                    <tr>
                        <td><?= e($k['nama_kategori']) ?></td>
                        <td class="angka"><?= (int) $k['jumlah_buku'] ?></td>
                        <td>
                            <div class="tabel__aksi">
                                <a class="icon-btn" href="<?= e(url('/admin/kategori/' . $k['id'] . '/edit')) ?>" aria-label="Edit <?= e($k['nama_kategori']) ?>"><?= ikon('pencil') ?></a>
                                <button class="icon-btn icon-btn--bahaya" type="button"
                                        data-hapus="<?= e($k['nama_kategori']) ?>"
                                        data-aksi="<?= e(url('/admin/kategori/' . $k['id'] . '/hapus')) ?>"
                                        aria-label="Hapus <?= e($k['nama_kategori']) ?>"><?= ikon('trash') ?></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
