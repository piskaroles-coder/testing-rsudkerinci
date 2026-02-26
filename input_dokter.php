<?php
session_start();
require 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.html');
    exit();
}

// Check if editing
$edit_mode = isset($_GET['id']);
$dokter = null;

if ($edit_mode) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM dokter WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dokter = $result->fetch_assoc();
    
    if (!$dokter) {
        echo "Data dokter tidak ditemukan";
        exit();
    }
}

// Get all doctors for dropdown if needed
$doctors_list = [];
$stmt = $conn->prepare("SELECT id, nama, spesialisasi FROM dokter ORDER BY nama");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $doctors_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit Data Dokter' : 'Input Data Dokter'; ?> - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 40px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .form-title {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
            font-weight: bold;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-top: 15px;
        }
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: #667eea;
            border: none;
            border-radius: 5px;
            padding: 10px 30px;
            font-weight: 600;
            margin-top: 20px;
        }
        .btn-primary:hover {
            background: #764ba2;
        }
        .btn-back {
            margin-right: 10px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
        .success-message {
            color: #28a745;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">
                <i class="fas fa-user-md"></i>
                <?php echo $edit_mode ? 'Edit Data Dokter' : 'Input Data Dokter Baru'; ?>
            </h2>

            <form id="dokterForm">
                <div class="form-group">
                    <label for="nama" class="form-label">
                        <i class="fas fa-user"></i> Nama Dokter <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="nama" name="nama" 
                        value="<?php echo $edit_mode ? htmlspecialchars($dokter['nama']) : ''; ?>" 
                        placeholder="Masukkan nama dokter" required>
                    <small class="form-text text-muted">Contoh: Dr. Ahmad Santoso</small>
                </div>

                <div class="form-group">
                    <label for="spesialisasi" class="form-label">
                        <i class="fas fa-stethoscope"></i> Spesialisasi <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="spesialisasi" name="spesialisasi" 
                        value="<?php echo $edit_mode ? htmlspecialchars($dokter['spesialisasi']) : ''; ?>" 
                        placeholder="Masukkan spesialisasi dokter" required>
                    <small class="form-text text-muted">Contoh: Spesialis Jantung, Spesialis Anak, dll</small>
                </div>

                <div class="form-group">
                    <label for="nip" class="form-label">
                        <i class="fas fa-id-card"></i> NIP (Nomor Induk Pegawai) <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="nip" name="nip" 
                        value="<?php echo $edit_mode ? htmlspecialchars($dokter['nip']) : ''; ?>" 
                        placeholder="Masukkan NIP dokter" required>
                </div>

                <div class="form-group">
                    <label for="no_str" class="form-label">
                        <i class="fas fa-certificate"></i> No. STR (Surat Tanda Registrasi) <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="no_str" name="no_str" 
                        value="<?php echo $edit_mode ? htmlspecialchars($dokter['no_str']) : ''; ?>" 
                        placeholder="Masukkan No. STR" required>
                </div>

                <div class="form-group">
                    <label for="telepon" class="form-label">
                        <i class="fas fa-phone"></i> Nomor Telepon
                    </label>
                    <input type="tel" class="form-control" id="telepon" name="telepon" 
                        value="<?php echo $edit_mode ? htmlspecialchars($dokter['telepon']) : ''; ?>" 
                        placeholder="Contoh: 0821-xxxx-xxxx">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                        value="<?php echo $edit_mode ? htmlspecialchars($dokter['email']) : ''; ?>" 
                        placeholder="Masukkan email dokter">
                </div>

                <div class="form-group">
                    <label for="alamat" class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Alamat
                    </label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat dokter"><?php echo $edit_mode ? htmlspecialchars($dokter['alamat']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">
                        <i class="fas fa-toggle-on"></i> Status <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="aktif" <?php echo $edit_mode && $dokter['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="nonaktif" <?php echo $edit_mode && $dokter['status'] == 'nonaktif' ? 'selected' : ''; ?>>Non-Aktif</option>
                    </select>
                </div>

                <div id="responseMessage" class="mt-3"></div>

                <div class="button-group">
                    <a href="dashboard.php" class="btn btn-secondary btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 
                        <?php echo $edit_mode ? 'Update Data' : 'Simpan Data'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dokterForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const url = '<?php echo $edit_mode ? 'api/handle_dokter.php?action=update&id=' . $_GET['id'] : 'api/handle_dokter.php?action=create'; ?>';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);

                            if (data.success) {
                                Swal.fire({
                                    title: 'Sukses!',
                                    text: data.message,
                                    icon: 'success',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    didOpen: function() {
                                        setTimeout(function() {
                                            Swal.close();
                                            window.location.href = 'dashboard.php';
                                        }, 2000);
                                    },
                                    willClose: function() {
                                        window.location.href = 'dashboard.php';
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error'
                                });
                            }
                        } catch(err) {
                            console.error('Error parsing response:', err);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat memproses data',
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal mengirim data. Periksa koneksi Anda.',
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
