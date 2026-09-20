<?php
/**
 * includes/functions.php
 * Kumpulan helper kecil yang dipakai di banyak halaman.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escape semua output ke HTML supaya aman dari XSS.
 * Dipakai untuk SETIAP data dari database yang dicetak ke halaman.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Format angka ke Rupiah, mis. 150000 -> "Rp 150.000" */
function rupiah($angka): string
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

/**
 * FLASH MESSAGE (redirect pattern)
 * Simpan pesan singkat di session sebelum redirect,
 * lalu tampilkan & hapus begitu halaman tujuan dibuka.
 */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}
