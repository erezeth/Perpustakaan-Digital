<?php
declare(strict_types=1);

final class PengembalianController extends Controller
{
    public function index(): void
    {
        $this->view('anggota/kembali', [
            'judul' => 'Pengembalian',
            'pinjaman' => Peminjaman::aktifUser((int) Auth::id()),
        ]);
    }

    public function ajukan(): void
    {
        $id = (int) $this->input('id');
        [$ok, $pesan] = Peminjaman::ajukanKembali($id, (int) Auth::id());
        Flash::set($ok ? 'sukses' : 'bahaya', $pesan);
        $this->redirect('/kembali');
    }

    public function proses(int $id): void
    {
        $kondisi = (array) ($_POST['kondisi'] ?? []);
        [$ok, $pesan, $totalDenda] = Peminjaman::prosesKembali($id, $kondisi, (int) Auth::id());

        if ($ok && $totalDenda > 0) {
            $pesan .= ' Total denda ' . rupiah($totalDenda) . '.';
        }

        Flash::set($ok ? 'sukses' : 'bahaya', $pesan);
        $this->redirect('/admin/transaksi/' . $id);
    }
}
