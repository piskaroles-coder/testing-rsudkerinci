-- ============================================
-- Database Setup untuk Berita & Pengumuman
-- ============================================
-- Jalankan query ini di phpMyAdmin untuk memastikan
-- semua table dan index sudah dibuat dengan benar
-- ============================================

-- Pastikan database dan table sudah ada
USE rsud_kerinci;

-- ==========================================
-- 1. TABEL BERITA (untuk news/artikel)
-- ==========================================
-- Sudah ada, tapi bisa di-recreate jika perlu
CREATE TABLE IF NOT EXISTS berita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    konten TEXT NOT NULL,
    gambar VARCHAR(255),
    tanggal_publikasi DATETIME NOT NULL,
    penulis VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('publikasi', 'draft') DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 2. TABEL PENGUMUMAN (untuk announcements)
-- ==========================================
-- Sudah ada, tapi bisa di-recreate jika perlu
CREATE TABLE IF NOT EXISTS pengumuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    tanggal_publikasi DATETIME NOT NULL,
    penulis VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('aktif', 'arsip') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 3. INDEXES UNTUK PERFORMA
-- ==========================================
-- Mengecek apakah index sudah ada, jika belum akan dibuat
CREATE INDEX IF NOT EXISTS idx_status_berita ON berita(status);
CREATE INDEX IF NOT EXISTS idx_tanggal_berita ON berita(tanggal_publikasi);
CREATE INDEX IF NOT EXISTS idx_status_pengumuman ON pengumuman(status);
CREATE INDEX IF NOT EXISTS idx_tanggal_pengumuman ON pengumuman(tanggal_publikasi);

-- ==========================================
-- 4. SAMPLE DATA (OPTIONAL)
-- ==========================================
-- Uncomment jika ingin menambah sample data
-- INSERT INTO berita (judul, konten, tanggal_publikasi, penulis, status) VALUES
-- ('Berita Test 1', 'Ini adalah berita test pertama', NOW(), 'Admin', 'publikasi'),
-- ('Berita Test 2', 'Ini adalah berita test kedua', NOW(), 'Admin', 'draft');

-- INSERT INTO pengumuman (judul, isi, tanggal_publikasi, penulis, status) VALUES
-- ('Pengumuman Test 1', 'Ini adalah pengumuman test pertama', NOW(), 'Admin', 'aktif'),
-- ('Pengumuman Test 2', 'Ini adalah pengumuman test kedua', NOW(), 'Admin', 'aktif');

-- ==========================================
-- 5. VERIFIKASI
-- ==========================================
-- Jalankan query di bawah untuk verifikasi

-- Lihat struktur tabel berita
DESC berita;

-- Lihat struktur tabel pengumuman
DESC pengumuman;

-- Lihat semua index
SHOW INDEX FROM berita;
SHOW INDEX FROM pengumuman;

-- Hitung total berita
SELECT COUNT(*) as total_berita FROM berita;

-- Hitung total pengumuman
SELECT COUNT(*) as total_pengumuman FROM pengumuman;

-- ==========================================
-- SELESAI
-- ==========================================
