<?php
$namaJenis = [
    'peminjaman' => 'Laporan Peminjaman',
    'pengembalian' => 'Laporan Pengembalian',
    'denda' => 'Laporan Denda',
    'stok' => 'Laporan Stok Buku',
];
$namaStatus = ['terlambat' => 'Terlambat', 'rusak' => 'Rusak', 'hilang' => 'Hilang'];
?>
<form class="filter-bar no-print" method="get" action="<?= e(url('/admin/laporan')) ?>">
    <div class="input" style="width:230px">
        <select name="jenis" aria-label="Jenis laporan">
            <?php foreach ($namaJenis as $nilai => $teks): ?>
                <option value="<?= $nilai ?>" <?= $jenis === $nilai ? 'selected' : '' ?>><?= e($teks) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($jenis !== 'stok'): ?>
        <div class="input" style="width:180px">
            <input type="month" name="periode" value="<?= e($periode) ?>" aria-label="Periode">
        </div>
    <?php endif; ?>
    <div class="filter-bar__aksi">
        <button class="btn btn--outline" type="submit">Tampilkan</button>
        <?php if ($baris): ?>
            <button class="btn btn--primary" type="button" onclick="window.print()"><?= ikon('download', 18) ?>Cetak</button>
        <?php endif; ?>
    </div>
</form>

<section class="stats stats--3">
    <?php foreach ($ringkasan as $r): ?>
        <article class="kartu stat">
            <span class="stat__label"><?= e($r['label']) ?></span>
            <strong class="stat__nilai"><?= e($r['nilai']) ?></strong>
            <span class="stat__sub"><?= e($r['sub']) ?></span>
        </article>
    <?php endforeach; ?>
</section>

<?php if (!$baris): ?>
    <div class="kartu kosong">
        <div class="kosong__ikon"><?= ikon('search', 24) ?></div>
        <h3>Data tidak ditemukan</h3>
        <p>Coba pilih periode atau jenis laporan lain.</p>
    </div>
<?php else: ?>
    <div class="table-card">
        <div class="table-scroll">
            <table class="tabel">
                <?php if ($jenis === 'peminjaman' || $jenis === 'pengembalian'): ?>
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
                        <?php foreach ($baris as $r): ?>
                            <?php [$label, $warna] = statusPeminjaman($r); ?>
                            <tr>
                                <td><?= e($r['nama']) ?></td>
                                <td><?= e($r['buku'] ?: '—') ?></td>
                                <td class="redup"><?= e(tanggal($r['tgl_pinjam'])) ?></td>
                                <td class="redup"><?= e(tanggal($r['tgl_jatuh_tempo'])) ?></td>
                                <td class="redup"><?= e(tanggal($r['tgl_kembali'])) ?></td>
                                <td><?= badge($label, $warna) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php elseif ($jenis === 'denda'): ?>
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Jenis</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($baris as $d): ?>
                            <tr>
                                <td><?= e($d['nama']) ?></td>
                                <td><?= e($d['buku'] ?: '—') ?></td>
                                <td><?= e($namaStatus[$d['jenis']] ?? $d['jenis']) ?><?= $d['jumlah_hari'] !== null ? ' (' . (int) $d['jumlah_hari'] . ' hari)' : '' ?></td>
                                <td class="tabel__nominal"><?= rupiah($d['nominal']) ?></td>
                                <td><?= badge($d['status_bayar'] === 'lunas' ? 'Lunas' : 'Belum Bayar', $d['status_bayar'] === 'lunas' ? 'netral' : 'bahaya') ?></td>
                                <td class="redup"><?= e(tanggal($d['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php else: ?>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Penulis</th>
                            <th>Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($baris as $b): ?>
                            <tr>
                                <td><?= e($b['judul']) ?></td>
                                <td class="redup"><?= e($b['nama_kategori']) ?></td>
                                <td class="redup"><?= e($b['penulis']) ?></td>
                                <td class="angka"><?= (int) $b['stok'] ?></td>
                                <td><?= badge((int) $b['stok'] > 0 ? 'Tersedia' : 'Habis', (int) $b['stok'] > 0 ? 'sukses' : 'bahaya') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php endif; ?>
            </table>
        </div>
    </div>
<?php endif; ?>
