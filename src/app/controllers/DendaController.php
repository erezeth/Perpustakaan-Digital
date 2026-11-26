<?php
declare(strict_types=1);

final class DendaController extends Controller
{
    /* ---------- Tarif denda (petugas) ---------- */

    public function index(): void
    {
        $this->view('admin/denda/index', [
            'judul' => 'Kelola Denda',
            'daftar' => Denda::masterAll(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->form(null, []);
    }

    public function store(): void
    {
        [$data, $errors] = $this->validasi();
        if ($errors) {
            $this->form($data, $errors);
            return;
        }

        Denda::masterBuat($data);
        Flash::set('sukses', 'Tarif denda berhasil ditambahkan.');
        $this->redirect('/admin/denda');
    }

    public function edit(int $id): void
    {
        $denda = Denda::masterFind($id);
        if (!$denda) {
            http_response_code(404);
            exit('Data denda tidak ditemukan.');
        }

        $this->form($denda, []);
    }

    public function update(int $id): void
    {
        $denda = Denda::masterFind($id);
        if (!$denda) {
            http_response_code(404);
            exit('Data denda tidak ditemukan.');
        }

        [$data, $errors] = $this->validasi($id);
        if ($errors) {
            $this->form($data + ['id' => $id], $errors);
            return;
        }

        Denda::masterUpdate($id, $data);
        Flash::set('sukses', 'Tarif denda berhasil diperbarui.');
        $this->redirect('/admin/denda');
    }

    public function destroy(int $id): void
    {
        Denda::masterHapus($id);
        Flash::set('sukses', 'Tarif denda berhasil dihapus.');
        $this->redirect('/admin/denda');
    }

    public function lunas(int $id): void
    {
        Denda::lunas($id);
        Flash::set('sukses', 'Denda ditandai lunas.');
        $kembali = (int) ($_POST['peminjaman_id'] ?? 0);
        $this->redirect($kembali > 0 ? '/admin/transaksi/' . $kembali : '/admin/transaksi');
    }

    /* ---------- Info denda anggota ---------- */

    public function saya(): void
    {
        $userId = (int) Auth::id();
        $daftar = Denda::untukUser($userId);
        $belum = array_filter($daftar, static fn ($d) => $d['status_bayar'] === 'belum');

        $this->view('anggota/denda', [
            'judul' => 'Denda',
            'daftar' => $daftar,
            'totalBelum' => Denda::totalBelum($userId),
            'jumlahBelum' => count($belum),
            'jumlahLunas' => count($daftar) - count($belum),
        ]);
    }

    private function form(?array $denda, array $errors): void
    {
        $this->view('admin/denda/form', [
            'judul' => $denda && isset($denda['id']) ? 'Edit Tarif Denda' : 'Tambah Tarif Denda',
            'denda' => $denda,
            'errors' => $errors,
        ], 'admin');
    }

    /** @return array{0: array<string, string>, 1: array<string, string>} */
    private function validasi(int $kecualiId = 0): array
    {
        $data = [
            'jenis' => $this->input('jenis'),
            'tipe_hitung' => $this->input('tipe_hitung'),
            'nominal' => $this->input('nominal', '0'),
            'keterangan' => $this->input('keterangan'),
        ];

        $v = new Validator();
        if (!in_array($data['jenis'], ['terlambat', 'rusak', 'hilang'], true)) {
            $v->tambah('jenis', 'Jenis denda tidak valid.');
        }
        if (!in_array($data['tipe_hitung'], ['per_hari', 'tetap'], true)) {
            $v->tambah('tipe_hitung', 'Tipe hitung tidak valid.');
        }
        $v->angka('nominal', $data['nominal'], 0, 'Nominal');
        $v->panjang('keterangan', $data['keterangan'], 0, 255, 'Keterangan');

        foreach (Denda::masterAll() as $m) {
            if ((int) $m['id'] !== $kecualiId && $m['jenis'] === $data['jenis']) {
                $v->tambah('jenis', 'Jenis denda ini sudah punya tarif.');
            }
        }

        return [$data, $v->errors()];
    }
}
