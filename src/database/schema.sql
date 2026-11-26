-- Litera — skema database (MySQL/MariaDB, utf8mb4)
-- Import: mysql -u root < database/schema.sql
-- PERINGATAN: skrip ini menghapus database `litera` lalu membuatnya ulang.

DROP DATABASE IF EXISTS litera;
CREATE DATABASE litera CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE litera;

CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nama          VARCHAR(100) NOT NULL,
    username      VARCHAR(30)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('anggota', 'petugas') NOT NULL DEFAULT 'anggota',
    no_telp       VARCHAR(20)  NULL,
    alamat        VARCHAR(255) NULL,
    status        ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

CREATE TABLE kategori (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
) ENGINE = InnoDB;

CREATE TABLE buku (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id  INT NOT NULL,
    judul        VARCHAR(200) NOT NULL,
    penulis      VARCHAR(150) NOT NULL,
    penerbit     VARCHAR(150) NULL,
    tahun_terbit SMALLINT NULL,
    isbn         VARCHAR(30) NULL,
    sinopsis     TEXT NULL,
    cover        VARCHAR(255) NULL,
    stok         INT NOT NULL DEFAULT 0,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_buku_kategori FOREIGN KEY (kategori_id) REFERENCES kategori (id) ON DELETE RESTRICT,
    INDEX idx_buku_kategori (kategori_id)
) ENGINE = InnoDB;

CREATE TABLE peminjaman (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    petugas_id      INT NULL,
    tgl_pinjam      DATE NOT NULL,
    tgl_jatuh_tempo DATE NOT NULL,
    tgl_kembali     DATE NULL,
    status          ENUM('dipinjam', 'menunggu_verifikasi', 'dikembalikan') NOT NULL DEFAULT 'dipinjam',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_peminjaman_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT,
    CONSTRAINT fk_peminjaman_petugas FOREIGN KEY (petugas_id) REFERENCES users (id) ON DELETE RESTRICT,
    INDEX idx_peminjaman_user (user_id),
    INDEX idx_peminjaman_status (status)
) ENGINE = InnoDB;

CREATE TABLE detail_peminjaman (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    peminjaman_id INT NOT NULL,
    buku_id       INT NOT NULL,
    kondisi       ENUM('baik', 'rusak', 'hilang') NOT NULL DEFAULT 'baik',
    CONSTRAINT fk_detail_peminjaman FOREIGN KEY (peminjaman_id) REFERENCES peminjaman (id) ON DELETE RESTRICT,
    CONSTRAINT fk_detail_buku FOREIGN KEY (buku_id) REFERENCES buku (id) ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Tarif denda yang dikelola lewat menu Kelola Denda.
CREATE TABLE denda_master (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    jenis       ENUM('terlambat', 'rusak', 'hilang') NOT NULL UNIQUE,
    tipe_hitung ENUM('per_hari', 'tetap') NOT NULL,
    nominal     DECIMAL(10, 0) NOT NULL DEFAULT 0,
    keterangan  VARCHAR(255) NULL
) ENGINE = InnoDB;

CREATE TABLE denda (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    peminjaman_id INT NOT NULL,
    detail_id     INT NULL,
    jenis         ENUM('terlambat', 'rusak', 'hilang') NOT NULL,
    jumlah_hari   INT NULL,
    nominal       DECIMAL(10, 0) NOT NULL,
    status_bayar  ENUM('belum', 'lunas') NOT NULL DEFAULT 'belum',
    tgl_bayar     DATE NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_denda_peminjaman FOREIGN KEY (peminjaman_id) REFERENCES peminjaman (id) ON DELETE RESTRICT,
    CONSTRAINT fk_denda_detail FOREIGN KEY (detail_id) REFERENCES detail_peminjaman (id) ON DELETE RESTRICT
) ENGINE = InnoDB;
