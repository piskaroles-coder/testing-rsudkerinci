<?php
require 'config/database.php';

// Create table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS informasi_publik (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi LONGTEXT NOT NULL,
    kategori ENUM('transparency', 'regulation', 'service', 'laporkan') DEFAULT 'transparency',
    penulis VARCHAR(100),
    sumber VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('aktif', 'arsip') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

$conn->query($create_table);
$create_index = "CREATE INDEX IF NOT EXISTS idx_kategori ON informasi_publik(kategori);
                 CREATE INDEX IF NOT EXISTS idx_status ON informasi_publik(status);";
$conn->multi_query($create_index);

// Check jika edit mode (ada parameter id)
$edit_mode = false;
$info_data = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM informasi_publik WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $edit_mode = true;
        $info_data = $result->fetch_assoc();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Input'; ?> Informasi Publik - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.css" rel="stylesheet">
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
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .form-section:last-of-type {
            border-bottom: none;
        }
        .section-title {
            color: #667eea;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .note {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 12px;
            margin-top: 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #666;
        }
        .summernote {
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">
                <i class="fas fa-file-alt"></i> <?php echo $edit_mode ? 'Edit' : 'Input'; ?> Informasi Publik
            </h2>

            <form id="infoForm">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?php echo $info_data['id']; ?>">
                <?php endif; ?>
                <!-- Judul -->
                <div class="form-section">
                    <h5 class="section-title"><i class="fas fa-heading"></i> Judul Informasi</h5>
                    <div class="form-group">
                        <label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="judul" name="judul" 
                            placeholder="Masukkan judul informasi publik" 
                            value="<?php echo $edit_mode ? htmlspecialchars($info_data['judul']) : ''; ?>" required>
                        <small class="text-muted">Judul harus deskriptif dan jelas</small>
                    </div>
                </div>

                <!-- Kategori -->
                <div class="form-section">
                    <h5 class="section-title"><i class="fas fa-folder"></i> Kategori</h5>
                    <div class="form-group">
                        <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="kategori" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="transparency" <?php echo ($edit_mode && $info_data['kategori'] == 'transparency') ? 'selected' : ''; ?>>Transparansi</option>
                            <option value="regulation" <?php echo ($edit_mode && $info_data['kategori'] == 'regulation') ? 'selected' : ''; ?>>Regulasi & Kebijakan</option>
                            <option value="service" <?php echo ($edit_mode && $info_data['kategori'] == 'service') ? 'selected' : ''; ?>>Layanan</option>
                            <option value="laporkan" <?php echo ($edit_mode && $info_data['kategori'] == 'laporkan') ? 'selected' : ''; ?>>Laporan & Pertanggungjawaban</option>
                        </select>
                    </div>
                </div>

                <!-- Isi Informasi -->
                <div class="form-section">
                    <h5 class="section-title"><i class="fas fa-pencil-alt"></i> Isi Informasi</h5>
                    <div class="form-group">
                        <label for="isi" class="form-label">Isi Informasi <span class="text-danger">*</span></label>
                        <textarea class="form-control summernote" id="isi" name="isi" rows="10" required><?php echo $edit_mode ? htmlspecialchars($info_data['isi']) : ''; ?></textarea>
                        <div class="note">
                            <i class="fas fa-info-circle"></i> Gunakan editor untuk format teks, tambahkan gambar, dan list
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="form-section">
                    <h5 class="section-title"><i class="fas fa-info-circle"></i> Metadata</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="penulis" class="form-label">Penulis <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="penulis" name="penulis" 
                                    placeholder="Nama penulis" 
                                    value="<?php echo $edit_mode ? htmlspecialchars($info_data['penulis']) : ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sumber" class="form-label">Sumber (Opsional)</label>
                                <input type="url" class="form-control" id="sumber" name="sumber" 
                                    placeholder="URL sumber referensi"
                                    value="<?php echo $edit_mode ? htmlspecialchars($info_data['sumber']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="form-section">
                    <div class="form-group">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="aktif" <?php echo ($edit_mode && $info_data['status'] == 'aktif') ? 'selected' : ($edit_mode ? '' : 'selected'); ?>>Aktif</option>
                            <option value="arsip" <?php echo ($edit_mode && $info_data['status'] == 'arsip') ? 'selected' : ''; ?>>Arsip</option>
                        </select>
                    </div>
                </div>

                <div id="responseMessage" class="mt-3"></div>

                <div class="button-group">
                    <a href="dashboard.php" class="btn btn-secondary btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $edit_mode ? 'Update' : 'Simpan'; ?> Informasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Summernote
            $('#isi').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Form submit
            $('#infoForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const isEditMode = $('input[name="id"]').val();
                const action = isEditMode ? 'update' : 'create';

                $.ajax({
                    url: 'api/handle_info_publik.php?action=' + action,
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
                                            window.location.href = 'info_publik.php';
                                        }, 2000);
                                    },
                                    willClose: function() {
                                        window.location.href = 'info_publik.php';
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
