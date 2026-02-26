<?php
/**
 * API Test & Documentation
 * File ini untuk testing API endpoints
 */

require_once 'config/database.php';

$result_message = '';
$test_type = $_GET['test'] ?? '';

// Handle test requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $test_type = $_POST['test_type'] ?? '';
    
    if ($test_type === 'test_pendaftaran') {
        // Test data pendaftaran
        $test_data = array(
            'nama' => 'Budi Santoso',
            'noktp' => '3200' . date('YmdHis'),
            'tgllahir' => '1990-05-15',
            'jeniskelamin' => 'Laki-laki',
            'goldarah' => 'O',
            'agama' => 'Islam',
            'alamat' => 'Jl. Test No. 123',
            'kota' => 'Kerinci',
            'kodepos' => '37162',
            'telepon' => '081234567890',
            'email' => 'test-' . time() . '@example.com',
            'penyakit' => 'Diabetes',
            'alergi' => 'Penisilin',
            'namapj' => 'Ibu Siti',
            'hubungan' => 'Istri',
            'teleponpj' => '081234567891',
            'alamatpj' => 'Jl. Test No. 123',
            'asuransi' => 'BPJS Kesehatan',
            'nomorasuransi' => '1234567890123456'
        );
        
        // Send test request
        $ch = curl_init('http://localhost/rsudkabkerinci.go.id/api/handle_pendaftaran.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($test_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result_message = '<div class="alert alert-info"><strong>Test Pendaftaran:</strong><pre>' . htmlspecialchars($response) . '</pre></div>';
    }
    
    if ($test_type === 'test_kontak') {
        // Test data kontak
        $test_data = array(
            'name' => 'Test User',
            'email' => 'test-' . time() . '@example.com',
            'message' => 'Ini adalah pesan test dari testing page'
        );
        
        // Send test request
        $ch = curl_init('http://localhost/rsudkabkerinci.go.id/api/handle_kontak.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($test_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result_message = '<div class="alert alert-info"><strong>Test Kontak:</strong><pre>' . htmlspecialchars($response) . '</pre></div>';
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Testing - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0047AB;
            margin-bottom: 30px;
        }
        .card {
            border: none;
            border-left: 5px solid #0047AB;
            margin-bottom: 20px;
        }
        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 600;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            color: #d63384;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .endpoint {
            font-family: monospace;
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .status-ok {
            color: #28a745;
        }
        .status-error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-flask"></i> API Testing & Documentation</h1>

        <?php echo $result_message; ?>

        <!-- Database Connection Status -->
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> 
            <strong>Database Status:</strong> Connected to <code>rsud_kerinci</code>
        </div>

        <!-- API Endpoints Documentation -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-book"></i> API Endpoints
            </div>
            <div class="card-body">
                <h5 class="mt-3">1. Handle Pendaftaran Pasien</h5>
                <div class="endpoint">POST /api/handle_pendaftaran.php</div>
                
                <p><strong>Parameters:</strong></p>
                <ul style="font-size: 0.9rem;">
                    <li><code>nama</code> (required) - Nama lengkap pasien</li>
                    <li><code>noktp</code> (required) - Nomor KTP (unik)</li>
                    <li><code>tgllahir</code> (required) - Tanggal lahir (YYYY-MM-DD)</li>
                    <li><code>jeniskelamin</code> (required) - Laki-laki / Perempuan</li>
                    <li><code>goldarah</code> - Golongan darah (A/B/AB/O)</li>
                    <li><code>agama</code> - Agama</li>
                    <li><code>alamat</code> (required) - Alamat lengkap</li>
                    <li><code>kota</code> (required) - Kota/Kabupaten</li>
                    <li><code>kodepos</code> - Kode pos</li>
                    <li><code>telepon</code> (required) - Nomor telepon</li>
                    <li><code>email</code> - Email</li>
                    <li><code>penyakit</code> - Riwayat penyakit</li>
                    <li><code>alergi</code> - Alergi obat/makanan</li>
                    <li><code>namapj</code> - Nama penanggung jawab</li>
                    <li><code>hubungan</code> - Hubungan keluarga</li>
                    <li><code>teleponpj</code> - Telepon penanggung jawab</li>
                    <li><code>alamatpj</code> - Alamat penanggung jawab</li>
                    <li><code>asuransi</code> - Jenis asuransi</li>
                    <li><code>nomorasuransi</code> - Nomor asuransi</li>
                </ul>

                <p class="mt-4"><strong>Response:</strong></p>
                <pre>{
  "success": true/false,
  "message": "Pesan response",
  "patient_id": 123 (jika berhasil),
  "errors": ["error1", "error2"] (jika gagal)
}</pre>

                <h5 class="mt-5">2. Handle Kontak/Contact Form</h5>
                <div class="endpoint">POST /api/handle_kontak.php</div>
                
                <p><strong>Parameters:</strong></p>
                <ul style="font-size: 0.9rem;">
                    <li><code>name</code> (required) - Nama pengirim</li>
                    <li><code>email</code> (required) - Email pengirim</li>
                    <li><code>message</code> (required) - Pesan</li>
                </ul>

                <p class="mt-4"><strong>Response:</strong></p>
                <pre>{
  "success": true/false,
  "message": "Pesan response",
  "errors": ["error1", "error2"] (jika gagal)
}</pre>
            </div>
        </div>

        <!-- Test Tools -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-tools"></i> Test Tools
            </div>
            <div class="card-body">
                <p>Gunakan tombol di bawah untuk test API dengan data dummy:</p>
                
                <form method="POST" class="mt-3">
                    <input type="hidden" name="test_type" value="test_pendaftaran">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-play"></i> Test Pendaftaran API
                    </button>
                </form>

                <form method="POST" class="mt-2">
                    <input type="hidden" name="test_type" value="test_kontak">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play"></i> Test Kontak API
                    </button>
                </form>
            </div>
        </div>

        <!-- Database Info -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-database"></i> Database Information
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Database Name</strong></td>
                        <td><code>rsud_kerinci</code></td>
                    </tr>
                    <tr>
                        <td><strong>Host</strong></td>
                        <td><code>localhost</code></td>
                    </tr>
                    <tr>
                        <td><strong>User</strong></td>
                        <td><code>root</code></td>
                    </tr>
                    <tr>
                        <td><strong>Total Tables</strong></td>
                        <td>
                            <?php
                            $tables = $conn->query("SHOW TABLES FROM rsud_kerinci");
                            echo $tables->num_rows . " tables";
                            ?>
                        </td>
                    </tr>
                </table>

                <h6 class="mt-4">Tables:</h6>
                <ul>
                    <?php
                    $tables = $conn->query("SHOW TABLES FROM rsud_kerinci");
                    while ($row = $tables->fetch_array()) {
                        $table_name = $row[0];
                        $count = $conn->query("SELECT COUNT(*) as cnt FROM $table_name")->fetch_assoc();
                        echo "<li><code>$table_name</code> ({$count['cnt']} rows)</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- Links -->
        <div class="mt-4">
            <a href="index.php" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Kembali ke Website
            </a>
            <a href="admin_data.php" class="btn btn-outline-success">
                <i class="fas fa-chart-bar"></i> Lihat Data Admin Panel
            </a>
            <a href="http://localhost/phpmyadmin" target="_blank" class="btn btn-outline-secondary">
                <i class="fas fa-database"></i> Buka phpMyAdmin
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
