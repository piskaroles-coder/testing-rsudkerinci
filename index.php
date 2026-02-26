<?php
session_start();

require_once 'config/database.php';

// Check if admin is logged in
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Get latest published news (berita)
$berita_list = [];
$result = @$conn->query("SELECT id, judul, konten, gambar, tanggal_publikasi, penulis FROM berita WHERE status = 'publikasi' ORDER BY tanggal_publikasi DESC LIMIT 3");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $berita_list[] = $row;
    }
}

// Get latest announcements (pengumuman)
$pengumuman_list = [];
$result = @$conn->query("SELECT id, judul, isi, tanggal_publikasi, penulis FROM pengumuman WHERE status = 'aktif' ORDER BY tanggal_publikasi DESC LIMIT 2");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pengumuman_list[] = $row;
    }
}

// Function to format date
function format_tanggal($tanggal) {
    $date = new DateTime($tanggal);
    setlocale(LC_TIME, 'id_ID.UTF-8');
    return strftime('%d %B %Y', $date->getTimestamp());
}

// Function to strip HTML tags and limit text
function truncate_text($text, $limit = 150) {
    $text = strip_tags($text);
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }
    return $text;
}

// Get jadwal dokter (Doctor schedules)
$jadwal_list = [];
$result = @$conn->query("SELECT jd.*, d.nama, d.spesialisasi FROM jadwal_dokter jd
                        INNER JOIN dokter d ON jd.dokter_id = d.id
                        WHERE d.status = 'aktif'
                        ORDER BY FIELD(jd.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jd.jam_mulai
                        LIMIT 6");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jadwal_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSUD Kabupaten Kerinci - Melayani dengan Sepenuh Hati</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
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
                            <li><a class="dropdown-item" href="#rawat-inap">Rawat Inap</a></li>
                            <li><a class="dropdown-item" href="#rawat-jalan">Rawat Jalan</a></li>
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
                            <li><a class="dropdown-item" href="#pengumuman">Pengumuman</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="galeri.php">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.html"><i class="fas fa-lock"></i> Admin</a></li>
                </ul>
                <form class="d-flex ms-auto search-form" role="search">
                    <input class="form-control search-input" type="search" placeholder="Cari di website..." aria-label="Search">
                    <button class="btn-search" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Beranda -->
    <section id="beranda" class="hero-section text-white text-center py-3">
        <div class="container">
        </div>
    </section>

    <!-- Berita Terbaru -->
    <section id="berita" class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-4"><i class="fas fa-newspaper"></i> Berita Terbaru</h2>
            <div class="row">
                <?php if (!empty($berita_list)): ?>
                    <?php foreach ($berita_list as $berita): ?>
                    <div class="col-md-4 mb-4">
                        <div class="news-card">
                            <?php if ($berita['gambar']): ?>
                                <img src="<?php echo htmlspecialchars($berita['gambar']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>" class="img-fluid news-img">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x200/4A90E2/ffffff?text=Berita" alt="Placeholder" class="img-fluid news-img">
                            <?php endif; ?>
                            <div class="news-content">
                                <h5><?php echo htmlspecialchars($berita['judul']); ?></h5>
                                <p class="news-date">
                                    <i class="fas fa-calendar"></i> <?php echo format_tanggal($berita['tanggal_publikasi']); ?>
                                    <?php if ($berita['penulis']): ?>
                                        | <i class="fas fa-user"></i> <?php echo htmlspecialchars($berita['penulis']); ?>
                                    <?php endif; ?>
                                </p>
                                <p><?php echo truncate_text($berita['konten'], 120); ?></p>
                                <a href="#berita-detail-<?php echo $berita['id']; ?>" class="news-link" data-bs-toggle="modal" data-bs-target="#beritaModal<?php echo $berita['id']; ?>">Baca Selengkapnya →</a>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail Berita -->
                    <div class="modal fade" id="beritaModal<?php echo $berita['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?php echo htmlspecialchars($berita['judul']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?php if ($berita['gambar']): ?>
                                        <img src="<?php echo htmlspecialchars($berita['gambar']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>" class="img-fluid mb-3">
                                    <?php endif; ?>
                                    <p class="text-muted">
                                        <i class="fas fa-calendar"></i> <?php echo format_tanggal($berita['tanggal_publikasi']); ?>
                                        <?php if ($berita['penulis']): ?>
                                            | <i class="fas fa-user"></i> <?php echo htmlspecialchars($berita['penulis']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <div class="news-detail-content">
                                        <?php echo $berita['konten']; ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Belum ada berita yang dipublikasikan.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($is_admin): ?>
            <div class="text-center mt-4">
                <a href="input_berita.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Berita
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Pengumuman Terbaru -->
    <section id="pengumuman-section" class="py-5">
        <div class="container">
            <h2 class="mb-4"><i class="fas fa-bullhorn"></i> Pengumuman Terbaru</h2>
            <div class="row">
                <?php if (!empty($pengumuman_list)): ?>
                    <?php foreach ($pengumuman_list as $pengumuman): ?>
                    <div class="col-md-6 mb-4">
                        <div class="pengumuman-card">
                            <div class="pengumuman-header">
                                <h5><?php echo htmlspecialchars($pengumuman['judul']); ?></h5>
                                <p class="pengumuman-date">
                                    <i class="fas fa-calendar"></i> <?php echo format_tanggal($pengumuman['tanggal_publikasi']); ?>
                                    <?php if ($pengumuman['penulis']): ?>
                                        | <i class="fas fa-user"></i> <?php echo htmlspecialchars($pengumuman['penulis']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="pengumuman-content">
                                <?php echo truncate_text($pengumuman['isi'], 150); ?>
                            </div>
                            <a href="#" class="pengumuman-link" data-bs-toggle="modal" data-bs-target="#pengumumanModal<?php echo $pengumuman['id']; ?>">Lihat Selengkapnya →</a>
                        </div>
                    </div>

                    <!-- Modal Detail Pengumuman -->
                    <div class="modal fade" id="pengumumanModal<?php echo $pengumuman['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?php echo htmlspecialchars($pengumuman['judul']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted">
                                        <i class="fas fa-calendar"></i> <?php echo format_tanggal($pengumuman['tanggal_publikasi']); ?>
                                        <?php if ($pengumuman['penulis']): ?>
                                            | <i class="fas fa-user"></i> <?php echo htmlspecialchars($pengumuman['penulis']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <div class="pengumuman-detail-content">
                                        <?php echo $pengumuman['isi']; ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Belum ada pengumuman.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($is_admin): ?>
            <div class="text-center mt-4">
                <a href="input_pengumuman.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Pengumuman
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Highlight Galeri Kegiatan -->
    <section id="highlight-galeri" class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-4"><i class="fas fa-images"></i> Galeri Kegiatan Terbaru</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="gallery-highlight">
                        <img src="https://via.placeholder.com/600x400/87CEEB/000000?text=Kegiatan+Vaksinasi" alt="Vaksinasi" class="img-fluid gallery-highlight-img">
                        <div class="gallery-overlay">
                            <h4>Program Vaksinasi Massal</h4>
                            <p>Kegiatan vaksinasi untuk meningkatkan imunitas masyarakat</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="gallery-highlight">
                        <img src="https://via.placeholder.com/600x400/25D366/000000?text=Pelatihan+Medis" alt="Pelatihan" class="img-fluid gallery-highlight-img">
                        <div class="gallery-overlay">
                            <h4>Pelatihan Tenaga Medis</h4>
                            <p>Peningkatan keterampilan dan pengetahuan tim medis</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="gallery-highlight">
                        <img src="https://via.placeholder.com/600x400/FF6B6B/000000?text=Kunjungan+Pasien" alt="Pasien" class="img-fluid gallery-highlight-img">
                        <div class="gallery-overlay">
                            <h4>Kunjungan Pasien Sembuh</h4>
                            <p>Kepuasan pasien adalah prioritas utama kami</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="gallery-highlight">
                        <img src="https://via.placeholder.com/600x400/FFD700/000000?text=Acara+Kesehatan" alt="Acara" class="img-fluid gallery-highlight-img">
                        <div class="gallery-overlay">
                            <h4>Sosialisasi Kesehatan Masyarakat</h4>
                            <p>Edukasi kesehatan untuk masyarakat Kerinci</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan -->
    <section id="layanan" class="py-5">
        <div class="container">
            <h2><i class="fas fa-hospital"></i> Layanan Kami</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="service-card">
                        <i class="fas fa-hospital-user"></i>
                        <h5>Rawat Inap</h5>
                        <p>Fasilitas perawatan dengan kamar nyaman dan pengawasan medis 24 jam untuk pasien yang membutuhkan rawat inap.</p>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="service-card">
                        <i class="fas fa-stethoscope"></i>
                        <h5>Rawat Jalan</h5>
                        <p>Layanan konsultasi dan pemeriksaan kesehatan tanpa perlu rawat inap dengan dokter spesialis berpengalaman.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="pendaftaran.html" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus"></i> Daftar Pasien Online
                </a>
            </div>
        </div>
    </section>

    <!-- Dokter & Jadwal -->
    <section id="dokter" class="py-5 bg-light">
        <div class="container">
            <h2><i class="fas fa-stethoscope"></i> Jadwal Dokter</h2>
            <p class="mb-4">Lihat jadwal dokter spesialis RSUD Kabupaten Kerinci</p>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Dokter</th>
                            <th>Spesialisasi</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Ruangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jadwal_list)): ?>
                            <?php foreach ($jadwal_list as $jadwal): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($jadwal['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($jadwal['spesialisasi']); ?></td>
                                    <td><?php echo htmlspecialchars($jadwal['hari']); ?></td>
                                    <td><?php echo htmlspecialchars($jadwal['jam_mulai']) . ' - ' . htmlspecialchars($jadwal['jam_selesai']); ?></td>
                                    <td><?php echo htmlspecialchars($jadwal['ruangan']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Data jadwal dokter tidak tersedia</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center mt-4">
                <a href="jadwal_dokter_view.php" class="btn btn-primary me-2">
                    <i class="fas fa-calendar"></i> Lihat Jadwal Lengkap
                </a>
                <?php if ($is_admin): ?>
                <a href="input_jadwal_dokter.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Jadwal Dokter
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact Us -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <h2><i class="fas fa-envelope"></i> Contact Us</h2>
            <div class="row mb-5">
                <div class="col-md-4 text-center">
                    <div class="contact-card">
                        <div class="contact-icon whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h4>WhatsApp</h4>
                        <a href="https://wa.me/6281371740005" target="_blank" class="contact-link">
                            081371740005
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="contact-card">
                        <div class="contact-icon email">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Email</h4>
                        <a href="mailto:rsudkabkerinci@gmail.com" class="contact-link">
                            rsudkabkerinci@gmail.com
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="contact-card">
                        <div class="contact-icon phone">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4>Telepon</h4>
                        <a href="tel:(0741)123456" class="contact-link">
                            (0741) 123456
                        </a>
                    </div>
                </div>
            </div>
            <form id="contactForm" method="POST" action="api/handle_kontak.php">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Pesan</label>
                    <textarea class="form-control" id="message" name="pesan" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </form>
            <div class="mt-4">
                <h3 class="mb-3">Lokasi Kami</h3>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.844!2d101.3376080657993!3d-1.9387556880497832!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31b32c5c5c5c5c5d:0x5e5e5e5e5e5e5e5e!2sRSUD%20Kabupaten%20Kerinci!5e0!3m2!1sid!2sid!4v1706524800000" width="100%" height="400" style="border:0; border-radius: 10px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-3">
        <div class="container text-center">
            <p>&copy; 2026 RSUD Kabupaten Kerinci | Crz_Algolirsm. Semua Hak Dilindungi.</p>
            <p>Alamat: Jln. Jalur 2 Bukit Tengah, Desa Sungai Pegeh, Siulak, Kerinci - Jambi 37162 | Tel: (0741) 123456</p>
            <div>
                <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>

<?php
$conn->close();
?>
