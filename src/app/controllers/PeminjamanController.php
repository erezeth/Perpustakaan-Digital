<?php
declare(strict_types=1);

final class PeminjamanController extends Controller
{
    /* ---------- Anggota ---------- */

    public function form(): void
    {
        $aktif = Peminjaman::hitungBukuAktif((int) Auth::id());

        $this->view('anggota/pinjam', [
            'judul' => 'Ajukan Peminjaman',
            'buku' => Buku::tersedia(),
            'dipilih' => (int) ($_GET['buku'] ?? 0),
            'sisaKuota' => max(0, MAX_BUKU_AKTIF - $aktif),
            'tanggal' => date('Y-m-d'),
            'jatuhTempo' => date('Y-m-d', strtotime('+' . MAX_HARI_PINJAM . ' days')),
        ]);
    }

    public function store(): void
    {
        $bukuIds = (array) ($_POST['buku'] ?? []);
        $tanggal = $this->input('tgl_pinjam', date('Y-m-d'));

        [$ok, $pesan, $id] = Peminjaman::ajukanPinjam((int) Auth::id(), $tanggal, $bukuIds);

        if (!$ok) {
            Flash::set('bahaya', $pesan);
            $this->redirect('/pinjam');
        }

        $this->redirect('/pinjam/bukti/' . $id);
    }

    public function bukti(int $id): void
    {
        $peminjaman = Peminjaman::ambilTransaksi($id);
        if (!$peminjaman || (int) $peminjaman['user_id'] !== (int) Auth::id()) {
            http_response_code(404);
            exit('Peminjaman tidak ditemukan.');
        }

        $this->view('anggota/bukti', [
            'judul' => 'Bukti Peminjaman',
            'p' => $peminjaman,
            'detail' => Peminjaman::detail($id),
        ]);
    }

    /* ---------- Petugas ---------- */

    public function adminIndex(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');
        $halaman = $this->halaman();
        $perHalaman = 20;
        $filter = ['q' => $q, 'status' => $status];
        $total = Peminjaman::hitung($filter);

        $this->view('admin/transaksi/index', [
            'judul' => 'Transaksi',
            'daftar' => Peminjaman::cari($filter, $perHalaman, ($halaman - 1) * $perHalaman),
            'q' => $q,
            'status' => $status,
            'paginasi' => paginasi($total, $perHalaman, $halaman, '/admin/transaksi', array_filter(['q' => $q, 'status' => $status])),
        ], 'admin');
    }

    public function adminCreate(): void
    {
        $this->view('admin/transaksi/form', [
            'judul' => 'Catat Peminjaman',
            'anggota' => User::opsiAnggota(),
            'buku' => Buku::tersedia(),
            'tanggal' => date('Y-m-d'),
            'jatuhTempo' => date('Y-m-d', strtotime('+' . MAX_HARI_PINJAM . ' days')),
        ], 'admin');
    }

    public function adminStore(): void
    {
        $userId = (int) $this->input('user_id');
        $anggota = User::find($userId);
        if (!$anggota || $anggota['role'] !== 'anggota' || $anggota['status'] !== 'aktif') {
            Flash::set('bahaya', 'Pilih anggota yang aktif.');
            $this->redirect('/admin/transaksi/tambah');
        }

        [$ok, $pesan, $id] = Peminjaman::ajukanPinjam($userId, $this->input('tgl_pinjam', date('Y-m-d')), (array) ($_POST['buku'] ?? []), (int) Auth::id());

        if (!$ok) {
            Flash::set('bahaya', $pesan);
            $this->redirect('/admin/transaksi/tambah');
        }

        Flash::set('sukses', 'Peminjaman berhasil dicatat.');
        $this->redirect('/admin/transaksi/' . $id);
    }

    public function adminShow(int $id): void
    {
        $peminjaman = Peminjaman::ambilTransaksi($id);
        if (!$peminjaman) {
            http_response_code(404);
            exit('Transaksi tidak ditemukan.');
        }

        $this->view('admin/transaksi/show', [
            'judul' => 'Transaksi #' . $id,
            'p' => $peminjaman,
            'detail' => Peminjaman::detail($id),
            'denda' => Denda::untukPeminjaman($id),
        ], 'admin');
    }
}
