# CRUD Inventaris — Tugas Rutin 8

Aplikasi CRUD sederhana untuk mengelola inventaris produk, dibuat dengan
PHP native + PDO (MySQL). Dibuat sesuai rubrik: fungsionalitas CRUD lengkap,
prepared statements di semua query, desain database ternormalisasi dengan FK,
dan output yang disanitasi dengan `htmlspecialchars()`.

## Struktur Project

```
inventaris-crud/
├── config/
│   └── database.php     # Koneksi PDO — SINGLETON PATTERN
├── includes/
│   ├── functions.php    # helper: e() untuk escape, flash message, rupiah()
│   ├── header.php       # bagian atas halaman (tampilkan flash message)
│   └── footer.php       # bagian bawah halaman
├── css/
│   └── style.css        # tampilan (dark theme, responsive)
├── schema.sql           # buat database + 3 tabel + seed data
├── index.php            # daftar produk (JOIN kategori & supplier) + search + pagination
├── create.php           # form tambah produk (dropdown kategori/supplier)
├── edit.php             # form edit produk (pre-filled)
├── delete.php           # hapus produk (TRANSACTION + log aktivitas)
└── export.php           # export laporan ke CSV
```

## Cara Menjalankan (XAMPP / Laragon)

1. **Copy folder** `inventaris-crud` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. **Buat database**: buka phpMyAdmin → tab *Import* → pilih file `schema.sql`,
   atau lewat terminal:
   ```bash
   mysql -u root -p < schema.sql
   ```
   Ini akan membuat database `inventaris_db`, 3 tabel utama (`kategori`,
   `supplier`, `produk`) dengan foreign key, tabel bonus `log_aktivitas`,
   dan mengisi data awal (5+ baris tiap tabel).
3. **Sesuaikan kredensial** di `config/database.php` jika user/password MySQL
   kamu berbeda dari default XAMPP (`root` tanpa password).
4. Buka `http://localhost/inventaris-crud/` di browser.

## Fitur Wajib (Requirements)

- ✅ Database `inventaris_db` dengan 3 tabel + relasi FK (`produk` → `kategori`, `produk` → `supplier`)
- ✅ Koneksi PDO dengan **Singleton pattern** (`config/database.php`)
- ✅ Minimal 5 data seed per tabel (`schema.sql`)
- ✅ Halaman list produk dengan **JOIN 2 tabel** (kategori & supplier)
- ✅ Form create dengan dropdown kategori & supplier
- ✅ Fitur update (form pre-filled) & delete (dengan konfirmasi JS `confirm()`)
- ✅ **Semua query** memakai **prepared statements**
- ✅ Output HTML memakai `htmlspecialchars()` (lewat helper `e()`)
- ✅ Flash message sukses/gagal dengan **redirect pattern** (via `$_SESSION`)
- ✅ UI rapi & responsive

## Fitur Bonus

- ⭐ **Transaction** saat delete: hapus produk + insert ke tabel `log_aktivitas`
  dibungkus dalam satu transaction (`beginTransaction` / `commit` / `rollBack`)
  supaya konsisten (all-or-nothing).
- ⭐ **Fitur pencarian** — cari produk berdasarkan nama produk atau nama kategori.
- ⭐ **Pagination** — 5 produk per halaman.
- ⭐ **Export laporan** ke CSV (tombol "Export CSV" di halaman utama).

## Catatan Keamanan

- Semua query yang melibatkan input dari user (create, update, delete, search,
  pagination) menggunakan **prepared statement** dengan parameter binding —
  tidak ada string concatenation ke dalam SQL.
- Semua data yang dicetak ke HTML dilewatkan lewat fungsi `e()` (wrapper
  `htmlspecialchars`) untuk mencegah XSS.
- `PDO::ATTR_EMULATE_PREPARES` di-set `false` supaya prepared statement
  benar-benar dieksekusi native oleh MySQL, bukan diemulasikan oleh PHP.
