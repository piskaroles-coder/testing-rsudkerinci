<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.html');
    exit;
}

// Simple Admin Panel untuk melihat data - Database View
require_once 'config/database.php';

// Check method
$table = $_GET['table'] ?? 'pasien';
$allowed_tables = ['pasien', 'dokter', 'kontak', 'jadwal_dokter', 'berita', 'pengumuman', 'informasi_publik'];

if (!in_array($table, $allowed_tables)) {
    $table = 'pasien';
}


// Get data dengan error handling
$result = null;
$error_message = '';
$row_count = 0;

if (in_array($table, $allowed_tables)) {
    $query = "SELECT * FROM `" . $table . "` ORDER BY id DESC LIMIT 100";
    $result = @$conn->query($query);
    
    if ($result === false) {
        $error_message = "Error: " . htmlspecialchars($conn->error);
    } else {
        $row_count = $result->num_rows;
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Data - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container-main {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .header-section {
            border-bottom: 3px solid #0047AB;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-section h1 {
            color: #0047AB;
            margin: 0;
            font-weight: 700;
        }
        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 25px;
        }
        .nav-tabs .nav-link {
            color: #666;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link:hover {
            color: #0047AB;
            border-bottom-color: #0047AB;
        }
        .nav-tabs .nav-link.active {
            background: none;
            color: #0047AB;
            border-bottom-color: #0047AB;
        }
        .nav-link i {
            margin-right: 8px;
        }
        .badge {
            margin-left: 8px;
            font-size: 0.85rem;
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead {
            background: #0047AB;
            color: white;
        }
        .table th {
            border-color: #0047AB;
            font-weight: 600;
            vertical-align: middle;
        }
        .table td {
            vertical-align: middle;
            padding: 12px;
        }
        .table tbody tr {
            transition: background-color 0.2s;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .text-truncate-cell {
            max-width: 300px;
            word-break: break-word;
        }
        .btn-sm {
            padding: 4px 10px;
            font-size: 0.85rem;
        }
        .alert-custom {
            background: #e7f3ff;
            border-left: 4px solid #0047AB;
            color: #0047AB;
            border-radius: 4px;
        }
        .action-buttons {
            margin-top: 25px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .action-buttons .btn {
            flex: 1;
            min-width: 150px;
        }
        @media (max-width: 768px) {
            .container-main {
                padding: 20px;
            }
            .header-section h1 {
                font-size: 1.75rem;
            }
            .nav-tabs .nav-link {
                padding: 10px 12px;
                font-size: 0.9rem;
            }
            .badge {
                display: none;
            }
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .error-alert {
            background: #ffe7e7;
            border-left: 4px solid #dc3545;
            color: #721c24;
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
    </style>
</head>
<body>
    <div class="container-main">
        <!-- Header -->
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h1><i class="fas fa-database"></i> Admin Data Panel</h1>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="dashboard.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="dashboard.php?action=logout" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?php echo ($table == 'pasien') ? 'active' : ''; ?>" 
                   href="?table=pasien">
                   <i class="fas fa-user-injured"></i> Pasien
                   <?php 
                   $count_result = $conn->query("SELECT COUNT(*) as cnt FROM pasien");
                   $count = $count_result ? $count_result->fetch_assoc()['cnt'] : 0;
                   echo '<span class="badge bg-primary">' . $count . '</span>';
                   ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($table == 'dokter') ? 'active' : ''; ?>" 
                   href="?table=dokter">
                   <i class="fas fa-user-md"></i> Dokter
                   <?php 
                   $count_result = $conn->query("SELECT COUNT(*) as cnt FROM dokter");
                   $count = $count_result ? $count_result->fetch_assoc()['cnt'] : 0;
                   echo '<span class="badge bg-info">' . $count . '</span>';
                   ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($table == 'kontak') ? 'active' : ''; ?>" 
                   href="?table=kontak">
                   <i class="fas fa-envelope"></i> Kontak
                   <?php 
                   $count_result = $conn->query("SELECT COUNT(*) as cnt FROM kontak");
                   $count = $count_result ? $count_result->fetch_assoc()['cnt'] : 0;
                   echo '<span class="badge bg-success">' . $count . '</span>';
                   ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($table == 'jadwal_dokter') ? 'active' : ''; ?>" 
                   href="?table=jadwal_dokter">
                   <i class="fas fa-calendar"></i> Jadwal Dokter
                   <?php 
                   $count_result = $conn->query("SELECT COUNT(*) as cnt FROM jadwal_dokter");
                   $count = $count_result ? $count_result->fetch_assoc()['cnt'] : 0;
                   echo '<span class="badge bg-info">' . $count . '</span>';
                   ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($table == 'berita') ? 'active' : ''; ?>" 
                   href="?table=berita">
                   <i class="fas fa-newspaper"></i> Berita
                   <?php 
                   $count_result = $conn->query("SELECT COUNT(*) as cnt FROM berita");
                   $count = $count_result ? $count_result->fetch_assoc()['cnt'] : 0;
                   echo '<span class="badge bg-warning">' . $count . '</span>';
                   ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($table == 'pengumuman') ? 'active' : ''; ?>" 
                   href="?table=pengumuman">
                   <i class="fas fa-bullhorn"></i> Pengumuman
                   <?php 
                   $count_result = $conn->query("SELECT COUNT(*) as cnt FROM pengumuman");
                   $count = $count_result ? $count_result->fetch_assoc()['cnt'] : 0;
                   echo '<span class="badge bg-danger">' . $count . '</span>';
                   ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($table == 'informasi_publik') ? 'active' : ''; ?>" 
                   href="?table=informasi_publik">
                   <i class="fas fa-file-alt"></i> Informasi Publik
                   <?php 
                   $count_result = $conn->query("SELECT COUNT(*) as cnt FROM informasi_publik");
                   $count = $count_result ? $count_result->fetch_assoc()['cnt'] : 0;
                   echo '<span class="badge bg-info">' . $count . '</span>';
                   ?>
                </a>
            </li>
        </ul>

        <!-- Error Message -->
        <?php if ($error_message): ?>
        <div class="alert alert-custom error-alert mb-4" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Error:</strong> <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <!-- Data Table -->
        <?php if ($row_count > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="dataTable">
                <thead>
                    <tr>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            $first_row = $result->fetch_assoc();
                            foreach (array_keys($first_row) as $col) {
                                echo "<th>" . htmlspecialchars(ucfirst(str_replace('_', ' ', $col))) . "</th>";
                            }
                            echo "<th>Aksi</th>";
                            $result->data_seek(0);
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            $id = $row['id'];
                            foreach ($row as $col => $value) {
                                // Format output
                                $display_value = '';
                                
                                if (empty($value)) {
                                    $display_value = '<span class="text-muted">-</span>';
                                } elseif (in_array($col, ['created_at', 'updated_at', 'tanggal', 'date'])) {
                                    // Format dates
                                    $display_value = htmlspecialchars($value);
                                } elseif (in_array($col, ['deskripsi', 'content', 'isi', 'pesan', 'keterangan'])) {
                                    // Truncate long content
                                    if (strlen($value) > 100) {
                                        $display_value = htmlspecialchars(substr($value, 0, 100)) . '...';
                                    } else {
                                        $display_value = htmlspecialchars($value);
                                    }
                                } else {
                                    $display_value = htmlspecialchars($value);
                                }
                                
                                echo "<td class='text-truncate-cell'>" . $display_value . "</td>";
                            }
                            
                            // Action buttons
                            echo "<td>";
                            echo "<div class='btn-group btn-group-sm' role='group'>";
                            
                            // Edit button dengan link yang sesuai per table
                            $edit_link = '';
                            switch($table) {
                                case 'pasien':
                                    $edit_link = "edit_pasien.php?id=" . $id;
                                    break;
                                case 'dokter':
                                    $edit_link = "input_dokter.php?id=" . $id;
                                    break;
                                case 'jadwal_dokter':
                                    $edit_link = "input_jadwal_dokter.php?id=" . $id;
                                    break;
                                case 'berita':
                                    $edit_link = "input_berita.php?id=" . $id;
                                    break;
                                case 'pengumuman':
                                    $edit_link = "input_pengumuman.php?id=" . $id;
                                    break;
                                case 'informasi_publik':
                                    $edit_link = "input_info_publik.php?id=" . $id;
                                    break;
                            }
                            
                            if (!empty($edit_link)) {
                                echo "<a href='" . $edit_link . "' class='btn btn-primary' title='Edit data'>
                                    <i class='fas fa-edit'></i> Edit
                                </a>";
                                echo "<button class='btn btn-danger' onclick='deleteData(\"" . $table . "\", " . $id . ")' title='Hapus data'>
                                    <i class='fas fa-trash'></i> Hapus
                                </button>";
                            } else {
                                echo "<small class='text-muted'>No actions</small>";
                            }
                            
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h4>Tidak ada data</h4>
            <p class="text-muted">Tabel <?php echo htmlspecialchars($table); ?> masih kosong.</p>
        </div>
        <?php endif; ?>

        <!-- Info -->
        <div class="alert alert-custom mt-4">
            <i class="fas fa-info-circle"></i> 
            <strong>Fitur:</strong> Gunakan tombol <strong>Edit</strong> untuk mengubah data atau <strong>Hapus</strong> untuk menghapus data dari database.
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Kembali ke Website
            </a>
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="dashboard.php" class="btn btn-outline-primary">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Delete data function
        function deleteData(table, id) {
            if (confirm('Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) {
                $.ajax({
                    url: 'api/handle_delete_data.php',
                    type: 'POST',
                    data: {
                        table: table,
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Data berhasil dihapus!');
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Gagal menghapus data. Silakan coba lagi.');
                    }
                });
            }
        }

        // Auto-initialize tooltips if any
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Admin Data Panel loaded successfully');
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
