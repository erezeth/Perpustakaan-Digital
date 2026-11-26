<?php
[$label, $jenis] = statusPeminjaman($p);
$selesai = $p['status'] === 'dikembalikan';
$sisa = sisaHari($p['tgl_jatuh_tempo']);
$namaKondisi = ['baik' => 'Baik', 'rusak' => 'Rusak', 'hilang' => 'Hilang'];
$namaJenis = ['terlambat' => 'Terlambat', 'rusak' => 'Rusak', 'hilang' => 'Hilang'];
?>
<div class="toolbar">
    <a class="btn btn--outline" href="<?= e(url('/admin/transaksi')) ?>"><?= ikon('arrow-left', 18) ?>Kembali</a>
</div>

<div class="grid-2">
    <section class="kartu kartu--pad tumpuk">
        <div class="kartu-kepala">
            <div>
                <h2>PMJ-<?= str_pad((string) $p['id'], 4, '0', STR_PAD_LEFT) ?></h2>
                <p class="stat__sub">Dibuat <?= e(tanggal($p['created_at'])) ?></p>
            </div>
            <?= badge($label, $jenis) ?>
        </div>

        <dl>
            <div class="bukti__baris"><dt>Anggota</dt><dd><?= e($p['nama']) ?> (<?= e($p['username']) ?>)</dd></div>
            <div class="bukti__baris"><dt>Tanggal pinjam</dt><dd><?= e(tanggal($p['tgl_pinjam'])) ?></dd></div>
            <div class="bukti__baris"><dt>Jatuh tempo</dt><dd><?= e(tanggal($p['tgl_jatuh_tempo'])) ?></dd></div>
            <div class="bukti__baris"><dt>Tanggal kembali</dt><dd><?= e(tanggal($p['tgl_kembali'])) ?></dd></div>
        </dl>

        <div class="table-scroll">
            <table class="tabel">
                <thead><tr><th>Buku</th><th>Kondisi</th></tr></thead>
                <tbody>
                    <?php foreach ($detail as $d): ?>
                        <tr>
                            <td><?= e($d['judul']) ?></td>
                            <td><?= badge($namaKondisi[$d['kondisi']] ?? $d['kondisi'], $d['kondisi'] === 'baik' ? 'netral' : 'bahaya') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="kartu kartu--pad tumpuk">
        <h2 class="judul-m">Verifikasi Pengembalian</h2>

        <?php if ($selesai): ?>
            <div class="catatan">
                <?= ikon('check', 18) ?>
                <p>Transaksi sudah dikembalikan pada <?= e(tanggal($p['tgl_kembali'])) ?>.</p>
            </div>
        <?php else: ?>
            <?php if ($p['status'] === 'dipinjam' && $sisa < 0): ?>
                <div class="catatan">
                    <?= ikon('warning', 18) ?>
                    <p>Terlambat <?= abs($sisa) ?> hari. Denda keterlambatan dihitung otomatis saat diproses.</p>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(url('/admin/transaksi/' . $p['id'] . '/kembali')) ?>" class="tumpuk">
                <?= csrf_field() ?>
                <?php foreach ($detail as $d): ?>
                    <div class="field">
                        <label for="kondisi-<?= (int) $d['id'] ?>"><?= e($d['judul']) ?></label>
                        <div class="input">
                            <select id="kondisi-<?= (int) $d['id'] ?>" name="kondisi[<?= (int) $d['id'] ?>]">
                                <?php foreach ($namaKondisi as $nilai => $teks): ?>
                                    <option value="<?= $nilai ?>"><?= $teks ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php endforeach; ?>

                <button class="btn btn--primary btn--block" type="submit">Selesaikan Pengembalian</button>
            </form>
            <p class="form__bantuan">Buku hilang tidak menambah stok kembali. Pastikan kondisi sudah diperiksa.</p>
        <?php endif; ?>
    </section>
</div>

<section class="kartu kartu--pad tumpuk">
    <h2 class="judul-m">Denda Transaksi</h2>

    <?php if (!$denda): ?>
        <p class="redup">Tidak ada denda pada transaksi ini.</p>
    <?php else: ?>
        <div class="table-scroll">
            <table class="tabel">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Buku</th>
                        <th>Hari</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th class="kanan">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($denda as $dn): ?>
                        <tr>
                            <td><?= e($namaJenis[$dn['jenis']] ?? $dn['jenis']) ?></td>
                            <td class="redup"><?= e($dn['judul'] ?: '—') ?></td>
                            <td class="angka"><?= $dn['jumlah_hari'] !== null ? (int) $dn['jumlah_hari'] : '—' ?></td>
                            <td class="tabel__nominal"><?= rupiah($dn['nominal']) ?></td>
                            <td><?= badge($dn['status_bayar'] === 'lunas' ? 'Lunas' : 'Belum Bayar', $dn['status_bayar'] === 'lunas' ? 'netral' : 'bahaya') ?></td>
                            <td>
                                <div class="tabel__aksi">
                                    <?php if ($dn['status_bayar'] === 'belum'): ?>
                                        <form method="post" action="<?= e(url('/admin/denda/' . $dn['id'] . '/lunas')) ?>">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="peminjaman_id" value="<?= (int) $p['id'] ?>">
                                            <button class="btn btn--sm btn--outline" type="submit">Tandai Lunas</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="redup"><?= e(tanggal($dn['tgl_bayar'])) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
