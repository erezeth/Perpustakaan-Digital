<?php
declare(strict_types=1);

final class BerandaController extends Controller
{
    public function index(): void
    {
        $user = User::find((int) Auth::id());
        if (!$user) {
            Auth::logout();
            $this->redirect('/login');
        }

        $this->view('anggota/beranda', [
            'judul' => 'Beranda',
            'user' => $user,
            'pinjaman' => Peminjaman::aktifUser((int) $user['id']),
            'buku' => Buku::rekomendasi(4),
            'kategori' => Kategori::all(),
        ]);
    }
}
