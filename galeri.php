<?php
session_start();
require_once 'config/database.php';

// Check if admin is logged in
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Get all berita with gambar (photos from news)
$galeri_list = [];
$result = @$conn->query("SELECT id, judul, gambar, tanggal_publikasi, penulis FROM berita WHERE status = 'publikasi' AND gambar IS NOT NULL AND gambar != '' ORDER BY tanggal_publikasi DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $galeri_list[] = $row;
    }
}

// Function to format date
function format_tanggal($tanggal) {
    $date = new DateTime($tanggal);
    setlocale(LC_TIME, 'id_ID.UTF-8');
    return strftime('%d %B %Y', $date->getTimestamp());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Foto - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
            border-bottom: 3px solid #667eea;
        }
        .page-header h1 {
            font-weight: 700;
            margin: 0;
        }
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            background: white;
        }
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }
        .gallery-info {
            padding: 15px;
            background: white;
        }
        .gallery-title {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin: 0 0 8px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .gallery-date {
            font-size: 12px;
            color: #999;
            margin: 0;
        }
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        .overlay-content {
            text-align: center;
            color: white;
        }
        .overlay-content i {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .overlay-content p {
            margin: 0;
            font-size: 14px;
        }
        .gallery-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        .back-link {
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .page-header {
                padding: 30px 0;
            }
            .page-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Waktu di sudut kanan atas -->
    <div id="jam" class="waktu-display"></div>

    <!-- Header -->
    <header class="bg-gradient text-white py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="logo/Logo RSUD Kerkinci.png" alt="Logo RSUD" class="logo-header">
                </div>
                <div class="col-md-10">
                    <h1 class="header-title">Rumah Sakit Umum Daerah Kabupaten Kerinci</h1>
                    <p class="motto">Melayani dengan Sepenuh Hati</p>
                    <p class="website-info">rsudkabkerinci.go.id</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="sejarah.html">Sejarah Singkat</a></li>
                            <li><a class="dropdown-item" href="visi-misi.html">Visi Misi</a></li>
                            <li><a class="dropdown-item" href="struktur-organisasi.html">Struktur Organisasi</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Layanan</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php#rawat-inap">Rawat Inap</a></li>
                            <li><a class="dropdown-item" href="index.php#rawat-jalan">Rawat Jalan</a></li>
                            <li><a class="dropdown-item" href="pendaftaran.html">Pendaftaran Pasien Online</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Dokter</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="daftar_dokter.php">Daftar Nama Dokter</a></li>
                            <li><a class="dropdown-item" href="jadwal_dokter_view.php">Jadwal Dokter</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Info</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="info_publik.php">Informasi Publik</a></li>
                            <li><a class="dropdown-item" href="index.php#pengumuman">Pengumuman</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link active" href="galeri.php">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.html"><i class="fas fa-lock"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-images"></i> Galeri Foto Kegiatan</h1>
            <p class="mb-0">Dokumentasi kegiatan dan fasilitas RSUD Kabupaten Kerinci</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="back-link">
            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
            <?php if ($is_admin): ?>
            <a href="input_galeri.php" class="btn btn-success btn-sm ms-2">
                <i class="fas fa-plus"></i> Tambah Foto ke Galeri
            </a>
            <?php endif; ?>
        </div>

        <div class="gallery-container">
            <?php if (!empty($galeri_list)): ?>
                <div class="row">
                    <?php foreach ($galeri_list as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="gallery-item">
                            <a href="<?php echo htmlspecialchars($item['gambar']); ?>" data-lightbox="gallery" data-title="<?php echo htmlspecialchars($item['judul']); ?>">
                                <img src="<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['judul']); ?>" class="gallery-image">
                                <div class="gallery-overlay">
                                    <div class="overlay-content">
                                        <i class="fas fa-search-plus"></i>
                                        <p>Perbesar</p>
                                    </div>
                                </div>
                            </a>
                            <div class="gallery-info">
                                <h5 class="gallery-title" title="<?php echo htmlspecialchars($item['judul']); ?>">
                                    <?php echo htmlspecialchars($item['judul']); ?>
                                </h5>
                                <p class="gallery-date">
                                    <i class="fas fa-calendar-alt"></i> <?php echo format_tanggal($item['tanggal_publikasi']); ?>
                                    <?php if ($item['penulis']): ?>
                                        <br><i class="fas fa-user"></i> <?php echo htmlspecialchars($item['penulis']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 text-center">
                    <small class="text-muted">
                        Total <?php echo count($galeri_list); ?> foto dalam galeri
                    </small>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-image"></i>
                    <h4>Galeri Masih Kosong</h4>
                    <p>Belum ada foto yang ditampilkan dalam galeri. Foto akan otomatis muncul ketika berita dengan gambar dipublikasikan.</p>
                    <a href="index.php" class="btn btn-primary mt-3">
                        <i class="fas fa-home"></i> Kembali ke Beranda
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p>&copy; 2026 RSUD Kabupaten Kerinci. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
