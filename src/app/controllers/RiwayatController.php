<?php
declare(strict_types=1);

final class RiwayatController extends Controller
{
    public function anggota(): void
    {
        $status = (string) ($_GET['status'] ?? '');

        $this->view('anggota/riwayat', [
            'judul' => 'Riwayat',
            'daftar' => Peminjaman::cari(['user_id' => (int) Auth::id(), 'status' => $status], 100),
            'status' => $status,
        ]);
    }

    public function admin(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');
        $periode = (string) ($_GET['periode'] ?? '');
        $halaman = $this->halaman();
        $perHalaman = 20;
        $filter = array_filter(['q' => $q, 'status' => $status, 'periode' => $periode]);
        $total = Peminjaman::hitung($filter);

        $this->view('admin/riwayat', [
            'judul' => 'Riwayat Peminjaman dan Pengembalian',
            'daftar' => Peminjaman::cari($filter, $perHalaman, ($halaman - 1) * $perHalaman),
            'q' => $q,
            'status' => $status,
            'periode' => $periode,
            'paginasi' => paginasi($total, $perHalaman, $halaman, '/admin/riwayat', array_filter(['q' => $q, 'status' => $status, 'periode' => $periode])),
        ], 'admin');
    }
}
