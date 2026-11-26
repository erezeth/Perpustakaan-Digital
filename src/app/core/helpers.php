<?php
/** Helper tampilan global. */

function e($nilai): string
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return ASSET_URL . '/' . ltrim($path, '/');
}

function inisial(?string $nama): string
{
    $kata = preg_split('/\s+/', trim((string) $nama)) ?: [];
    $huruf = array_map(static fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)), array_slice($kata, 0, 2));

    return $huruf ? implode('', $huruf) : '?';
}

function rupiah($nominal): string
{
    return 'Rp ' . number_format((float) $nominal, 0, ',', '.');
}

function tanggal(?string $ymd): string
{
    if (!$ymd) {
        return '—';
    }

    static $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $waktu = strtotime($ymd);

    return $waktu ? date('j', $waktu) . ' ' . $bulan[(int) date('n', $waktu) - 1] . ' ' . date('Y', $waktu) : '—';
}

/** Sisa hari menuju jatuh tempo (negatif = terlambat). */
function sisaHari(string $jatuhTempo): int
{
    $tempo = new DateTime($jatuhTempo);
    $hariIni = new DateTime('today');

    return (int) $hariIni->diff($tempo)->format('%r%a');
}

/** Label + jenis badge berdasarkan status tersimpan dan tanggal jatuh tempo. */
function statusPeminjaman(array $p): array
{
    if (($p['status'] ?? '') === 'dikembalikan') {
        return ['Dikembalikan', 'netral'];
    }
    if (($p['status'] ?? '') === 'menunggu_verifikasi') {
        return ['Menunggu verifikasi', 'peringatan'];
    }
    if (!empty($p['tgl_jatuh_tempo']) && $p['tgl_jatuh_tempo'] < date('Y-m-d')) {
        return ['Terlambat', 'bahaya'];
    }

    return ['Dipinjam', 'sukses'];
}

function badge(string $teks, string $jenis = 'netral'): string
{
    return '<span class="badge badge--' . e($jenis) . '">' . e($teks) . '</span>';
}

/** Cover buku: foto asli bila ada, selain itu blok warna + ikon. */
function coverBuku(array $b, string $kelas = ''): string
{
    if (!empty($b['cover'])) {
        return '<div class="cover ' . e($kelas) . '"><img src="' . e(UPLOAD_URL . '/' . $b['cover']) . '" alt="Cover ' . e($b['judul'] ?? '') . '"></div>';
    }

    $warna = ['sage', 'warm', 'rose-tua'][((int) ($b['id'] ?? 0)) % 3];

    return '<div class="cover cover--' . $warna . ' ' . e($kelas) . '">' . ikon('book-open', 28) . '</div>';
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(Csrf::token()) . '">';
}

/** Partial paginasi sederhana. $query = parameter GET yang dipertahankan. */
function paginasi(int $total, int $perHalaman, int $halaman, string $path, array $query = []): string
{
    $jumlah = max(1, (int) ceil($total / $perHalaman));
    if ($jumlah <= 1) {
        return '';
    }

    $halaman = min(max(1, $halaman), $jumlah);
    $tautan = static function (int $target) use ($path, $query): string {
        $query['halaman'] = $target;

        return url($path . '?' . http_build_query($query));
    };

    $html = '<div class="paginasi">';
    if ($halaman > 1) {
        $html .= '<a class="btn btn--sm btn--outline" href="' . e($tautan($halaman - 1)) . '">Sebelumnya</a>';
    }
    $html .= '<span class="redup">Halaman ' . $halaman . ' dari ' . $jumlah . '</span>';
    if ($halaman < $jumlah) {
        $html .= '<a class="btn btn--sm btn--outline" href="' . e($tautan($halaman + 1)) . '">Berikutnya</a>';
    }

    return $html . '</div>';
}

function ikon(string $nama, int $ukuran = 20): string
{
    static $paths = [
        'home'         => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9.5 21v-6h5v6"/>',
        'books'        => '<path d="M4 4h4v16H4z"/><path d="M10 4h4v16h-4z"/><path d="m16.5 4.5 3.5 1-2.5 14-3.5-1z"/>',
        'book-open'    => '<path d="M12 6.5C10 4.5 6.5 4 3 5v14c3.5-1 7-.5 9 1.5 2-2 5.5-2.5 9-1.5V5c-3.5-1-7-.5-9 1.5Z"/><path d="M12 6.5v14"/>',
        'history'      => '<path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/><path d="M12 8v4l3 2"/>',
        'receipt'      => '<path d="M5 3h14v18l-2-1.5-2 1.5-2-1.5-2 1.5-2-1.5L5 21z"/><path d="M9 8h6M9 12h6"/>',
        'user'         => '<circle cx="12" cy="8" r="3.5"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0"/>',
        'users'        => '<circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0"/><path d="M16 5.5a3 3 0 0 1 0 5.5"/><path d="M17.5 14.5A6 6 0 0 1 21 20"/>',
        'search'       => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'chevron-right'=> '<path d="m9 6 6 6-6 6"/>',
        'arrow-left'   => '<path d="M19 12H5"/><path d="m11 18-6-6 6-6"/>',
        'plus'         => '<path d="M12 5v14M5 12h14"/>',
        'pencil'       => '<path d="M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16z"/><path d="m14 6 4 4"/>',
        'trash'        => '<path d="M4 7h16"/><path d="M9 7V4h6v3"/><path d="M6 7l1 14h10l1-14"/><path d="M10 11v6M14 11v6"/>',
        'check'        => '<path d="m5 12 5 5 9-10"/>',
        'x'            => '<path d="M6 6l12 12M18 6 6 18"/>',
        'warning'      => '<path d="M12 3 2.5 20h19z"/><path d="M12 9v5"/><path d="M12 17.5h.01"/>',
        'calendar'     => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/>',
        'lock'         => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
        'envelope'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'download'     => '<path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M4 21h16"/>',
        'logout'       => '<path d="M14 4h5v16h-5"/><path d="m10 8 4 4-4 4"/><path d="M14 12H3"/>',
        'info'         => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><path d="M12 8h.01"/>',
        'chart'        => '<path d="M4 20V4"/><path d="M4 20h16"/><rect x="7" y="12" width="3" height="5"/><rect x="12" y="8" width="3" height="9"/><rect x="17" y="5" width="3" height="12"/>',
        'grid'         => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>',
        'tag'          => '<path d="M3 12V4h8l10 10-8 8z"/><path d="M8 8h.01"/>',
        'swap'         => '<path d="M4 8h13l-3-3"/><path d="M20 16H7l3 3"/>',
        'clock'        => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    ];

    $path = $paths[$nama] ?? '';

    return '<svg class="ikon" width="' . $ukuran . '" height="' . $ukuran . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}

function logo(string $href = '#', bool $inverse = false): string
{
    $kelas = 'logo' . ($inverse ? ' logo--inverse' : '');
    $ikon = '<svg class="logo__ikon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 6.5C10 4.5 6.5 4 3 5v14c3.5-1 7-.5 9 1.5 2-2 5.5-2.5 9-1.5V5c-3.5-1-7-.5-9 1.5Z"/><path d="M12 6.5v14"/></svg>';

    return '<a class="' . $kelas . '" href="' . e($href) . '">' . $ikon
        . '<span class="logo__teks">Litera<span class="logo__titik">.</span></span></a>';
}
