-- Litera — data contoh. Jalankan SETELAH database/schema.sql.
-- Akun demo: petugas / petugas123 ; anggota A-0001..A-0003 / anggota123

USE litera;

INSERT INTO users (nama, username, email, password_hash, role, no_telp, alamat) VALUES
('Wulan Sari', 'petugas', 'wulan@litera.id', '$2y$10$C5oBprqHMBEcFPtiEL8wZ.iNfyiDtkBedV7qOUj2gUmVj2Kz7gHM6', 'petugas', '081200000001', 'Ruang Perpustakaan'),
('Putri Anggraini', 'A-0001', 'putri@sekolah.sch.id', '$2y$10$m4pbgHCXIx6LCOSoyjpODeKUkPsOPIWeYEf3XmVx/OgCHKt/GzpU.', 'anggota', '081234567801', 'Jl. Melati No. 4'),
('Raka Pratama', 'A-0002', 'raka@sekolah.sch.id', '$2y$10$m4pbgHCXIx6LCOSoyjpODeKUkPsOPIWeYEf3XmVx/OgCHKt/GzpU.', 'anggota', '081234567802', 'Jl. Kenanga No. 12'),
('Sinta Maharani', 'A-0003', 'sinta@sekolah.sch.id', '$2y$10$m4pbgHCXIx6LCOSoyjpODeKUkPsOPIWeYEf3XmVx/OgCHKt/GzpU.', 'anggota', '081234567803', 'Jl. Anggrek No. 9');

INSERT INTO kategori (nama_kategori) VALUES
('Fiksi'), ('Non-Fiksi'), ('Sejarah'), ('Biografi'), ('Pengembangan Diri');

INSERT INTO buku (kategori_id, judul, penulis, penerbit, tahun_terbit, isbn, sinopsis, stok) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, '9789793062797', 'Sepuluh anak Belitung berjuang mendapatkan pendidikan di tengah keterbatasan.', 3),
(1, 'Bumi', 'Tere Liye', 'Gramedia Pustaka Utama', 2010, '9786020331856', 'Perjalanan Matahari mencari ilmu, sahabat, dan jalan pulang.', 2),
(2, 'Sapiens', 'Yuval Noah Harari', 'Kepustakaan Populer Gramedia', 2011, '9786024243576', 'Riwayat singkat umat manusia, dari revolusi kognitif hingga revolusi sains.', 2),
(1, 'Negeri 5 Menara', 'A. Fuadi', 'Gramedia Pustaka Utama', 2009, '9789792248567', 'Enam santri dari penjuru Indonesia belajar, bermimpi, dan menemukan arti menara.', 0),
(5, 'Atomic Habits', 'James Clear', 'Gramedia Pustaka Utama', 2018, '9786020633179', 'Perubahan kecil yang memberi hasil luar biasa dalam kebiasaan sehari-hari.', 2),
(1, 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', 1980, '9789799731239', 'Kisah Minke dan Nyai Ontosoroh di akhir masa kolonial Hindia Belanda.', 3);

INSERT INTO denda_master (jenis, tipe_hitung, nominal, keterangan) VALUES
('terlambat', 'per_hari', 1000, 'Denda keterlambatan per hari'),
('rusak', 'tetap', 10000, 'Denda buku rusak'),
('hilang', 'tetap', 50000, 'Denda buku hilang');

-- Transaksi contoh: satu aktif, satu dikembalikan (terlambat), satu menunggu verifikasi.
INSERT INTO peminjaman (user_id, tgl_pinjam, tgl_jatuh_tempo, status) VALUES
(2, CURDATE() - INTERVAL 3 DAY, CURDATE() + INTERVAL 4 DAY, 'dipinjam'),
(3, CURDATE() - INTERVAL 12 DAY, CURDATE() - INTERVAL 5 DAY, 'dikembalikan'),
(4, CURDATE() - INTERVAL 5 DAY, CURDATE() + INTERVAL 2 DAY, 'menunggu_verifikasi');

UPDATE peminjaman SET tgl_kembali = CURDATE() - INTERVAL 2 DAY WHERE id = 2;

INSERT INTO detail_peminjaman (peminjaman_id, buku_id, kondisi) VALUES
(1, 1, 'baik'), (1, 3, 'baik'),
(2, 5, 'baik'),
(3, 2, 'baik');

UPDATE buku SET stok = stok - 1 WHERE id IN (1, 3, 2);

INSERT INTO denda (peminjaman_id, jenis, jumlah_hari, nominal, status_bayar) VALUES
(2, 'terlambat', 3, 3000, 'belum');
