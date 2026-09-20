<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Database::getInstance();

$keyword = trim($_GET['q'] ?? '');

$sql = 'SELECT p.id, p.nama_produk, k.nama_kategori, s.nama_supplier, p.harga, p.stok, p.created_at
        FROM produk p
        JOIN kategori k ON k.id = p.kategori_id
        JOIN supplier s ON s.id = p.supplier_id';

$params = [];
if ($keyword !== '') {
    $sql .= ' WHERE p.nama_produk LIKE :kw OR k.nama_kategori LIKE :kw';
    $params[':kw'] = '%' . $keyword . '%';
}
$sql .= ' ORDER BY p.id ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// ---------- BONUS: Export laporan (CSV) ----------
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=laporan_inventaris_' . date('Ymd_His') . '.csv');

$out = fopen('php://output', 'w');
// BOM supaya karakter/format terbaca rapi saat dibuka di Excel
fwrite($out, "\xEF\xBB\xBF");

fputcsv($out, ['ID', 'Nama Produk', 'Kategori', 'Supplier', 'Harga', 'Stok', 'Dibuat Pada']);

foreach ($rows as $row) {
    fputcsv($out, [
        $row['id'],
        $row['nama_produk'],
        $row['nama_kategori'],
        $row['nama_supplier'],
        $row['harga'],
        $row['stok'],
        $row['created_at'],
    ]);
}

fclose($out);
exit;
