<?php
declare(strict_types=1);

final class AuthController extends Controller
{
    public function form(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::role() === 'petugas' ? '/admin' : '/beranda');
        }

        $this->view('auth/login', ['judul' => 'Masuk'], 'auth');
    }

    public function login(): void
    {
        $username = $this->input('username');
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $this->loginGagal('Username dan kata sandi wajib diisi.');
        }

        $user = User::cariUser($username);

        if (!$user || !Auth::verifikasiPassword($user, $password)) {
            $this->loginGagal('Username atau kata sandi salah.');
        }

        if ($user['status'] !== 'aktif') {
            $this->loginGagal('Akun Anda nonaktif. Hubungi petugas perpustakaan.');
        }

        Auth::loginBerhasil($user);

        $this->redirect($user['role'] === 'petugas' ? '/admin' : '/beranda');
    }

    private function loginGagal(string $pesan): never
    {
        Flash::set('bahaya', $pesan);
        $this->redirect('/login');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
