<?php
require 'config/database.php';

// Get all active doctors
$dokter_list = [];
$stmt = $conn->prepare("SELECT d.*, COUNT(jd.id) as total_jadwal FROM dokter d 
                        LEFT JOIN jadwal_dokter jd ON d.id = jd.dokter_id 
                        WHERE d.status = 'aktif' 
                        GROUP BY d.id 
                        ORDER BY d.nama");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $dokter_list[] = $row;
}

// Get schedules for each doctor
$schedules = [];
$stmt = $conn->prepare("SELECT * FROM jadwal_dokter WHERE dokter_id = ? ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam_mulai");
foreach ($dokter_list as &$dokter) {
    $stmt->bind_param("i", $dokter['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $dokter['jadwal'] = [];
    while ($row = $res->fetch_assoc()) {
        $dokter['jadwal'][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Dokter - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Style.css">
    <style>
        .dokter-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 50px;
        }
        .dokter-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .dokter-hero p {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        .dokter-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .dokter-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }
        .dokter-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        .dokter-avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 15px;
        }
        .dokter-nama {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .dokter-spesialisasi {
            font-size: 0.95rem;
            opacity: 0.9;
            font-style: italic;
        }
        .dokter-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .dokter-info {
            margin-bottom: 20px;
        }
        .info-label {
            font-weight: 600;
            color: #667eea;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            margin-bottom: 12px;
        }
        .info-value i {
            color: #667eea;
            width: 20px;
            margin-right: 8px;
        }
        .jadwal-title {
            font-weight: 600;
            color: #667eea;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 15px;
            margin-bottom: 12px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .jadwal-item {
            background: #f8f9fa;
            border-left: 3px solid #667eea;
            padding: 10px 12px;
            margin-bottom: 8px;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .jadwal-hari {
            font-weight: 600;
            color: #667eea;
        }
        .jadwal-waktu {
            color: #666;
            font-size: 0.85rem;
        }
        .jadwal-lokasi {
            color: #999;
            font-size: 0.85rem;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .btn-contact {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s ease;
            margin-top: auto;
            display: inline-block;
            text-align: center;
        }
        .btn-contact:hover {
            background: #764ba2;
            color: white;
            text-decoration: none;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 40px;
        }
        .filter-section input,
        .filter-section select {
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        @media (max-width: 768px) {
            .dokter-hero h1 {
                font-size: 1.75rem;
            }
            .dokter-hero p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php#beranda">
                <i class="fas fa-hospital"></i> RSUD Kabupaten Kerinci
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php#beranda">Beranda</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php#rawat-inap">Rawat Inap</a></li>
                            <li><a class="dropdown-item" href="index.php#rawat-jalan">Rawat Jalan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Info</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php#info-publik">Informasi Publik</a></li>
                            <li><a class="dropdown-item" href="index.php#pengumuman">Pengumuman</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="index.php#galeri">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact Us</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="dokter-hero">
        <div class="container">
            <h1><i class="fas fa-stethoscope"></i> Daftar Dokter</h1>
            <p>Tim medis profesional siap melayani kesehatan Anda</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari nama dokter...">
                </div>
                <div class="col-md-6">
                    <select class="form-select" id="spesialisasiFilter">
                        <option value="">-- Semua Spesialisasi --</option>
                        <?php
                        // Get unique specializations
                        $spec_result = $conn->query("SELECT DISTINCT spesialisasi FROM dokter WHERE status = 'aktif' ORDER BY spesialisasi");
                        while ($row = $spec_result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['spesialisasi']) . '">' . htmlspecialchars($row['spesialisasi']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Dokter Cards -->
        <?php if (count($dokter_list) > 0): ?>
        <div class="row" id="dokterContainer">
            <?php foreach ($dokter_list as $dokter): ?>
            <div class="col-md-6 col-lg-4 mb-4 dokter-item" data-nama="<?php echo htmlspecialchars($dokter['nama']); ?>" data-spesialisasi="<?php echo htmlspecialchars($dokter['spesialisasi']); ?>">
                <div class="dokter-card">
                    <!-- Header -->
                    <div class="dokter-header">
                        <div class="dokter-avatar">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="dokter-nama"><?php echo htmlspecialchars($dokter['nama']); ?></div>
                        <div class="dokter-spesialisasi"><?php echo htmlspecialchars($dokter['spesialisasi']); ?></div>
                    </div>

                    <!-- Content -->
                    <div class="dokter-content">
                        <!-- NIP -->
                        <div class="dokter-info">
                            <div class="info-label"><i class="fas fa-id-card"></i> NIP</div>
                            <div class="info-value"><?php echo htmlspecialchars($dokter['nip']); ?></div>
                        </div>

                        <!-- Kontak -->
                        <?php if (!empty($dokter['telepon'])): ?>
                        <div class="dokter-info">
                            <div class="info-label"><i class="fas fa-phone"></i> Telepon</div>
                            <div class="info-value">
                                <i class="fas fa-phone"></i> 
                                <a href="tel:<?php echo htmlspecialchars($dokter['telepon']); ?>" style="text-decoration: none; color: #333;">
                                    <?php echo htmlspecialchars($dokter['telepon']); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($dokter['email'])): ?>
                        <div class="dokter-info">
                            <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                            <div class="info-value">
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?php echo htmlspecialchars($dokter['email']); ?>" style="text-decoration: none; color: #333;">
                                    <?php echo htmlspecialchars($dokter['email']); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Jadwal -->
                        <?php if (count($dokter['jadwal']) > 0): ?>
                        <div class="jadwal-title">
                            <i class="fas fa-calendar-alt"></i> Jadwal Praktik (<?php echo count($dokter['jadwal']); ?>)
                        </div>
                        <?php foreach ($dokter['jadwal'] as $jadwal): ?>
                        <div class="jadwal-item">
                            <div class="jadwal-hari">
                                <i class="fas fa-calendar-day"></i> <?php echo htmlspecialchars($jadwal['hari']); ?>
                            </div>
                            <div class="jadwal-waktu">
                                <i class="fas fa-clock"></i> 
                                <?php 
                                $mulai = date('H:i', strtotime($jadwal['jam_mulai']));
                                $selesai = date('H:i', strtotime($jadwal['jam_selesai']));
                                echo $mulai . ' - ' . $selesai . ' WIB';
                                ?>
                            </div>
                            <div class="jadwal-lokasi">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($jadwal['lokasi']); ?>
                            </div>
                            <?php if (!empty($jadwal['kuota'])): ?>
                            <div class="jadwal-lokasi">
                                <i class="fas fa-users"></i> Kuota: <?php echo $jadwal['kuota']; ?> pasien
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="jadwal-title">
                            <i class="fas fa-calendar-alt"></i> Jadwal Praktik
                        </div>
                        <div class="text-muted" style="font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i> Jadwal belum tersedia
                        </div>
                        <?php endif; ?>

                        <!-- Contact Button -->
                        <a href="index.php#contact" class="btn-contact">
                            <i class="fas fa-envelope"></i> Hubungi Dokter
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-user-md"></i>
            <h4>Tidak ada dokter</h4>
            <p class="text-muted">Data dokter tidak tersedia saat ini.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2026 RSUD Kabupaten Kerinci. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter functionality
        document.getElementById('searchInput').addEventListener('keyup', filterDokter);
        document.getElementById('spesialisasiFilter').addEventListener('change', filterDokter);

        function filterDokter() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const spesialisasiValue = document.getElementById('spesialisasiFilter').value.toLowerCase();
            const items = document.querySelectorAll('.dokter-item');

            let visibleCount = 0;
            items.forEach(item => {
                const nama = item.getAttribute('data-nama').toLowerCase();
                const spesialisasi = item.getAttribute('data-spesialisasi').toLowerCase();

                const namaMatch = nama.includes(searchValue);
                const spesialisasiMatch = spesialisasiValue === '' || spesialisasi === spesialisasiValue;

                if (namaMatch && spesialisasiMatch) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show empty message if no results
            const container = document.getElementById('dokterContainer');
            let emptyMsg = container.querySelector('.empty-msg');
            if (visibleCount === 0) {
                if (!emptyMsg) {
                    emptyMsg = document.createElement('div');
                    emptyMsg.className = 'empty-state empty-msg col-12';
                    emptyMsg.innerHTML = '<i class="fas fa-search"></i><h4>Tidak ada hasil</h4><p class="text-muted">Coba dengan pencarian yang berbeda</p>';
                    container.appendChild(emptyMsg);
                }
                emptyMsg.style.display = '';
            } else if (emptyMsg) {
                emptyMsg.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
