<?php
$filterStatus = ['' => 'Semua', 'dipinjam' => 'Dipinjam', 'terlambat' => 'Terlambat', 'menunggu_verifikasi' => 'Menunggu verifikasi', 'dikembalikan' => 'Dikembalikan'];
?>
<div class="toolbar">
    <form class="input" action="<?= e(url('/admin/transaksi')) ?>" role="search">
        <?= ikon('search') ?>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari anggota atau buku" aria-label="Cari transaksi">
        <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
    </form>
    <a class="btn btn--primary" href="<?= e(url('/admin/transaksi/tambah')) ?>"><?= ikon('plus', 18) ?>Catat Peminjaman</a>
</div>

<div class="chips">
    <?php foreach ($filterStatus as $nilai => $teks): ?>
        <?php $query = array_filter(['q' => $q, 'status' => $nilai]); ?>
        <a class="chip <?= $status === $nilai ? 'chip--aktif' : '' ?>"
           href="<?= e(url('/admin/transaksi' . ($query ? '?' . http_build_query($query) : ''))) ?>"><?= e($teks) ?></a>
    <?php endforeach; ?>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th class="kanan">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$daftar): ?>
                    <tr><td colspan="6" class="redup">Belum ada transaksi yang cocok.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $t): ?>
                    <?php [$label, $jenis] = statusPeminjaman($t); ?>
                    <tr>
                        <td>
                            <div class="tabel__nama">
                                <span class="avatar avatar--netral" aria-hidden="true"><?= e(inisial($t['nama'])) ?></span>
                                <?= e($t['nama']) ?>
                            </div>
                        </td>
                        <td><?= e($t['buku'] ?: '—') ?></td>
                        <td class="redup"><?= e(tanggal($t['tgl_pinjam'])) ?></td>
                        <td class="redup"><?= e(tanggal($t['tgl_jatuh_tempo'])) ?></td>
                        <td><?= badge($label, $jenis) ?></td>
                        <td>
                            <div class="tabel__aksi">
                                <a class="btn btn--sm btn--outline" href="<?= e(url('/admin/transaksi/' . $t['id'])) ?>">Lihat</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $paginasi ?>
