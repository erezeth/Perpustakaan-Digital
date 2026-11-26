<form class="kartu form-card" method="post" action="<?= e(url('/admin/transaksi')) ?>" id="formPinjam" data-hari="<?= MAX_HARI_PINJAM ?>">
    <?= csrf_field() ?>
    <div class="form-grid">
        <div class="field span-2">
            <label for="user_id">Anggota</label>
            <div class="input">
                <select id="user_id" name="user_id" required>
                    <option value="">Pilih anggota</option>
                    <?php foreach ($anggota as $a): ?>
                        <option value="<?= (int) $a['id'] ?>"><?= e($a['nama']) ?> (<?= e($a['username']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="field">
            <label for="tgl_pinjam">Tanggal pinjam</label>
            <div class="input">
                <?= ikon('calendar') ?>
                <input id="tgl_pinjam" type="date" name="tgl_pinjam" value="<?= e($tanggal) ?>" min="<?= e(date('Y-m-d')) ?>" required>
            </div>
        </div>

        <div class="field">
            <label>Batas pengembalian</label>
            <div class="input"><input type="text" id="estimasiKembali" value="<?= e(tanggal($jatuhTempo)) ?>" readonly></div>
            <span class="form__bantuan">Otomatis <?= MAX_HARI_PINJAM ?> hari dari tanggal pinjam.</span>
        </div>

        <div class="field span-2">
            <label>Buku (maksimal <?= MAX_BUKU_AKTIF ?> buku)</label>
            <?php if (!$buku): ?>
                <p class="redup">Tidak ada buku dengan stok tersedia.</p>
            <?php endif; ?>
            <?php foreach ($buku as $b): ?>
                <label class="buku-pilih">
                    <input type="checkbox" name="buku[]" value="<?= (int) $b['id'] ?>">
                    <?= coverBuku($b, 'cover--sm') ?>
                    <span class="buku-pilih__info">
                        <span class="buku-pilih__judul"><?= e($b['judul']) ?></span>
                        <span class="buku-pilih__meta"><?= e($b['penulis']) ?> · stok <?= (int) $b['stok'] ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>

        <p class="form__bantuan span-2">Stok berkurang otomatis setelah transaksi disimpan.</p>
    </div>

    <div class="form-aksi">
        <button class="btn btn--primary" type="submit">Simpan Peminjaman</button>
        <a class="btn btn--outline" href="<?= e(url('/admin/transaksi')) ?>">Batal</a>
    </div>
</form>
