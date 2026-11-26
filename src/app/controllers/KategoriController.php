<?php
declare(strict_types=1);

final class KategoriController extends Controller
{
    public function index(): void
    {
        $this->view('admin/kategori/index', [
            'judul' => 'Kelola Kategori',
            'daftar' => Kategori::all(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->form(null, []);
    }

    public function store(): void
    {
        $nama = $this->input('nama_kategori');
        $errors = $this->validasi($nama);

        if ($errors) {
            $this->form(['nama_kategori' => $nama], $errors);
            return;
        }

        Kategori::buat($nama);
        Flash::set('sukses', 'Kategori berhasil ditambahkan.');
        $this->redirect('/admin/kategori');
    }

    public function edit(int $id): void
    {
        $kategori = Kategori::find($id);
        if (!$kategori) {
            http_response_code(404);
            exit('Kategori tidak ditemukan.');
        }

        $this->form($kategori, []);
    }

    public function update(int $id): void
    {
        $kategori = Kategori::find($id);
        if (!$kategori) {
            http_response_code(404);
            exit('Kategori tidak ditemukan.');
        }

        $nama = $this->input('nama_kategori');
        $errors = $this->validasi($nama, $id);

        if ($errors) {
            $this->form(['id' => $id, 'nama_kategori' => $nama], $errors);
            return;
        }

        Kategori::update($id, $nama);
        Flash::set('sukses', 'Kategori berhasil diperbarui.');
        $this->redirect('/admin/kategori');
    }

    public function destroy(int $id): void
    {
        if (Kategori::dipakai($id)) {
            Flash::set('bahaya', 'Kategori tidak bisa dihapus karena masih dipakai buku.');
        } else {
            Kategori::hapus($id);
            Flash::set('sukses', 'Kategori berhasil dihapus.');
        }

        $this->redirect('/admin/kategori');
    }

    private function form(?array $kategori, array $errors): void
    {
        $this->view('admin/kategori/form', [
            'judul' => $kategori && isset($kategori['id']) ? 'Edit Kategori' : 'Tambah Kategori',
            'kategori' => $kategori,
            'errors' => $errors,
        ], 'admin');
    }

    private function validasi(string $nama, int $kecualiId = 0): array
    {
        $v = new Validator();
        $v->wajib('nama_kategori', $nama, 'Nama kategori');
        $v->panjang('nama_kategori', $nama, 1, 100, 'Nama kategori');
        if ($nama !== '' && Kategori::namaDipakai($nama, $kecualiId)) {
            $v->tambah('nama_kategori', 'Nama kategori sudah dipakai.');
        }

        return $v->errors();
    }
}
