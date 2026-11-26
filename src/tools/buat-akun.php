<?php
declare(strict_types=1);

/**
 * Membuat akun petugas atau anggota dari terminal.
 * Jalankan dari root proyek: php tools/buat-akun.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Hanya bisa dijalankan dari terminal.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

function tanya(string $label): string
{
    fwrite(STDOUT, $label . ': ');
    $baris = fgets(STDIN);

    return $baris === false ? '' : trim($baris);
}

$role = strtolower(tanya('Jenis akun (petugas/anggota)'));
if (!in_array($role, ['petugas', 'anggota'], true)) {
    exit("Jenis akun harus 'petugas' atau 'anggota'.\n");
}

$nama = tanya('Nama lengkap');
if ($nama === '') {
    exit("Nama wajib diisi.\n");
}

$username = $role === 'petugas' ? tanya('Username') : User::bahasaUsername();
if ($username === '' || !preg_match('/^[A-Za-z0-9._-]+$/', $username)) {
    exit("Username wajib diisi dan hanya boleh huruf, angka, titik, garis bawah, atau strip.\n");
}
if (User::usernameDipakai($username)) {
    exit("Username '$username' sudah dipakai.\n");
}

$email = tanya('Email');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Email tidak valid.\n");
}

// shortcut: password terlihat saat diketik; pakai input tersembunyi bila perlu.
$password = tanya('Password (minimal 6 karakter)');
if (strlen($password) < 6) {
    exit("Password minimal 6 karakter.\n");
}

$data = [
    'nama' => $nama,
    'username' => $username,
    'email' => $email,
    'password' => $password,
    'status' => 'aktif',
];

if ($role === 'petugas') {
    User::buatPetugas($data);
} else {
    User::buatAnggota($data + ['no_telp' => '', 'alamat' => '']);
}

fwrite(STDOUT, "\nAkun $role dibuat.\nUsername: $username\nPassword: $password\n");
