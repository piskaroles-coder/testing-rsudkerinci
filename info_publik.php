<?php
require 'config/database.php';

// Get all public information
$info_list = [];
$stmt = $conn->prepare("SELECT * FROM informasi_publik WHERE status = 'aktif' ORDER BY created_at DESC LIMIT 50");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $info_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Publik - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Style.css">
    <style>
        .info-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 50px;
        }
        .info-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .info-hero p {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        .info-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            border-left: 5px solid #667eea;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .info-header {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        .info-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }
        .info-meta {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            font-size: 0.9rem;
            color: #666;
        }
        .info-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .info-meta i {
            color: #667eea;
        }
        .info-body {
            padding: 20px;
        }
        .info-content {
            line-height: 1.8;
            color: #555;
            margin-bottom: 15px;
        }
        .info-content p {
            margin-bottom: 10px;
        }
        .info-content ul,
        .info-content ol {
            margin-left: 20px;
        }
        .info-content li {
            margin-bottom: 8px;
        }
        .category-badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .category-badge.transparency {
            background: #FF6B6B;
        }
        .category-badge.regulation {
            background: #4ECDC4;
        }
        .category-badge.service {
            background: #45B7D1;
        }
        .category-badge.laporkan {
            background: #FFA502;
        }
        .read-more {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .read-more:hover {
            text-decoration: underline;
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
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        .filter-section select,
        .filter-section input {
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        @media (max-width: 768px) {
            .info-hero h1 {
                font-size: 1.75rem;
            }
            .info-meta {
                flex-wrap: wrap;
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
                            <li><a class="dropdown-item" href="sejarah.html">Sejarah Singkat</a></li>
                            <li><a class="dropdown-item" href="visi-misi.html">Visi Misi</a></li>
                            <li><a class="dropdown-item" href="struktur-organisasi.html">Struktur Organisasi</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Info</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="info_publik.php">Informasi Publik</a></li>
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
    <div class="info-hero">
        <div class="container">
            <h1><i class="fas fa-file-alt"></i> Informasi Publik</h1>
            <p>Transparansi dan akuntabilitas pelayanan kesehatan</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Filter Section -->
        <div class="filter-section">
            <h5 class="mb-3"><i class="fas fa-filter"></i> Filter Informasi</h5>
            <form method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari informasi...">
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" id="categoryFilter">
                            <option value="">-- Semua Kategori --</option>
                            <option value="transparency">Transparansi</option>
                            <option value="regulation">Regulasi & Kebijakan</option>
                            <option value="service">Layanan</option>
                            <option value="laporkan">Laporan & Pertanggungjawaban</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" onclick="applyFilter()">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info Cards -->
        <?php if (count($info_list) > 0): ?>
        <div id="infoContainer">
            <?php foreach ($info_list as $info): ?>
            <div class="info-card info-item" data-kategori="<?php echo htmlspecialchars($info['kategori']); ?>" data-judul="<?php echo htmlspecialchars($info['judul']); ?>">
                <!-- Category Badge -->
                <div style="padding: 15px 20px 0;">
                    <span class="category-badge <?php echo htmlspecialchars($info['kategori']); ?>">
                        <?php
                        $kategori_label = [
                            'transparency' => 'Transparansi',
                            'regulation' => 'Regulasi & Kebijakan',
                            'service' => 'Layanan',
                            'laporkan' => 'Laporan & Pertanggungjawaban'
                        ];
                        echo $kategori_label[$info['kategori']] ?? ucfirst($info['kategori']);
                        ?>
                    </span>
                </div>

                <!-- Header -->
                <div class="info-header">
                    <h3 class="info-title"><?php echo htmlspecialchars($info['judul']); ?></h3>
                    <div class="info-meta">
                        <div class="info-meta-item">
                            <i class="fas fa-calendar"></i>
                            <?php echo date('d M Y', strtotime($info['created_at'])); ?>
                        </div>
                        <div class="info-meta-item">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($info['penulis']); ?>
                        </div>
                        <?php if (!empty($info['sumber'])): ?>
                        <div class="info-meta-item">
                            <i class="fas fa-link"></i>
                            <span class="text-truncate"><?php echo htmlspecialchars($info['sumber']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Body -->
                <div class="info-body">
                    <div class="info-content">
                        <?php 
                        $isi = $info['isi'];
                        // Truncate content if too long
                        if (strlen(strip_tags($isi)) > 300) {
                            echo substr(strip_tags($isi), 0, 300) . '...';
                        } else {
                            echo $isi;
                        }
                        ?>
                    </div>
                    
                    <!-- Button -->
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#infoModal<?php echo $info['id']; ?>">
                        <i class="fas fa-expand"></i> Lihat Lengkap
                    </button>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="infoModal<?php echo $info['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo htmlspecialchars($info['judul']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="info-meta mb-3">
                                <div class="info-meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('d M Y', strtotime($info['created_at'])); ?>
                                </div>
                                <div class="info-meta-item">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($info['penulis']); ?>
                                </div>
                                <?php if (!empty($info['sumber'])): ?>
                                <div class="info-meta-item">
                                    <i class="fas fa-link"></i>
                                    <a href="<?php echo htmlspecialchars($info['sumber']); ?>" target="_blank" rel="noopener">
                                        Sumber
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="info-content">
                                <?php echo $info['isi']; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h4>Tidak ada informasi</h4>
            <p class="text-muted">Informasi publik tidak tersedia saat ini.</p>
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
        function applyFilter() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const categoryValue = document.getElementById('categoryFilter').value.toLowerCase();
            const items = document.querySelectorAll('.info-item');

            let visibleCount = 0;
            items.forEach(item => {
                const judul = item.getAttribute('data-judul').toLowerCase();
                const kategori = item.getAttribute('data-kategori').toLowerCase();

                const judulMatch = judul.includes(searchValue);
                const kategoriMatch = categoryValue === '' || kategori === categoryValue;

                if (judulMatch && kategoriMatch) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide empty message
            const container = document.getElementById('infoContainer');
            let emptyMsg = container.querySelector('.empty-msg');
            if (visibleCount === 0) {
                if (!emptyMsg) {
                    emptyMsg = document.createElement('div');
                    emptyMsg.className = 'empty-state empty-msg';
                    emptyMsg.innerHTML = '<i class="fas fa-search"></i><h4>Tidak ada hasil</h4><p class="text-muted">Coba dengan pencarian yang berbeda</p>';
                    container.appendChild(emptyMsg);
                }
                emptyMsg.style.display = '';
            } else if (emptyMsg) {
                emptyMsg.style.display = 'none';
            }
        }

        // Trigger filter on Enter
        document.getElementById('searchInput')?.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') applyFilter();
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
