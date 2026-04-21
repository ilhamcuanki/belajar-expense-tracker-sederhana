CREATE DATABASE IF NOT EXISTS db_expense_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_expense_tracker;

-- Tabel Kategori
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(50) NOT NULL,
    tipe ENUM('pemasukan', 'pengeluaran') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Transaksi (Jurnal Umum)
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    deskripsi VARCHAR(150) NOT NULL,
    jumlah DECIMAL(12,2) NOT NULL CHECK (jumlah > 0),
    tipe ENUM('pemasukan', 'pengeluaran') NOT NULL,
    kategori_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Tabel Budget
CREATE TABLE budget (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NOT NULL,
    jumlah_batas DECIMAL(12,2) NOT NULL CHECK (jumlah_batas > 0),
    bulan TINYINT NOT NULL CHECK (bulan BETWEEN 1 AND 12),
    tahun YEAR NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uk_budget_period (kategori_id, bulan, tahun)
) ENGINE=InnoDB;

-- Index untuk optimasi query filter & agregasi
CREATE INDEX idx_transaksi_tanggal ON transaksi(tanggal);
CREATE INDEX idx_transaksi_kategori ON transaksi(kategori_id);
CREATE INDEX idx_budget_periode ON budget(kategori_id, bulan, tahun);

-- Data Default
INSERT INTO kategori (nama, tipe) VALUES 
('Gaji', 'pemasukan'),
('Freelance', 'pemasukan'),
('Makan', 'pengeluaran'),
('Transport', 'pengeluaran'),
('Hiburan', 'pengeluaran'),
('Tagihan', 'pengeluaran');