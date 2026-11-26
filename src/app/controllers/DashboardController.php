<?php
declare(strict_types=1);

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin/dashboard', [
            'judul' => 'Dashboard',
            'anggotaAktif' => User::hitungAnggotaAktif(),
            'koleksi' => Buku::hitungSemua(),
            'aktif' => Peminjaman::jumlahAktif(),
            'terlambat' => Peminjaman::jumlahTerlambat(),
            'dendaBelum' => Denda::totalBelum(),
            'jumlahDenda' => Denda::jumlahBelum(),
            'terbaru' => Peminjaman::terbaru(5),
        ], 'admin');
    }
}
