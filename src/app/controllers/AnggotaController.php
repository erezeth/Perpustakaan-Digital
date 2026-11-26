<?php
declare(strict_types=1);

final class AnggotaController extends Controller
{
    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $halaman = $this->halaman();
        $perHalaman = 20;
        $total = User::hitungAnggota($q);

        $this->view('admin/anggota/index', [
            'judul' => 'Kelola Anggota',
            'daftar' => User::anggota($q, $perHalaman, ($halaman - 1) * $perHalaman),
            'q' => $q,
            'paginasi' => paginasi($total, $perHalaman, $halaman, '/admin/anggota', ['q' => $q]),
        ], 'admin');
    }

    public function create(): void
    {
        $this->form(null, [], User::bahasaUsername());
    }

    public function store(): void
    {
        [$data, $errors] = $this->validasi(true);
        if ($errors) {
            $this->form($data, $errors, User::bahasaUsername());
            return;
        }

        $data['username'] = User::bahasaUsername();
        if (User::usernameDipakai($data['username'])) {
            $this->form($data, ['username' => 'Username sudah dipakai, coba lagi.'], $data['username']);
            return;
        }

        User::buatAnggota($data);
        Flash::set('sukses', 'Anggota berhasil ditambahkan dengan username ' . $data['username'] . '.');
        $this->redirect('/admin/anggota');
    }

    public function edit(int $id): void
    {
        $anggota = User::find($id);
        if (!$anggota || $anggota['role'] !== 'anggota') {
            http_response_code(404);
            exit('Anggota tidak ditemukan.');
        }

        $this->form($anggota, []);
    }

    public function update(int $id): void
    {
        $anggota = User::find($id);
        if (!$anggota || $anggota['role'] !== 'anggota') {
            http_response_code(404);
            exit('Anggota tidak ditemukan.');
        }

        [$data, $errors] = $this->validasi(false, $id);
        if ($errors) {
            $this->form($data + $anggota, $errors);
            return;
        }

        $data['username'] = $anggota['username'];
        User::updateAnggota($id, $data);
        Flash::set('sukses', 'Data anggota berhasil diperbarui.');
        $this->redirect('/admin/anggota');
    }

    public function destroy(int $id): void
    {
        if (User::dipakaiTransaksi($id)) {
            Flash::set('bahaya', 'Anggota tidak bisa dihapus karena masih punya riwayat transaksi.');
            $this->redirect('/admin/anggota');
        }

        User::hapus($id);
        Flash::set('sukses', 'Anggota berhasil dihapus.');
        $this->redirect('/admin/anggota');
    }

    private function form(?array $anggota, array $errors, string $username = ''): void
    {
        $this->view('admin/anggota/form', [
            'judul' => ($anggota && isset($anggota['id'])) ? 'Edit Anggota' : 'Tambah Anggota',
            'anggota' => $anggota,
            'errors' => $errors,
            'username' => $username,
        ], 'admin');
    }

    /** @return array{0: array<string, string>, 1: array<string, string>} */
    private function validasi(bool $baru, int $kecualiId = 0): array
    {
        $data = [
            'nama' => $this->input('nama'),
            'email' => $this->input('email'),
            'no_telp' => $this->input('no_telp'),
            'alamat' => $this->input('alamat'),
            'status' => $this->input('status', 'aktif'),
            'password' => (string) ($_POST['password'] ?? ''),
        ];

        $v = new Validator();
        $v->wajib('nama', $data['nama'], 'Nama');
        $v->panjang('nama', $data['nama'], 1, 100, 'Nama');
        $v->wajib('email', $data['email'], 'Email');
        $v->email('email', $data['email']);
        $v->panjang('no_telp', $data['no_telp'], 0, 20, 'No. telepon');
        $v->panjang('alamat', $data['alamat'], 0, 255, 'Alamat');
        if (!in_array($data['status'], ['aktif', 'nonaktif'], true)) {
            $v->tambah('status', 'Status tidak valid.');
        }
        if ($baru || $data['password'] !== '') {
            $v->wajib('password', $data['password'], 'Password');
        }
        if ($data['password'] !== '') {
            $v->panjang('password', $data['password'], 6, 100, 'Password');
        }

        return [$data, $v->errors()];
    }
}
