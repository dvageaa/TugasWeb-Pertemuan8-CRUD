<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$pdo = Database::getInstance();
$id  = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    set_flash('error', 'ID produk tidak valid.');
    redirect('index.php');
}

// Ambil dulu nama produk untuk keterangan log & pesan flash
$check = $pdo->prepare('SELECT nama_produk FROM produk WHERE id = :id');
$check->execute([':id' => $id]);
$produk = $check->fetch();

if (!$produk) {
    set_flash('error', 'Produk tidak ditemukan atau sudah dihapus sebelumnya.');
    redirect('index.php');
}

// ---------- BONUS: TRANSACTION ----------
// Hapus produk & catat log aktivitas sebagai satu kesatuan (all-or-nothing).
// Jika salah satu gagal, semua dibatalkan (rollback) supaya data tetap konsisten.
try {
    $pdo->beginTransaction();

    $deleteStmt = $pdo->prepare('DELETE FROM produk WHERE id = :id');
    $deleteStmt->execute([':id' => $id]);

    $logStmt = $pdo->prepare(
        'INSERT INTO log_aktivitas (aksi, keterangan) VALUES (:aksi, :keterangan)'
    );
    $logStmt->execute([
        ':aksi'       => 'HAPUS_PRODUK',
        ':keterangan' => 'Produk "' . $produk['nama_produk'] . '" (ID: ' . $id . ') dihapus dari inventaris.',
    ]);

    $pdo->commit();

    set_flash('success', 'Produk "' . $produk['nama_produk'] . '" berhasil dihapus.');
} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('error', 'Gagal menghapus produk. Perubahan dibatalkan (rollback).');
}

redirect('index.php');
