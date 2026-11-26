<div class="toolbar">
    <p class="redup">Tarif dipakai otomatis saat petugas memproses pengembalian.</p>
    <?php if (count($daftar) < 3): ?>
        <a class="btn btn--primary" href="<?= e(url('/admin/denda/tambah')) ?>">Tambah Tarif</a>
    <?php endif; ?>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th>Tipe Hitung</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th class="kanan">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$daftar): ?>
                    <tr><td colspan="5" class="redup">Belum ada tarif denda.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $d): ?>
                    <tr>
                        <td><?= badge(ucfirst($d['jenis']), $d['jenis'] === 'terlambat' ? 'peringatan' : 'bahaya') ?></td>
                        <td class="redup"><?= $d['tipe_hitung'] === 'per_hari' ? 'Per hari' : 'Tetap' ?></td>
                        <td class="tabel__nominal"><?= rupiah($d['nominal']) ?></td>
                        <td class="redup"><?= e($d['keterangan'] ?: '—') ?></td>
                        <td>
                            <div class="tabel__aksi">
                                <a class="icon-btn" href="<?= e(url('/admin/denda/' . $d['id'] . '/edit')) ?>" aria-label="Edit tarif <?= e($d['jenis']) ?>"><?= ikon('pencil') ?></a>
                                <button class="icon-btn icon-btn--bahaya" type="button"
                                        data-hapus="Tarif denda <?= e($d['jenis']) ?>"
                                        data-aksi="<?= e(url('/admin/denda/' . $d['id'] . '/hapus')) ?>"
                                        aria-label="Hapus tarif <?= e($d['jenis']) ?>"><?= ikon('trash') ?></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
