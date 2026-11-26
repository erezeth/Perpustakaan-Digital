<?php
declare(strict_types=1);

final class BukuController extends Controller
{
    /* ---------- Anggota ---------- */

    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $kategoriId = (int) ($_GET['kategori'] ?? 0);
        $halaman = $this->halaman();
        $perHalaman = 12;
        $total = Buku::hitung($q, $kategoriId);

        $this->view('anggota/katalog', [
            'judul' => 'Katalog Buku',
            'buku' => Buku::cari($q, $kategoriId, $perHalaman, ($halaman - 1) * $perHalaman),
            'kategori' => Kategori::all(),
            'q' => $q,
            'kategoriId' => $kategoriId,
            'paginasi' => paginasi($total, $perHalaman, $halaman, '/buku', array_filter(['q' => $q, 'kategori' => $kategoriId])),
        ]);
    }

    public function detail(int $id): void
    {
        $buku = Buku::find($id);
        if (!$buku) {
            http_response_code(404);
            exit('Buku tidak ditemukan.');
        }

        $this->view('anggota/detail', [
            'judul' => $buku['judul'],
            'buku' => $buku,
            'pinjamanAktif' => Peminjaman::hitungBukuAktif((int) Auth::id()),
        ]);
    }

    /* ---------- Petugas ---------- */

    public function adminIndex(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $halaman = $this->halaman();
        $perHalaman = 20;
        $total = Buku::hitung($q, 0);

        $this->view('admin/buku/index', [
            'judul' => 'Kelola Buku',
            'daftar' => Buku::cari($q, 0, $perHalaman, ($halaman - 1) * $perHalaman),
            'q' => $q,
            'paginasi' => paginasi($total, $perHalaman, $halaman, '/admin/buku', ['q' => $q]),
        ], 'admin');
    }

    public function create(): void
    {
        $this->formBuku(null, []);
    }

    public function store(): void
    {
        [$data, $errors] = $this->validasiBuku();
        [$cover, $galatCover] = $this->uploadCover();
        if ($galatCover) {
            $errors['cover'] = $galatCover;
        }

        if ($errors) {
            $this->formBuku($data, $errors);
            return;
        }

        $data['tahun_terbit'] = $data['tahun_terbit'] !== '' ? (int) $data['tahun_terbit'] : null;
        $data['cover'] = $cover;
        Buku::buat($data);

        Flash::set('sukses', 'Buku berhasil ditambahkan.');
        $this->redirect('/admin/buku');
    }

    public function edit(int $id): void
    {
        $buku = Buku::find($id);
        if (!$buku) {
            http_response_code(404);
            exit('Buku tidak ditemukan.');
        }

        $this->formBuku($buku, []);
    }

    public function update(int $id): void
    {
        $buku = Buku::find($id);
        if (!$buku) {
            http_response_code(404);
            exit('Buku tidak ditemukan.');
        }

        [$data, $errors] = $this->validasiBuku();
        [$cover, $galatCover] = $this->uploadCover();
        if ($galatCover) {
            $errors['cover'] = $galatCover;
        }

        if ($errors) {
            $this->formBuku($data + $buku, $errors);
            return;
        }

        $data['tahun_terbit'] = $data['tahun_terbit'] !== '' ? (int) $data['tahun_terbit'] : null;
        if ($cover !== null) {
            $this->hapusCover($buku['cover']);
            $data['cover'] = $cover;
        } else {
            $data['cover'] = $buku['cover'];
        }

        Buku::update($id, $data);
        Flash::set('sukses', 'Buku berhasil diperbarui.');
        $this->redirect('/admin/buku');
    }

    public function destroy(int $id): void
    {
        $buku = Buku::find($id);
        if (!$buku) {
            $this->redirect('/admin/buku');
        }

        if (Buku::dipakai($id)) {
            Flash::set('bahaya', 'Buku tidak bisa dihapus karena masih dipakai pada transaksi.');
            $this->redirect('/admin/buku');
        }

        Buku::hapus($id);
        $this->hapusCover($buku['cover']);
        Flash::set('sukses', 'Buku berhasil dihapus.');
        $this->redirect('/admin/buku');
    }

    private function formBuku(?array $buku, array $errors): void
    {
        $this->view('admin/buku/form', [
            'judul' => $buku ? 'Edit Buku' : 'Tambah Buku',
            'buku' => $buku,
            'kategori' => Kategori::all(),
            'errors' => $errors,
        ], 'admin');
    }

    /** @return array{0: array<string, mixed>, 1: array<string, string>} */
    private function validasiBuku(): array
    {
        $data = [
            'kategori_id' => (int) $this->input('kategori_id'),
            'judul' => $this->input('judul'),
            'penulis' => $this->input('penulis'),
            'penerbit' => $this->input('penerbit'),
            'tahun_terbit' => $this->input('tahun_terbit'),
            'isbn' => $this->input('isbn'),
            'sinopsis' => $this->input('sinopsis'),
            'stok' => $this->input('stok', '0'),
        ];

        $v = new Validator();
        $v->wajib('judul', $data['judul'], 'Judul');
        $v->panjang('judul', $data['judul'], 1, 200, 'Judul');
        $v->wajib('penulis', $data['penulis'], 'Penulis');
        $v->panjang('penulis', $data['penulis'], 1, 150, 'Penulis');
        $v->wajib('kategori_id', $data['kategori_id'] > 0 ? 'ok' : '', 'Kategori');
        if ($data['kategori_id'] > 0 && !Kategori::find($data['kategori_id'])) {
            $v->tambah('kategori_id', 'Kategori tidak ditemukan.');
        }
        $v->angka('stok', $data['stok'], 0, 'Stok');
        if ($data['tahun_terbit'] !== '') {
            $tahun = (int) $data['tahun_terbit'];
            if (!ctype_digit($data['tahun_terbit']) || $tahun < 1000 || $tahun > (int) date('Y') + 1) {
                $v->tambah('tahun_terbit', 'Tahun terbit tidak valid.');
            }
        }
        $v->panjang('isbn', $data['isbn'], 0, 30, 'ISBN');
        $v->panjang('penerbit', $data['penerbit'], 0, 150, 'Penerbit');

        return [$data, $v->errors()];
    }

    /** @return array{0: ?string, 1: ?string} [nama file, pesan galat] */
    private function uploadCover(): array
    {
        if (empty($_FILES['cover']['name'])) {
            return [null, null];
        }

        $file = $_FILES['cover'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [null, 'Upload cover gagal.'];
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return [null, 'Ukuran cover maksimal 2 MB.'];
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $izin = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($izin[$mime])) {
            return [null, 'Format cover harus JPG, PNG, atau WEBP.'];
        }

        if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0775, true) && !is_dir(UPLOAD_DIR)) {
            return [null, 'Folder upload tidak bisa dibuat.'];
        }

        $nama = bin2hex(random_bytes(16)) . '.' . $izin[$mime];
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $nama)) {
            return [null, 'Gagal menyimpan file cover.'];
        }

        return [$nama, null];
    }

    private function hapusCover(?string $nama): void
    {
        if ($nama && is_file(UPLOAD_DIR . '/' . $nama)) {
            @unlink(UPLOAD_DIR . '/' . $nama);
        }
    }
}
