<?php
// Koneksi database
require 'config/database.php';

// SQL untuk membuat tabel dokter
$sql_dokter = "CREATE TABLE IF NOT EXISTS dokter (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// SQL untuk update jadwal_dokter - rename column dan add foreign key
$sql_jadwal = "ALTER TABLE jadwal_dokter 
    DROP COLUMN IF EXISTS nama_dokter,
    DROP COLUMN IF EXISTS spesialisasi,
    ADD COLUMN IF NOT EXISTS dokter_id INT,
    ADD COLUMN IF NOT EXISTS kuota INT,
    MODIFY COLUMN hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
    ADD CONSTRAINT fk_dokter_jadwal FOREIGN KEY (dokter_id) REFERENCES dokter(id) ON DELETE CASCADE;";

// SQL untuk membuat index
$sql_index_dokter = "CREATE INDEX IF NOT EXISTS idx_nip_dokter ON dokter(nip);";
$sql_index_dokter_status = "CREATE INDEX IF NOT EXISTS idx_status_dokter ON dokter(status);";
$sql_index_jadwal = "CREATE INDEX IF NOT EXISTS idx_dokter_jadwal ON jadwal_dokter(dokter_id);";
$sql_index_hari = "CREATE INDEX IF NOT EXISTS idx_hari_jadwal ON jadwal_dokter(hari);";

// Insert sample data dokter
$sql_sample_dokter = "INSERT IGNORE INTO dokter (id, nama, spesialisasi, nip, no_str, telepon, email, alamat, status) VALUES
(1, 'Dr. Ahmad Santoso', 'Spesialis Jantung', '196201151987031001', 'STR-001-2020', '082100000001', 'ahmad@rsud.go.id', 'Jln. Merdeka No. 10, Kerinci', 'aktif'),
(2, 'Dr. Siti Aminah', 'Spesialis Anak', '196503251989032002', 'STR-002-2020', '082100000002', 'siti@rsud.go.id', 'Jln. Sudirman No. 15, Kerinci', 'aktif');";

try {
    // Jalankan SQL untuk membuat tabel dokter
    if ($conn->query($sql_dokter) === TRUE) {
        echo "✓ Tabel dokter berhasil dibuat atau sudah ada<br>";
    } else {
        echo "✗ Error membuat tabel dokter: " . $conn->error . "<br>";
    }

    // Insert sample data dokter
    if ($conn->query($sql_sample_dokter) === TRUE) {
        echo "✓ Sample data dokter berhasil ditambahkan<br>";
    } else {
        echo "✗ Error menambahkan sample data: " . $conn->error . "<br>";
    }

    // Update tabel jadwal_dokter
    if ($conn->query($sql_jadwal) === TRUE) {
        echo "✓ Tabel jadwal_dokter berhasil diupdate<br>";
    } else {
        echo "✗ Error update jadwal_dokter: " . $conn->error . "<br>";
    }

    // Buat indexes
    if ($conn->query($sql_index_dokter) === TRUE) {
        echo "✓ Index idx_nip_dokter berhasil dibuat<br>";
    } else {
        echo "✗ Error membuat index: " . $conn->error . "<br>";
    }

    if ($conn->query($sql_index_dokter_status) === TRUE) {
        echo "✓ Index idx_status_dokter berhasil dibuat<br>";
    } else {
        echo "✗ Error membuat index: " . $conn->error . "<br>";
    }

    if ($conn->query($sql_index_jadwal) === TRUE) {
        echo "✓ Index idx_dokter_jadwal berhasil dibuat<br>";
    } else {
        echo "✗ Error membuat index: " . $conn->error . "<br>";
    }

    if ($conn->query($sql_index_hari) === TRUE) {
        echo "✓ Index idx_hari_jadwal berhasil dibuat<br>";
    } else {
        echo "✗ Error membuat index: " . $conn->error . "<br>";
    }

    // Update jadwal_dokter yang sudah ada dengan dokter_id
    $update_jadwal = "UPDATE jadwal_dokter SET dokter_id = 1 WHERE nama_dokter = 'Dr. Ahmad Santoso' AND dokter_id IS NULL;";
    if ($conn->query($update_jadwal) === TRUE) {
        echo "✓ Jadwal dokter diupdate dengan dokter_id<br>";
    } else {
        echo "✗ Error update jadwal: " . $conn->error . "<br>";
    }

    $update_jadwal2 = "UPDATE jadwal_dokter SET dokter_id = 2 WHERE nama_dokter = 'Dr. Siti Aminah' AND dokter_id IS NULL;";
    if ($conn->query($update_jadwal2) === TRUE) {
        echo "✓ Jadwal dokter Siti Aminah diupdate<br>";
    } else {
        echo "✗ Error update jadwal: " . $conn->error . "<br>";
    }

    echo "<br><strong>✓ Setup database berhasil!</strong><br>";
    echo "<a href='dashboard.php'>← Kembali ke Dashboard</a>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
