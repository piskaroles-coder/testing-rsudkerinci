-- Database RSUD Kabupaten Kerinci
-- Backup SQL Script

-- Create Database
CREATE DATABASE IF NOT EXISTS rsud_kerinci;
USE rsud_kerinci;

-- Table for patient registration
CREATE TABLE IF NOT EXISTS pasien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    noktp VARCHAR(20) NOT NULL UNIQUE,
    tgllahir DATE NOT NULL,
    jeniskelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    goldarah VARCHAR(5),
    agama VARCHAR(50),
    alamat TEXT NOT NULL,
    kota VARCHAR(50) NOT NULL,
    kodepos VARCHAR(10),
    telepon VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    penyakit TEXT,
    alergi TEXT,
    namapj VARCHAR(100),
    hubungan VARCHAR(50),
    teleponpj VARCHAR(20),
    alamatpj TEXT,
    asuransi VARCHAR(50),
    nomorasuransi VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for contact messages
CREATE TABLE IF NOT EXISTS kontak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    pesan TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('baru', 'dibaca', 'ditanggapi') DEFAULT 'baru'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for doctors
CREATE TABLE IF NOT EXISTS dokter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    spesialisasi VARCHAR(100) NOT NULL,
    nip VARCHAR(30) NOT NULL UNIQUE,
    no_str VARCHAR(50) NOT NULL,
    telepon VARCHAR(20),
    email VARCHAR(100),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for doctor schedules
CREATE TABLE IF NOT EXISTS jadwal_dokter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dokter_id INT NOT NULL,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    kuota INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for news/berita
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

-- Table for announcements
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

-- Create indexes for better performance
CREATE INDEX idx_noktp ON pasien(noktp);
CREATE INDEX idx_email ON pasien(email);
CREATE INDEX idx_telepon ON pasien(telepon);
CREATE INDEX idx_email_kontak ON kontak(email);
CREATE INDEX idx_status_kontak ON kontak(status);
CREATE INDEX idx_nip_dokter ON dokter(nip);
CREATE INDEX idx_status_dokter ON dokter(status);
CREATE INDEX idx_dokter_jadwal ON jadwal_dokter(dokter_id);
CREATE INDEX idx_hari_jadwal ON jadwal_dokter(hari);
CREATE INDEX idx_status_berita ON berita(status);
CREATE INDEX idx_tanggal_berita ON berita(tanggal_publikasi);
CREATE INDEX idx_status_pengumuman ON pengumuman(status);
CREATE INDEX idx_tanggal_pengumuman ON pengumuman(tanggal_publikasi);

-- Sample data for doctors
INSERT INTO dokter (nama, spesialisasi, nip, no_str, telepon, email, alamat, status) VALUES
('Dr. Ahmad Santoso', 'Spesialis Jantung', '196201151987031001', 'STR-001-2020', '082100000001', 'ahmad@rsud.go.id', 'Jln. Merdeka No. 10, Kerinci', 'aktif'),
('Dr. Siti Aminah', 'Spesialis Anak', '196503251989032002', 'STR-002-2020', '082100000002', 'siti@rsud.go.id', 'Jln. Sudirman No. 15, Kerinci', 'aktif');

-- Sample data for jadwal dokter
INSERT INTO jadwal_dokter (dokter_id, hari, jam_mulai, jam_selesai, lokasi, kuota) VALUES
(1, 'Senin', '08:00:00', '12:00:00', 'Ruang Jantung A', 20),
(1, 'Rabu', '14:00:00', '17:00:00', 'Ruang Jantung A', 20),
(2, 'Senin', '09:00:00', '13:00:00', 'Ruang Anak B', 25),
(2, 'Kamis', '15:00:00', '18:00:00', 'Ruang Anak B', 25);

-- Sample news
INSERT INTO berita (judul, konten, tanggal_publikasi, penulis, status) VALUES
('Program Vaksinasi Gratis', 'RSUD mengadakan program vaksinasi gratis untuk seluruh masyarakat Kerinci setiap akhir pekan.', NOW(), 'Admin', 'publikasi'),
('Peluncuran Layanan Konsultasi Online', 'RSUD meluncurkan layanan konsultasi dokter online untuk kemudahan pasien.', NOW(), 'Admin', 'publikasi'),
('Fasilitas Baru Departemen Gawat Darurat', 'RSUD menambah fasilitas modern di departemen gawat darurat untuk pelayanan lebih baik.', NOW(), 'Admin', 'publikasi');
