<?php
require 'config/database.php';

// Get filter parameters
$filter_hari = $_GET['hari'] ?? '';
$filter_dokter = $_GET['dokter'] ?? '';
$tampilan = $_GET['tampilan'] ?? 'tabel'; // tabel, minggu, atau daftar

// Get unique doctors for filter
$dokter_options = [];
$stmt = $conn->prepare("SELECT DISTINCT d.id, d.nama FROM dokter d 
                        INNER JOIN jadwal_dokter jd ON d.id = jd.dokter_id 
                        WHERE d.status = 'aktif' 
                        ORDER BY d.nama");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $dokter_options[] = $row;
}

// Get jadwal with filters
$query = "SELECT jd.*, d.nama, d.spesialisasi, d.nip FROM jadwal_dokter jd
          INNER JOIN dokter d ON jd.dokter_id = d.id
          WHERE d.status = 'aktif'";

$params = [];
$types = '';

if (!empty($filter_hari)) {
    $query .= " AND jd.hari = ?";
    $params[] = $filter_hari;
    $types .= 's';
}

if (!empty($filter_dokter)) {
    $query .= " AND d.id = ?";
    $params[] = $filter_dokter;
    $types .= 'i';
}

$query .= " ORDER BY FIELD(jd.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jd.jam_mulai";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$jadwal_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Group by hari for week view
$jadwal_by_hari = [];
$hari_order = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
foreach ($hari_order as $hari) {
    $jadwal_by_hari[$hari] = [];
}
foreach ($jadwal_list as $jadwal) {
    $jadwal_by_hari[$jadwal['hari']][] = $jadwal;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Dokter - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Style.css">
    <style>
        .jadwal-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 50px;
        }
        .jadwal-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .jadwal-hero p {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        .filter-panel {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        .view-toggle {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .view-toggle .btn {
            flex: 1;
        }
        .view-toggle .btn.active {
            background: #667eea;
            color: white;
        }
        /* Table View */
        .jadwal-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        .jadwal-table table {
            margin-bottom: 0;
        }
        .jadwal-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .jadwal-table th {
            padding: 15px;
            font-weight: 600;
            border: none;
        }
        .jadwal-table td {
            padding: 15px;
            border-color: #eee;
            vertical-align: middle;
        }
        .jadwal-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .dokter-name {
            font-weight: 600;
            color: #667eea;
        }
        .spesialisasi-badge {
            display: inline-block;
            background: #e7f3ff;
            color: #667eea;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .hari-badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .waktu-jadwal {
            font-weight: 600;
            color: #333;
        }
        .lokasi-jadwal {
            color: #666;
            font-size: 0.95rem;
        }
        /* Week View */
        .week-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .hari-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .hari-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .hari-content {
            padding: 20px;
            max-height: 500px;
            overflow-y: auto;
        }
        .jadwal-item-week {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 6px;
        }
        .jadwal-item-week .dokter {
            font-weight: 600;
            color: #667eea;
            margin-bottom: 5px;
        }
        .jadwal-item-week .spesialisasi {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 8px;
        }
        .jadwal-item-week .waktu {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
            color: #333;
        }
        .jadwal-item-week .waktu i {
            color: #667eea;
        }
        .jadwal-item-week .lokasi {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 0.9rem;
        }
        .jadwal-item-week .lokasi i {
            color: #667eea;
        }
        .empty-hari {
            text-align: center;
            padding: 30px 15px;
            color: #999;
        }
        .empty-hari i {
            font-size: 2rem;
            margin-bottom: 10px;
            opacity: 0.5;
        }
        /* List View */
        .jadwal-list-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 20px;
            align-items: center;
            border-left: 5px solid #667eea;
        }
        .jadwal-list-item.alt {
            border-left-color: #764ba2;
        }
        .jadwal-list-left {
            min-width: 100px;
            text-align: center;
        }
        .jadwal-list-hari {
            font-size: 0.85rem;
            color: #999;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .jadwal-list-waktu {
            font-size: 1.3rem;
            font-weight: 700;
            color: #667eea;
        }
        .jadwal-list-content {
            flex: 1;
        }
        .jadwal-list-dokter {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        .jadwal-list-spec {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }
        .jadwal-list-info {
            display: flex;
            gap: 20px;
            font-size: 0.9rem;
        }
        .jadwal-list-info i {
            color: #667eea;
            width: 18px;
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
        @media (max-width: 768px) {
            .jadwal-hero h1 {
                font-size: 1.75rem;
            }
            .jadwal-list-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .jadwal-list-left {
                width: 100%;
                text-align: left;
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Dokter</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="daftar_dokter.php">Daftar Nama Dokter</a></li>
                            <li><a class="dropdown-item" href="jadwal_dokter_view.php">Jadwal Dokter</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="index.php#galeri">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact Us</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="jadwal-hero">
        <div class="container">
            <h1><i class="fas fa-calendar-alt"></i> Jadwal Dokter</h1>
            <p>Cek jadwal praktik dokter spesialis kami</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Filter Panel -->
        <div class="filter-panel">
            <h5 class="mb-3"><i class="fas fa-filter"></i> Filter Jadwal</h5>
            <form method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="filterHari" class="form-label">Hari</label>
                        <select class="form-select" id="filterHari" name="hari">
                            <option value="">-- Semua Hari --</option>
                            <option value="Senin" <?php echo $filter_hari === 'Senin' ? 'selected' : ''; ?>>Senin</option>
                            <option value="Selasa" <?php echo $filter_hari === 'Selasa' ? 'selected' : ''; ?>>Selasa</option>
                            <option value="Rabu" <?php echo $filter_hari === 'Rabu' ? 'selected' : ''; ?>>Rabu</option>
                            <option value="Kamis" <?php echo $filter_hari === 'Kamis' ? 'selected' : ''; ?>>Kamis</option>
                            <option value="Jumat" <?php echo $filter_hari === 'Jumat' ? 'selected' : ''; ?>>Jumat</option>
                            <option value="Sabtu" <?php echo $filter_hari === 'Sabtu' ? 'selected' : ''; ?>>Sabtu</option>
                            <option value="Minggu" <?php echo $filter_hari === 'Minggu' ? 'selected' : ''; ?>>Minggu</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filterDokter" class="form-label">Dokter</label>
                        <select class="form-select" id="filterDokter" name="dokter">
                            <option value="">-- Semua Dokter --</option>
                            <?php foreach ($dokter_options as $dok): ?>
                            <option value="<?php echo $dok['id']; ?>" <?php echo $filter_dokter == $dok['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dok['nama']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tampilan" class="form-label">Tampilan</label>
                        <select class="form-select" id="tampilan" name="tampilan">
                            <option value="tabel" <?php echo $tampilan === 'tabel' ? 'selected' : ''; ?>>Tabel</option>
                            <option value="minggu" <?php echo $tampilan === 'minggu' ? 'selected' : ''; ?>>Per Hari (Minggu)</option>
                            <option value="daftar" <?php echo $tampilan === 'daftar' ? 'selected' : ''; ?>>Daftar</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="jadwal_dokter_view.php" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Content based on view type -->
        <?php if (count($jadwal_list) > 0): ?>
            <?php if ($tampilan === 'tabel'): ?>
            <!-- Table View -->
            <div class="jadwal-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user-md"></i> Dokter</th>
                            <th><i class="fas fa-stethoscope"></i> Spesialisasi</th>
                            <th><i class="fas fa-calendar-day"></i> Hari</th>
                            <th><i class="fas fa-clock"></i> Jam</th>
                            <th><i class="fas fa-map-marker-alt"></i> Lokasi</th>
                            <th><i class="fas fa-users"></i> Kuota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jadwal_list as $jadwal): ?>
                        <tr>
                            <td><span class="dokter-name"><?php echo htmlspecialchars($jadwal['nama']); ?></span></td>
                            <td><span class="spesialisasi-badge"><?php echo htmlspecialchars($jadwal['spesialisasi']); ?></span></td>
                            <td><span class="hari-badge"><?php echo htmlspecialchars($jadwal['hari']); ?></span></td>
                            <td>
                                <span class="waktu-jadwal">
                                    <?php 
                                    $mulai = date('H:i', strtotime($jadwal['jam_mulai']));
                                    $selesai = date('H:i', strtotime($jadwal['jam_selesai']));
                                    echo $mulai . ' - ' . $selesai;
                                    ?>
                                </span>
                            </td>
                            <td><span class="lokasi-jadwal"><?php echo htmlspecialchars($jadwal['lokasi']); ?></span></td>
                            <td><?php echo !empty($jadwal['kuota']) ? $jadwal['kuota'] . ' pasien' : '-'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php elseif ($tampilan === 'minggu'): ?>
            <!-- Week View -->
            <div class="week-container">
                <?php foreach ($hari_order as $hari): ?>
                <div class="hari-card">
                    <div class="hari-header"><?php echo $hari; ?></div>
                    <div class="hari-content">
                        <?php if (count($jadwal_by_hari[$hari]) > 0): ?>
                            <?php foreach ($jadwal_by_hari[$hari] as $jadwal): ?>
                            <div class="jadwal-item-week">
                                <div class="dokter"><?php echo htmlspecialchars($jadwal['nama']); ?></div>
                                <div class="spesialisasi"><?php echo htmlspecialchars($jadwal['spesialisasi']); ?></div>
                                <div class="waktu">
                                    <i class="fas fa-clock"></i>
                                    <?php 
                                    $mulai = date('H:i', strtotime($jadwal['jam_mulai']));
                                    $selesai = date('H:i', strtotime($jadwal['jam_selesai']));
                                    echo $mulai . ' - ' . $selesai;
                                    ?>
                                </div>
                                <div class="lokasi">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($jadwal['lokasi']); ?>
                                </div>
                                <?php if (!empty($jadwal['kuota'])): ?>
                                <div class="lokasi" style="margin-top: 8px;">
                                    <i class="fas fa-users"></i>
                                    Kuota: <?php echo $jadwal['kuota']; ?> pasien
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="empty-hari">
                            <i class="fas fa-calendar-times"></i>
                            <p>Tidak ada jadwal</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php else: ?>
            <!-- List View -->
            <div style="margin-bottom: 40px;">
                <?php foreach ($jadwal_list as $index => $jadwal): ?>
                <div class="jadwal-list-item <?php echo $index % 2 === 0 ? '' : 'alt'; ?>">
                    <div class="jadwal-list-left">
                        <div class="jadwal-list-hari"><?php echo htmlspecialchars($jadwal['hari']); ?></div>
                        <div class="jadwal-list-waktu">
                            <?php 
                            $mulai = date('H:i', strtotime($jadwal['jam_mulai']));
                            echo $mulai;
                            ?>
                        </div>
                    </div>
                    <div class="jadwal-list-content">
                        <div class="jadwal-list-dokter"><?php echo htmlspecialchars($jadwal['nama']); ?></div>
                        <div class="jadwal-list-spec">
                            <i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($jadwal['spesialisasi']); ?>
                        </div>
                        <div class="jadwal-list-info">
                            <div>
                                <i class="fas fa-clock"></i>
                                <?php 
                                $mulai = date('H:i', strtotime($jadwal['jam_mulai']));
                                $selesai = date('H:i', strtotime($jadwal['jam_selesai']));
                                echo $mulai . ' - ' . $selesai . ' WIB';
                                ?>
                            </div>
                            <div>
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($jadwal['lokasi']); ?>
                            </div>
                            <?php if (!empty($jadwal['kuota'])): ?>
                            <div>
                                <i class="fas fa-users"></i>
                                <?php echo $jadwal['kuota']; ?> pasien
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <h4>Tidak ada jadwal</h4>
            <p class="text-muted">Jadwal dokter tidak ditemukan sesuai filter yang dipilih.</p>
            <a href="jadwal_dokter_view.php" class="btn btn-primary mt-3">
                <i class="fas fa-redo"></i> Reset Filter
            </a>
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
</body>
</html>
<?php $conn->close(); ?>
