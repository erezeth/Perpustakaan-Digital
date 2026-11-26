# Litera — Perpustakaan Digital

Aplikasi perpustakaan PHP native (tanpa framework) untuk anggota dan petugas.
Alur dan perilaku mengikuti `AGENTS.md`; arsitektur View → Controller → Model → Database (PDO).

## Kebutuhan

- PHP 8.1+ (diuji di PHP 8.2)
- MySQL/MariaDB (diuji di MySQL XAMPP)
- Apache dengan `mod_rewrite` (XAMPP/Laragon), atau `php -S` untuk pengembangan

## Instalasi

1. Salin proyek ke `htdocs` (mis. `C:\xampp\htdocs\Template-Perpustakaan-Reza`).
2. Start **Apache** dan **MySQL** di XAMPP.
3. Impor database (perintah di bawah **menghapus** database `litera` bila sudah ada):

   ```bat
   C:\xampp\mysql\bin\mysql.exe -u root < database\schema.sql
   C:\xampp\mysql\bin\mysql.exe -u root < database\seed.sql
   ```

4. Sesuaikan kredensial bila perlu:
   - Default: host `127.0.0.1`, database `litera`, user `root`, password kosong.
   - Ubah di `app/config/config.php`, atau buat `app/config/config.local.php` yang meng-override
     (file ini tidak perlu di-commit).
5. Buka `http://localhost/Template-Perpustakaan-Reza/`.

Aplikasi juga bisa dijalankan tanpa Apache:

```bat
php -S 127.0.0.1:8099 -t public public/index.php
```

lalu buka `http://127.0.0.1:8099/`.

## Akun demo

| Peran | Username | Password |
|---|---|---|
| Petugas | `petugas` | `petugas123` |
| Anggota | `A-0001` | `anggota123` |
| Anggota | `A-0002` | `anggota123` |
| Anggota | `A-0003` | `anggota123` |

Anggota baru dibuat oleh petugas lewat menu **Kelola Anggota**; username dibuat otomatis
(`A-0001`, `A-0002`, ...). Tidak ada registrasi mandiri.

## Menambah akun

- **Anggota**: login sebagai petugas → **Kelola Anggota** → **Tambah Anggota**. Isi nama, email,
  password (minimal 6 karakter), lalu simpan. Username otomatis (`A-0004`, dst.).
- **Petugas**: dari terminal di root proyek:

  ```bat
  php tools\buat-akun.php
  ```

  Jawab pertanyaan yang muncul (jenis akun, nama, username, email, password). Akun langsung aktif
  dan bisa dipakai login. Password terlihat saat diketik.

Petugas juga bisa mengganti password anggota lewat **Edit Anggota** (kosongkan password bila tidak
ingin mengubahnya).

## Aturan bisnis (dapat diubah di `app/config/config.php`)

- Maksimal **3** buku aktif per anggota (`MAX_BUKU_AKTIF`).
- Lama pinjam **7** hari (`MAX_HARI_PINJAM`).
- Denda keterlambatan **Rp1.000/hari** (`DENDA_TERLAMBAT_DEFAULT`); tarif rusak/hilang dikelola
  lewat menu **Kelola Denda**.
- Alur pengembalian: anggota mengajukan → status `menunggu_verifikasi` → petugas memeriksa kondisi
  dan menyelesaikan. Denda belum lunas **tidak** memblokir peminjaman baru.
- Buku berkondisi **hilang** tidak menambah stok kembali.

## Struktur

```
├─ index.php               penerus URL lama (root proyek)
├─ .htaccess               rewrite root → public/
├─ database/               schema.sql, seed.sql
├─ public/                 document root
│  ├─ index.php            front controller + tabel route
│  ├─ .htaccess
│  ├─ assets/css|js
│  └─ uploads/             cover buku
└─ app/
   ├─ config/              config.php, config.example.php
   ├─ core/                Router, Database, Controller, Auth, Csrf, Flash, Validator, helpers
   ├─ controllers/
   ├─ models/
   └─ views/               layouts, partials, auth, anggota, admin
```

## Keamanan

- Semua query memakai PDO prepared statement.
- Password di-hash (`password_hash`), sesi diperbarui saat login.
- CSRF token di semua form POST; request POST tanpa token ditolak (419).
- Guard role di server: anggota tidak bisa membuka `/admin`, petugas tidak bisa membuka halaman
  anggota; data anggota dibatasi pada `user_id` miliknya.
- Upload cover divalidasi MIME, ukuran (maks 2 MB), dan nama file diacak.
- Peminjaman/pengembalian berjalan dalam transaksi database dengan `SELECT ... FOR UPDATE`.

## Catatan pengembangan

- Halaman anggota mobile-first (±390px); dashboard petugas responsif (±1440px).
- Cetak laporan memakai halaman print-friendly + `window.print()` (tanpa library PDF).
- `database/schema.sql` bersifat destruktif (DROP/CREATE database `litera`).
