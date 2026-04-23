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

-- 1. Tambah kolom bukti_foto ke tabel transaksi
ALTER TABLE transaksi ADD COLUMN bukti_foto VARCHAR(255) NULL AFTER kategori_id;

-- 2. Buat tabel Target Budgeting (Tabungan khusus)
CREATE TABLE target_budget (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_target VARCHAR(100) NOT NULL,
    gambar VARCHAR(255) NULL,
    target_jumlah DECIMAL(12,2) NOT NULL CHECK (target_jumlah > 0),
    terkumpul_jumlah DECIMAL(12,2) DEFAULT 0.00,
    sumber_dana VARCHAR(50) NOT NULL, -- Contoh: "BCA", "GoPay", "Tunai"
    status ENUM('aktif', 'tercapai', 'dibatalkan') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Buat tabel Riwayat Setor/Tarik Target
CREATE TABLE target_riwayat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    target_id INT NOT NULL,
    tipe ENUM('setor', 'tarik') NOT NULL,
    jumlah DECIMAL(12,2) NOT NULL CHECK (jumlah > 0),
    keterangan VARCHAR(150),
    tanggal DATE NOT NULL,
    FOREIGN KEY (target_id) REFERENCES target_budget(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Tambah kategori "Lainnya" (hanya untuk pengeluaran sesuai permintaan)
INSERT IGNORE INTO kategori (nama, tipe) VALUES ('Lainnya', 'pemasukan');
INSERT IGNORE INTO kategori (nama, tipe) VALUES ('Lainnya', 'pengeluaran');

-- Login/Register
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Akun Demo (Email: demo@expense.com | Password: password)
INSERT INTO users (nama, email, password) VALUES 
('User Demo', 'demo@expense.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');