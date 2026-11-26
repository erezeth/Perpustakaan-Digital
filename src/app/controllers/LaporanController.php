<?php
declare(strict_types=1);

final class LaporanController extends Controller
{
    public function buatLaporan(): void
    {
        $jenis = (string) ($_GET['jenis'] ?? 'peminjaman');
        if (!in_array($jenis, ['peminjaman', 'pengembalian', 'denda', 'stok'], true)) {
            $jenis = 'peminjaman';
        }

        $periode = (string) ($_GET['periode'] ?? date('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', $periode)) {
            $periode = date('Y-m');
        }

        $data = [];
        $ringkasan = [];

        if ($jenis === 'peminjaman' || $jenis === 'pengembalian') {
            $data = Peminjaman::periode($periode, $jenis === 'pengembalian');
            $dikembalikan = count(array_filter($data, static fn ($p) => $p['status'] === 'dikembalikan'));
            $terlambat = count(array_filter($data, static fn ($p) => $p['status'] === 'dipinjam' && $p['tgl_jatuh_tempo'] < date('Y-m-d')));
            $ringkasan = [
                ['label' => 'Total Transaksi', 'nilai' => (string) count($data), 'sub' => 'Periode ' . $periode],
                ['label' => 'Sudah Kembali', 'nilai' => (string) $dikembalikan, 'sub' => (count($data) - $dikembalikan) . ' belum kembali'],
                ['label' => 'Terlambat', 'nilai' => (string) $terlambat, 'sub' => 'Belum dikembalikan'],
            ];
        } elseif ($jenis === 'denda') {
            $data = Denda::periode($periode);
            $total = array_sum(array_map(static fn ($d) => (float) $d['nominal'], $data));
            $belum = array_sum(array_map(static fn ($d) => $d['status_bayar'] === 'belum' ? (float) $d['nominal'] : 0, $data));
            $ringkasan = [
                ['label' => 'Total Denda', 'nilai' => rupiah($total), 'sub' => count($data) . ' catatan'],
                ['label' => 'Belum Dibayar', 'nilai' => rupiah($belum), 'sub' => 'Menunggu pelunasan'],
                ['label' => 'Sudah Dibayar', 'nilai' => rupiah($total - $belum), 'sub' => 'Lunas'],
            ];
        } else {
            $data = Buku::cari('', 0, 1000, 0);
            $eksemplar = array_sum(array_map(static fn ($b) => (int) $b['stok'], $data));
            $habis = count(array_filter($data, static fn ($b) => (int) $b['stok'] === 0));
            $ringkasan = [
                ['label' => 'Judul Buku', 'nilai' => (string) count($data), 'sub' => 'Seluruh koleksi'],
                ['label' => 'Total Eksemplar', 'nilai' => (string) $eksemplar, 'sub' => 'Stok tersedia'],
                ['label' => 'Stok Habis', 'nilai' => (string) $habis, 'sub' => 'Judul tanpa stok'],
            ];
        }

        $this->view('admin/laporan', [
            'judul' => 'Laporan',
            'jenis' => $jenis,
            'periode' => $periode,
            'baris' => $data,
            'ringkasan' => $ringkasan,
        ], 'admin');
    }
}
