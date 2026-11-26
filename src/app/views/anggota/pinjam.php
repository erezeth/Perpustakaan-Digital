<?php
$aksiBawah = '<a class="btn btn--outline" href="' . e(url('/buku')) . '">Batal</a>'
    . '<button class="btn btn--primary" type="submit" form="formPinjam"' . ($sisaKuota === 0 || !$buku ? ' disabled' : '') . '>Ajukan Pinjam</button>';
?>
<div class="halaman-kepala">
    <a class="icon-btn" href="<?= e(url('/buku')) ?>" aria-label="Kembali"><?= ikon('arrow-left') ?></a>
    <h1 class="judul-xl">Ajukan Peminjaman</h1>
</div>

<div class="catatan">
    <?= ikon('info', 18) ?>
    <p>Maksimal <?= MAX_BUKU_AKTIF ?> buku aktif per anggota. Sisa kuota Anda: <strong><?= $sisaKuota ?> buku</strong>.</p>
</div>

<?php if (!$buku): ?>
    <div class="kartu kosong">
        <div class="kosong__ikon"><?= ikon('books', 24) ?></div>
        <h3>Tidak ada buku tersedia</h3>
        <p>Semua stok sedang dipinjam. Coba lagi nanti.</p>
    </div>
<?php elseif ($sisaKuota === 0): ?>
    <div class="kartu kosong">
        <div class="kosong__ikon"><?= ikon('warning', 24) ?></div>
        <h3>Kuota pinjam penuh</h3>
        <p>Kembalikan salah satu buku terlebih dahulu sebelum meminjam lagi.</p>
        <a class="btn btn--primary btn--sm" href="<?= e(url('/kembali')) ?>">Lihat pinjaman aktif</a>
    </div>
<?php else: ?>
    <form id="formPinjam" method="post" action="<?= e(url('/pinjam')) ?>" data-hari="<?= MAX_HARI_PINJAM ?>">
        <?= csrf_field() ?>

        <div class="field">
            <label for="tglPinjam">Tanggal pinjam</label>
            <div class="input">
                <?= ikon('calendar') ?>
                <input id="tglPinjam" type="date" name="tgl_pinjam" value="<?= e($tanggal) ?>" min="<?= e(date('Y-m-d')) ?>" required>
            </div>
        </div>

        <div class="tumpuk">
            <div class="bagian-kepala">
                <h2 class="judul-m">Buku dipilih</h2>
                <span class="keterangan redup" id="jumlahDipilih">0 buku</span>
            </div>

            <?php foreach ($buku as $b): ?>
                <label class="buku-pilih">
                    <input type="checkbox" name="buku[]" value="<?= (int) $b['id'] ?>" <?= $dipilih === (int) $b['id'] ? 'checked' : '' ?>>
                    <?= coverBuku($b, 'cover--sm') ?>
                    <span class="buku-pilih__info">
                        <span class="buku-pilih__judul"><?= e($b['judul']) ?></span>
                        <span class="buku-pilih__meta"><?= e($b['penulis']) ?> · stok <?= (int) $b['stok'] ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="ringkasan">
            <div class="ringkasan__baris"><span>Tanggal pinjam</span><strong id="tanggalPinjam"><?= e(tanggal($tanggal)) ?></strong></div>
            <div class="ringkasan__baris"><span>Batas pengembalian</span><strong id="estimasiKembali"><?= e(tanggal($jatuhTempo)) ?></strong></div>
            <div class="ringkasan__baris"><span>Jumlah buku</span><strong id="jumlahRingkas">0 buku</strong></div>
        </div>
    </form>

    <div class="catatan">
        <?= ikon('info', 18) ?>
        <p>Buku harus dikembalikan sebelum batas waktu. Keterlambatan dikenakan denda <?= rupiah(DENDA_TERLAMBAT_DEFAULT) ?> per hari.</p>
    </div>
<?php endif; ?>
