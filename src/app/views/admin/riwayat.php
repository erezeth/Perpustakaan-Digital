<form class="filter-bar" method="get" action="<?= e(url('/admin/riwayat')) ?>">
    <div class="input" style="width:240px">
        <?= ikon('search') ?>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari anggota atau buku" aria-label="Cari riwayat">
    </div>
    <div class="input" style="width:190px">
        <select name="status" aria-label="Filter status">
            <?php foreach (['' => 'Semua status', 'dipinjam' => 'Dipinjam', 'terlambat' => 'Terlambat', 'menunggu_verifikasi' => 'Menunggu verifikasi', 'dikembalikan' => 'Dikembalikan'] as $nilai => $teks): ?>
                <option value="<?= $nilai ?>" <?= $status === $nilai ? 'selected' : '' ?>><?= e($teks) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="input" style="width:170px">
        <input type="month" name="periode" value="<?= e($periode) ?>" aria-label="Periode pinjam">
    </div>
    <div class="filter-bar__aksi">
        <button class="btn btn--outline" type="submit">Tampilkan</button>
        <a class="btn btn--ghost" href="<?= e(url('/admin/riwayat')) ?>">Reset</a>
    </div>
</form>

<div class="table-card">
    <div class="table-scroll">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$daftar): ?>
                    <tr><td colspan="6" class="redup">Tidak ada data riwayat yang cocok.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $r): ?>
                    <?php [$label, $jenis] = statusPeminjaman($r); ?>
                    <tr>
                        <td>
                            <div class="tabel__nama">
                                <span class="avatar avatar--netral" aria-hidden="true"><?= e(inisial($r['nama'])) ?></span>
                                <?= e($r['nama']) ?>
                            </div>
                        </td>
                        <td><?= e($r['buku'] ?: '—') ?></td>
                        <td class="redup"><?= e(tanggal($r['tgl_pinjam'])) ?></td>
                        <td class="redup"><?= e(tanggal($r['tgl_jatuh_tempo'])) ?></td>
                        <td class="redup"><?= e(tanggal($r['tgl_kembali'])) ?></td>
                        <td><?= badge($label, $jenis) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $paginasi ?>
