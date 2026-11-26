<?php
declare(strict_types=1);

final class ProfilController extends Controller
{
    public function index(): void
    {
        $user = User::find((int) Auth::id());
        if (!$user) {
            Auth::logout();
            $this->redirect('/login');
        }

        $filter = ['user_id' => $user['id']];

        $this->view('anggota/profil', [
            'judul' => 'Profil',
            'user' => $user,
            'dipinjam' => Peminjaman::hitungBukuAktif((int) $user['id']),
            'selesai' => Peminjaman::hitung($filter + ['status' => 'dikembalikan']),
            'denda' => Denda::totalBelum((int) $user['id']),
        ]);
    }
}
