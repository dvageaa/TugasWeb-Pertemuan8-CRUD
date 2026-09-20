<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Database::getInstance();

// ---------- Fitur pencarian (bonus) ----------
$keyword = trim($_GET['q'] ?? '');

// ---------- Fitur pagination (bonus) ----------
$perPage    = 5;
$page       = max(1, (int) ($_GET['page'] ?? 1));
$offset     = ($page - 1) * $perPage;

// Hitung total data untuk pagination (prepared statement)
if ($keyword !== '') {
    $countStmt = $pdo->prepare(
        'SELECT COUNT(*) FROM produk p
         JOIN kategori k ON k.id = p.kategori_id
         WHERE p.nama_produk LIKE :kw OR k.nama_kategori LIKE :kw'
    );
    $countStmt->execute([':kw' => '%' . $keyword . '%']);
} else {
    $countStmt = $pdo->query('SELECT COUNT(*) FROM produk');
}
$totalRows  = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalRows / $perPage));

// ---------- Query utama: JOIN produk + kategori + supplier ----------
$sql = 'SELECT p.id, p.nama_produk, p.harga, p.stok, p.created_at,
               k.nama_kategori, s.nama_supplier
        FROM produk p
        JOIN kategori k ON k.id = p.kategori_id
        JOIN supplier s ON s.id = p.supplier_id';

$params = [];
if ($keyword !== '') {
    $sql .= ' WHERE p.nama_produk LIKE :kw OR k.nama_kategori LIKE :kw';
    $params[':kw'] = '%' . $keyword . '%';
}
$sql .= ' ORDER BY p.id DESC LIMIT :limit OFFSET :offset';

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$produkList = $stmt->fetchAll();

$pageTitle = 'Daftar Produk';
require __DIR__ . '/includes/header.php';
?>

<div class="card">
  <div class="toolbar">
    <form class="search-form" method="get" action="index.php">
      <input type="text" name="q" placeholder="Cari nama produk atau kategori..." value="<?= e($keyword) ?>">
      <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
      <?php if ($keyword !== ''): ?>
        <a href="index.php" class="btn btn-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </form>
    <div class="actions">
      <a href="export.php<?= $keyword !== '' ? '?q=' . urlencode($keyword) : '' ?>" class="btn btn-secondary">⬇ Export CSV</a>
      <a href="create.php" class="btn btn-primary">+ Tambah Produk</a>
    </div>
  </div>

  <?php if (empty($produkList)): ?>
    <div class="empty-state">
      <p>Tidak ada produk ditemukan<?= $keyword !== '' ? ' untuk pencarian "' . e($keyword) . '"' : '' ?>.</p>
    </div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Produk</th>
          <th>Kategori</th>
          <th>Supplier</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produkList as $row): ?>
          <tr>
            <td data-label="#"><?= e((string) $row['id']) ?></td>
            <td data-label="Nama Produk"><strong><?= e($row['nama_produk']) ?></strong></td>
            <td data-label="Kategori"><span class="badge"><?= e($row['nama_kategori']) ?></span></td>
            <td data-label="Supplier"><?= e($row['nama_supplier']) ?></td>
            <td data-label="Harga"><?= e(rupiah($row['harga'])) ?></td>
            <td data-label="Stok">
              <span class="<?= (int) $row['stok'] < 10 ? 'stok-low' : 'stok-ok' ?>">
                <?= e((string) $row['stok']) ?>
              </span>
            </td>
            <td data-label="Aksi">
              <div class="actions">
                <a class="btn btn-warning btn-sm" href="edit.php?id=<?= (int) $row['id'] ?>">Edit</a>
                <form method="post" action="delete.php" onsubmit="return confirm('Yakin ingin menghapus produk &quot;<?= e($row['nama_produk']) ?>&quot;? Aksi ini tidak bisa dibatalkan.');" style="display:inline;">
                  <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <?php $qs = http_build_query(['q' => $keyword, 'page' => $i]); ?>
          <a href="index.php?<?= e($qs) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
