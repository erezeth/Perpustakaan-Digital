<?php
declare(strict_types=1);

final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function wajib(string $key, $nilai, string $label): void
    {
        if (trim((string) $nilai) === '') {
            $this->errors[$key] = $label . ' wajib diisi.';
        }
    }

    public function panjang(string $key, $nilai, int $min, int $maks, string $label): void
    {
        $panjang = mb_strlen(trim((string) $nilai));
        if ($panjang > 0 && ($panjang < $min || $panjang > $maks)) {
            $this->errors[$key] = sprintf('%s harus %d–%d karakter.', $label, $min, $maks);
        }
    }

    public function email(string $key, $nilai): void
    {
        if ((string) $nilai !== '' && !filter_var($nilai, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$key] = 'Format email tidak valid.';
        }
    }

    public function angka(string $key, $nilai, int $min, string $label): void
    {
        if ((string) $nilai === '' || !is_numeric($nilai) || (int) $nilai < $min) {
            $this->errors[$key] = $label . ' minimal ' . $min . '.';
        }
    }

    public function tambah(string $key, string $pesan): void
    {
        $this->errors[$key] = $pesan;
    }

    public function gagal(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }
}
