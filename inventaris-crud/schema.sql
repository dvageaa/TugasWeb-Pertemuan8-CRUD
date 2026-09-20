CREATE DATABASE IF NOT EXISTS inventaris_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventaris_db;

-- -------------------------------------------------
-- Tabel 1: kategori
-- -------------------------------------------------
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS supplier;
DROP TABLE IF EXISTS log_aktivitas;

CREATE TABLE kategori (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori  VARCHAR(100) NOT NULL,
  deskripsi      VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

-- -------------------------------------------------
-- Tabel 2: supplier
-- -------------------------------------------------
CREATE TABLE supplier (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nama_supplier  VARCHAR(100) NOT NULL,
  kontak         VARCHAR(50)  DEFAULT NULL,
  alamat         VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

-- -------------------------------------------------
-- Tabel 3: produk  (FK ke kategori & supplier)
-- -------------------------------------------------
CREATE TABLE produk (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nama_produk   VARCHAR(150) NOT NULL,
  kategori_id   INT NOT NULL,
  supplier_id   INT NOT NULL,
  harga         DECIMAL(12,2) NOT NULL DEFAULT 0,
  stok          INT NOT NULL DEFAULT 0,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_produk_kategori
    FOREIGN KEY (kategori_id) REFERENCES kategori(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_produk_supplier
    FOREIGN KEY (supplier_id) REFERENCES supplier(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------
-- Tabel bonus: log_aktivitas (diisi dalam TRANSACTION saat delete produk)
-- -------------------------------------------------
CREATE TABLE log_aktivitas (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  aksi         VARCHAR(50)  NOT NULL,
  keterangan   VARCHAR(255) NOT NULL,
  waktu        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- SEED DATA (minimal 5 baris per tabel)
-- =========================================================

INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Elektronik',              'Perangkat elektronik dan aksesorisnya'),
('Alat Tulis Kantor',       'Perlengkapan kantor dan sekolah'),
('Bahan Baku',              'Bahan mentah untuk produksi'),
('Peralatan Rumah Tangga',  'Perlengkapan kebutuhan rumah tangga'),
('Kesehatan',               'Produk kesehatan dan kebersihan');

INSERT INTO supplier (nama_supplier, kontak, alamat) VALUES
('CV Sumber Jaya',        '0812-3456-7890', 'Jl. Merdeka No. 10, Medan'),
('PT Mitra Elektronik',   '061-4567890',    'Jl. Gatot Subroto No. 25, Medan'),
('UD Berkah Abadi',       '0813-1122-3344', 'Jl. Setia Budi No. 5, Medan'),
('CV Anugerah Sejahtera', '0821-9988-7766', 'Jl. Sisingamangaraja No. 88, Medan'),
('PT Nusantara Supplies', '061-8899001',    'Jl. Krakatau No. 17, Medan');

INSERT INTO produk (nama_produk, kategori_id, supplier_id, harga, stok) VALUES
('Mouse Wireless Logitech',  1, 2, 150000, 40),
('Keyboard Mekanik',         1, 2, 450000, 25),
('Pulpen Standard AE7',      2, 1,   3000, 500),
('Buku Tulis 58 Lembar',     2, 3,   4500, 300),
('Tepung Terigu 1kg',        3, 4,  12000, 150),
('Gula Pasir 1kg',           3, 4,  15000, 200),
('Panci Set Stainless',      4, 5, 275000, 20),
('Masker Medis (box)',       5, 3,  35000, 100);
