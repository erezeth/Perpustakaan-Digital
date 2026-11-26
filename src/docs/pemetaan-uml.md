# Pemetaan nama method UML → implementasi

Nama pada sequence diagram (`AGENTS.md` §5) dipetakan ke kode nyata berikut.
Beberapa langkah digabung dalam satu method karena berjalan dalam satu transaksi database.

| Method UML | Implementasi |
|---|---|
| `kirimData()` | form login → `AuthController::login()` |
| `cariUser(username)` | `User::cariUser()` |
| `verifikasiPassword()` | `Auth::verifikasiPassword()` |
| `loginBerhasil(role)` | `Auth::loginBerhasil()` |
| `loginGagal()` | `AuthController::loginGagal()` |
| `logout()` | `Auth::logout()` |
| `hapusSesi()` | `Auth::hapusSesi()` |
| `ajukanPinjam()` | `Peminjaman::ajukanPinjam()` |
| `cekStok(idBuku)` | di dalam `Peminjaman::ajukanPinjam()` — `SELECT ... FOR UPDATE` + cek `stok` |
| `cekBatasPinjam()` | `Peminjaman::hitungBukuAktif()` dipanggil di `ajukanPinjam()` |
| `simpanTransaksi()` | di dalam `ajukanPinjam()` — INSERT `peminjaman` + `detail_peminjaman` |
| `kurangiStok()` | di dalam `ajukanPinjam()` — UPDATE `buku.stok` |
| `pinjamGagal()` | `PeminjamanController::store()` / `adminStore()` → flash + redirect |
| `prosesKembali()` | `Peminjaman::prosesKembali()` |
| `ambilTransaksi(id)` | `Peminjaman::ambilTransaksi()` |
| `ambilTransaksi(periode)` | `Peminjaman::periode()`, `Denda::periode()`, `Buku::cari()` |
| `hitungDenda()` | `Peminjaman::hitungDenda()` + `Denda::tarif()` |
| `simpanDenda()` | INSERT `denda` di dalam `Peminjaman::prosesKembali()` |
| `updateStatus()` | UPDATE `peminjaman` di dalam `prosesKembali()` |
| `tambahStok()` | UPDATE `buku.stok` di dalam `prosesKembali()` |
| `getData()` | `*Controller::index()` + `Model::cari()/all()` |
| `simpanData()` | `*Controller::store()` / `update()` |
| `validasi()` | `Validator` + method privat `validasi*()` di tiap controller |
| `hapusData(id)` | `*Controller::destroy()` |
| `getRiwayat(user, role)` | `RiwayatController::anggota()` / `admin()` + `Peminjaman::cari()` |
| `buatLaporan()` | `LaporanController::buatLaporan()` |
| `olahData()` | ringkasan laporan di `LaporanController::buatLaporan()` |

Catatan: `updateStatus`, `simpanDenda`, `tambahStok`, dan `cekStok` sengaja tidak dipecah menjadi
method terpisah agar seluruh proses tetap atomik dalam satu `beginTransaction()`.
