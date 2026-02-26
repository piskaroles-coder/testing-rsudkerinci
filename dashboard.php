<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.html');
    exit;
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: login.html');
    exit;
}

// Koneksi database
require_once 'config/database.php';

$admin_username = $_SESSION['admin_username'];
$login_time = date('d/m/Y H:i:s', $_SESSION['login_time']);

// Hitung statistik dengan error handling
$stats = [
    'total_pasien' => 0,
    'total_kontak' => 0,
    'total_dokter' => 0,
    'total_berita' => 0,
    'total_pengumuman' => 0
];

try {
    // Hitung total pasien
    $result = @$conn->query("SELECT COUNT(*) as total FROM pasien");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total_pasien'] = $row['total'] ?? 0;
    }
    
    // Hitung total kontak
    $result = @$conn->query("SELECT COUNT(*) as total FROM kontak");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total_kontak'] = $row['total'] ?? 0;
    }
    
    // Hitung total dokter
    $result = @$conn->query("SELECT COUNT(*) as total FROM dokter");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total_dokter'] = $row['total'] ?? 0;
    }
    
    // Hitung total berita
    $result = @$conn->query("SELECT COUNT(*) as total FROM berita");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total_berita'] = $row['total'] ?? 0;
    }
    
    // Hitung total pengumuman
    $result = @$conn->query("SELECT COUNT(*) as total FROM pengumuman");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total_pengumuman'] = $row['total'] ?? 0;
    }
    
} catch (Exception $e) {
    // Stats sudah diinisialisasi dengan nilai 0
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ===== SIDEBAR ===== */
        .dashboard-sidebar {
            background: linear-gradient(180deg, #0047AB 0%, #003D99 100%);
            color: white;
            padding: 0;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.15);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header {
            padding: 30px 20px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.15);
            background: rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .sidebar-header h5 {
            margin: 0;
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
        }

        .sidebar-header i {
            margin-right: 10px;
            color: #FFD700;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            margin: 0;
        }

        .menu-item {
            padding: 0;
            margin: 0;
        }

        .menu-link {
            display: block;
            padding: 14px 25px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
        }

        .menu-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #FFD700;
            border-left-color: #FFD700;
            padding-left: 35px;
        }

        .menu-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .menu-item.active .menu-link {
            background: rgba(255, 215, 0, 0.1);
            color: #FFD700;
            border-left-color: #FFD700;
        }

        .divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.15);
            margin: 15px 0;
        }

        .logout-link {
            color: #ff6b6b !important;
            border-left-color: transparent !important;
        }

        .logout-link:hover {
            background: rgba(255, 107, 107, 0.15) !important;
            border-left-color: #ff6b6b !important;
            color: #ff8888 !important;
        }

        /* ===== MAIN CONTENT ===== */
        .dashboard-main {
            margin-left: 280px;
            padding: 30px;
            overflow-y: auto;
            min-height: 100vh;
        }

        /* ===== HEADER ===== */
        .dashboard-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #0047AB;
        }

        .header-content h1 {
            color: #0047AB;
            font-weight: 700;
            margin: 0 0 8px 0;
            font-size: 2.2rem;
        }

        .header-content p {
            margin: 0;
            color: #666;
            font-size: 1rem;
        }

        .header-info {
            text-align: right;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info i {
            font-size: 3rem;
            color: #4A90E2;
            opacity: 0.8;
        }

        .user-info small {
            color: #999;
            display: block;
            font-size: 0.85rem;
        }

        .user-info p {
            margin: 0;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            display: flex;
            gap: 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-top: 4px solid transparent;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-card:nth-child(1) { border-top-color: #667eea; }
        .stat-card:nth-child(2) { border-top-color: #f5576c; }
        .stat-card:nth-child(3) { border-top-color: #00f2fe; }
        .stat-card:nth-child(4) { border-top-color: #43e97b; }
        .stat-card:nth-child(5) { border-top-color: #fee140; }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            flex-shrink: 0;
        }

        .stat-icon.pasien { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-icon.kontak { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-icon.dokter { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-icon.berita { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .stat-icon.pengumuman { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

        .stat-content h3 {
            margin: 0;
            color: #0047AB;
            font-weight: 700;
            font-size: 1.8rem;
            line-height: 1;
        }

        .stat-content p {
            margin: 8px 0 0 0;
            color: #999;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* ===== QUICK ACTIONS ===== */
        .quick-actions {
            margin-bottom: 35px;
        }

        .quick-actions h2 {
            color: #0047AB;
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 1.6rem;
        }

        .action-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .action-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border-top: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 71, 171, 0.02);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.2);
            border-top-color: #4A90E2;
        }

        .action-card:hover::before {
            opacity: 1;
        }

        .action-icon {
            font-size: 2.8rem;
            color: #4A90E2;
            margin-bottom: 18px;
            transition: transform 0.3s ease;
        }

        .action-card:hover .action-icon {
            transform: scale(1.1);
        }

        .action-card h5 {
            color: #0047AB;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 1.15rem;
        }

        .action-card p {
            color: #999;
            font-size: 0.95rem;
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .action-card .btn {
            position: relative;
            z-index: 1;
        }

        /* ===== SYSTEM INFO ===== */
        .system-info {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 5px solid #4A90E2;
        }

        .system-info h2 {
            color: #0047AB;
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 1.6rem;
        }

        .info-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            padding: 18px;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 10px;
            border-left: 4px solid #4A90E2;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .info-item label {
            display: block;
            color: #999;
            font-size: 0.85rem;
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-item span {
            display: block;
            color: #333;
            font-weight: 700;
            font-size: 1rem;
            word-break: break-all;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            }

            .action-cards {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            body {
                margin: 0;
                padding: 0;
            }

            .dashboard-sidebar {
                position: fixed;
                left: -280px;
                transition: left 0.3s ease;
                width: 280px;
                height: 100vh;
                z-index: 2000;
            }

            .dashboard-sidebar.active {
                left: 0;
            }

            .dashboard-main {
                margin-left: 0;
                padding: 20px;
                width: 100%;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }

            .header-info {
                text-align: left;
                width: 100%;
            }

            .dashboard-header h1 {
                font-size: 1.75rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .stat-card {
                gap: 15px;
            }

            .stat-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .action-cards {
                grid-template-columns: 1fr;
            }

            .info-box {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .dashboard-main {
                padding: 15px;
            }

            .dashboard-header {
                padding: 20px;
            }

            .quick-actions h2,
            .system-info h2 {
                font-size: 1.3rem;
            }

            .action-card {
                padding: 20px;
            }

            .stat-content h3 {
                font-size: 1.5rem;
            }
        }

        /* ===== SCROLLBAR ===== */
        .dashboard-main::-webkit-scrollbar {
            width: 8px;
        }

        .dashboard-main::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }

        .dashboard-main::-webkit-scrollbar-thumb {
            background: rgba(0, 71, 171, 0.3);
            border-radius: 4px;
        }

        .dashboard-main::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 71, 171, 0.5);
        }
    </style>
</head>
<body class="dashboard-page">
    <!-- Sidebar Navigation -->
    <nav class="dashboard-sidebar">
        <div class="sidebar-header">
            <h5><i class="fas fa-hospital-user"></i> Admin Panel</h5>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-item active">
                <a href="#dashboard" class="menu-link">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="menu-item">
                <a href="input_dokter.php" class="menu-link">
                    <i class="fas fa-user-md"></i> Input Dokter
                </a>
            </li>
            <li class="menu-item">
                <a href="input_jadwal_dokter.php" class="menu-link">
                    <i class="fas fa-calendar-alt"></i> Input Jadwal Dokter
                </a>
            </li>
            <li class="menu-item">
                <a href="input_berita.php" class="menu-link">
                    <i class="fas fa-newspaper"></i> Input Berita
                </a>
            </li>
            <li class="menu-item">
                <a href="input_pengumuman.php" class="menu-link">
                    <i class="fas fa-bullhorn"></i> Input Pengumuman
                </a>
            </li>
            <li class="menu-item">
                <a href="input_info_publik.php" class="menu-link">
                    <i class="fas fa-file-alt"></i> Input Informasi Publik
                </a>
            </li>
            <li class="menu-item">
                <a href="admin_data.php" class="menu-link" target="_blank">
                    <i class="fas fa-database"></i> Kelola Data
                </a>
            </li>
            <li class="menu-item">
                <a href="api_testing.php" class="menu-link" target="_blank">
                    <i class="fas fa-flask"></i> API Testing
                </a>
            </li>
            <li class="divider"></li>
            <li class="menu-item">
                <a href="?action=logout" class="menu-link logout-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-main">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <h1>Dashboard Admin</h1>
                <p class="text-muted">Selamat datang kembali, <strong><?php echo htmlspecialchars($admin_username); ?></strong></p>
            </div>
            <div class="header-info">
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <small>Login:</small>
                        <p><?php echo $login_time; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon pasien">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo intval($stats['total_pasien']); ?></h3>
                    <p>Total Pasien</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon kontak">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo intval($stats['total_kontak']); ?></h3>
                    <p>Pesan Kontak</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon dokter">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo intval($stats['total_dokter']); ?></h3>
                    <p>Jadwal Dokter</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon berita">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo intval($stats['total_berita']); ?></h3>
                    <p>Berita</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pengumuman">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo intval($stats['total_pengumuman']); ?></h3>
                    <p>Pengumuman</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2>Akses Cepat</h2>
            <div class="action-cards">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h5>Input Dokter</h5>
                    <p>Kelola data dokter di rumah sakit</p>
                    <a href="input_dokter.php" class="btn btn-sm btn-primary">
                        Akses <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h5>Jadwal Dokter</h5>
                    <p>Atur jadwal praktik dokter di berbagai hari dan jam</p>
                    <a href="input_jadwal_dokter.php" class="btn btn-sm btn-primary">
                        Akses <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h5>Input Berita</h5>
                    <p>Tambah atau edit berita untuk ditampilkan di website</p>
                    <a href="input_berita.php" class="btn btn-sm btn-primary">
                        Akses <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h5>Input Pengumuman</h5>
                    <p>Tambah atau edit pengumuman untuk masyarakat</p>
                    <a href="input_pengumuman.php" class="btn btn-sm btn-primary">
                        Akses <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5>Input Informasi Publik</h5>
                    <p>Tambah atau edit informasi publik untuk transparansi</p>
                    <a href="input_info_publik.php" class="btn btn-sm btn-primary">
                        Akses <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h5>Lihat Data Pasien</h5>
                    <p>Kelola dan lihat data pendaftaran pasien secara real-time</p>
                    <a href="admin_data.php" class="btn btn-sm btn-primary" target="_blank">
                        Akses <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h5>Uji API</h5>
                    <p>Test endpoint API dan validasi data sistem</p>
                    <a href="api_testing.php" class="btn btn-sm btn-primary" target="_blank">
                        Akses <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h5>Database SQL</h5>
                    <p>Download backup dan restore database sistem</p>
                    <a href="config/database.sql" class="btn btn-sm btn-primary" download>
                        Download <i class="fas fa-download"></i>
                    </a>
                </div>

                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5>Dokumentasi</h5>
                    <p>Baca dokumentasi lengkap sistem dan API</p>
                    <a href="README.md" class="btn btn-sm btn-primary" target="_blank">
                        Baca <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="system-info">
            <h2>Informasi Sistem</h2>
            <div class="info-box">
                <div class="info-item">
                    <label>Server Software:</label>
                    <span><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <label>PHP Version:</label>
                    <span><?php echo phpversion(); ?></span>
                </div>
                <div class="info-item">
                    <label>MySQL Version:</label>
                    <span><?php echo htmlspecialchars($conn->server_info); ?></span>
                </div>
                <div class="info-item">
                    <label>Current Time:</label>
                    <span><?php echo date('d/m/Y H:i:s'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-logout setelah 30 menit tidak ada aktivitas
        let inactivityTimer;
        const INACTIVITY_TIMEOUT = 30 * 60 * 1000; // 30 menit

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                if (confirm('Sesi Anda telah berakhir karena tidak ada aktivitas. Klik OK untuk login kembali.')) {
                    window.location.href = '?action=logout';
                }
            }, INACTIVITY_TIMEOUT);
        }

        // Reset timer on aktivitas
        document.addEventListener('mousemove', resetInactivityTimer);
        document.addEventListener('keypress', resetInactivityTimer);
        document.addEventListener('click', resetInactivityTimer);
        document.addEventListener('scroll', resetInactivityTimer);

        // Initial timer
        resetInactivityTimer();

        // Sidebar toggle untuk mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.dashboard-sidebar');
            const main = document.querySelector('.dashboard-main');

            // Toggle sidebar on mobile
            const toggleSidebar = () => {
                sidebar.classList.toggle('active');
            };

            // Close sidebar when link is clicked
            document.querySelectorAll('.menu-link').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('active');
                    }
                });
            });
        });
    </script>
</body>
</html>
