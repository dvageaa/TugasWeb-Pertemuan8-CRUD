<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Database::getInstance();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    set_flash('error', 'ID produk tidak valid.');
    redirect('index.php');
}

// Ambil data produk yang akan diedit (prepared statement)
$stmt = $pdo->prepare('SELECT * FROM produk WHERE id = :id');
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    set_flash('error', 'Produk tidak ditemukan.');
    redirect('index.php');
}

$kategoriList = $pdo->query('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori')->fetchAll();
$supplierList = $pdo->query('SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier')->fetchAll();

// Form pre-filled dari data produk (bisa dioverride oleh POST saat validasi gagal)
$old = [
    'nama_produk' => $produk['nama_produk'],
    'kategori_id' => $produk['kategori_id'],
    'supplier_id' => $produk['supplier_id'],
    'harga'       => $produk['harga'],
    'stok'        => $produk['stok'],
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['nama_produk'] = trim($_POST['nama_produk'] ?? '');
    $old['kategori_id'] = $_POST['kategori_id'] ?? '';
    $old['supplier_id'] = $_POST['supplier_id'] ?? '';
    $old['harga']       = $_POST['harga'] ?? '';
    $old['stok']        = $_POST['stok'] ?? '';

    if ($old['nama_produk'] === '') {
        $errors['nama_produk'] = 'Nama produk wajib diisi.';
    }
    if ($old['kategori_id'] === '' || !ctype_digit((string) $old['kategori_id'])) {
        $errors['kategori_id'] = 'Kategori wajib dipilih.';
    }
    if ($old['supplier_id'] === '' || !ctype_digit((string) $old['supplier_id'])) {
        $errors['supplier_id'] = 'Supplier wajib dipilih.';
    }
    if ($old['harga'] === '' || !is_numeric($old['harga']) || (float) $old['harga'] < 0) {
        $errors['harga'] = 'Harga harus berupa angka dan tidak boleh negatif.';
    }
    if ($old['stok'] === '' || !ctype_digit((string) $old['stok'])) {
        $errors['stok'] = 'Stok harus berupa bilangan bulat.';
    }

    if (empty($errors)) {
        $update = $pdo->prepare(
            'UPDATE produk
             SET nama_produk = :nama_produk,
                 kategori_id = :kategori_id,
                 supplier_id = :supplier_id,
                 harga = :harga,
                 stok = :stok
             WHERE id = :id'
        );
        $update->execute([
            ':nama_produk' => $old['nama_produk'],
            ':kategori_id' => (int) $old['kategori_id'],
            ':supplier_id' => (int) $old['supplier_id'],
            ':harga'       => (float) $old['harga'],
            ':stok'        => (int) $old['stok'],
            ':id'          => $id,
        ]);

        set_flash('success', 'Produk "' . $old['nama_produk'] . '" berhasil diperbarui.');
        redirect('index.php');
    }
}

$pageTitle = 'Edit Produk';
require __DIR__ . '/includes/header.php';
?>

<a href="index.php" class="back-link">&larr; Kembali ke daftar produk</a>

<div class="card">
  <h2 style="margin-top:0;">Edit Produk — #<?= e((string) $id) ?></h2>

  <form method="post" action="edit.php?id=<?= (int) $id ?>" novalidate>
    <input type="hidden" name="id" value="<?= (int) $id ?>">
    <div class="form-grid">
      <div class="full">
        <label for="nama_produk">Nama Produk</label>
        <input type="text" id="nama_produk" name="nama_produk" value="<?= e($old['nama_produk']) ?>">
        <?php if (isset($errors['nama_produk'])): ?><div class="field-error"><?= e($errors['nama_produk']) ?></div><?php endif; ?>
      </div>

      <div>
        <label for="kategori_id">Kategori</label>
        <select id="kategori_id" name="kategori_id">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($kategoriList as $k): ?>
            <option value="<?= (int) $k['id'] ?>" <?= (string) $old['kategori_id'] === (string) $k['id'] ? 'selected' : '' ?>>
              <?= e($k['nama_kategori']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($errors['kategori_id'])): ?><div class="field-error"><?= e($errors['kategori_id']) ?></div><?php endif; ?>
      </div>

      <div>
        <label for="supplier_id">Supplier</label>
        <select id="supplier_id" name="supplier_id">
          <option value="">-- Pilih Supplier --</option>
          <?php foreach ($supplierList as $s): ?>
            <option value="<?= (int) $s['id'] ?>" <?= (string) $old['supplier_id'] === (string) $s['id'] ? 'selected' : '' ?>>
              <?= e($s['nama_supplier']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($errors['supplier_id'])): ?><div class="field-error"><?= e($errors['supplier_id']) ?></div><?php endif; ?>
      </div>

      <div>
        <label for="harga">Harga (Rp)</label>
        <input type="number" id="harga" name="harga" min="0" step="0.01" value="<?= e((string) $old['harga']) ?>">
        <?php if (isset($errors['harga'])): ?><div class="field-error"><?= e($errors['harga']) ?></div><?php endif; ?>
      </div>

      <div>
        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok" min="0" step="1" value="<?= e((string) $old['stok']) ?>">
        <?php if (isset($errors['stok'])): ?><div class="field-error"><?= e($errors['stok']) ?></div><?php endif; ?>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      <a href="index.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
