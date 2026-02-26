<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.html');
    exit;
}

require_once 'config/database.php';

// Get ID untuk edit mode
$edit_id = $_GET['id'] ?? null;
$edit_data = null;
$mode = 'create';

if ($edit_id) {
    $query = "SELECT * FROM berita WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
        $mode = 'edit';
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Berita - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container-form {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            max-width: 900px;
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
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px 12px;
            transition: border-color 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0047AB;
            box-shadow: 0 0 0 0.2rem rgba(0, 71, 171, 0.15);
        }
        .btn-primary {
            background: #0047AB;
            border: none;
            padding: 10px 30px;
            border-radius: 6px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #003085;
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 10px 30px;
            border-radius: 6px;
            font-weight: 600;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .note-editor.note-frame {
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .note-editor .note-toolbar {
            border-bottom: 1px solid #ddd;
            background: #f8f9fa;
        }
        .btn-group {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }
        .alert-info {
            background: #e7f3ff;
            border-left: 4px solid #0047AB;
            color: #0047AB;
            border-radius: 4px;
        }
        .form-info {
            background: #f8f9fa;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-info i {
            color: #17a2b8;
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            .container-form {
                padding: 20px;
            }
            .btn-group {
                flex-direction: column;
            }
            .btn-group .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container-form">
        <!-- Header -->
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h1>
                    <i class="fas fa-newspaper"></i>
                    <?php echo ($mode === 'edit') ? 'Edit Berita' : 'Tambah Berita Baru'; ?>
                </h1>
                <a href="admin_data.php?table=berita" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Form Info -->
        <div class="form-info">
            <i class="fas fa-info-circle"></i>
            <strong>Informasi:</strong> Isi semua field yang diperlukan untuk <?php echo ($mode === 'edit') ? 'memperbarui' : 'membuat'; ?> berita.
        </div>

        <!-- Form -->
        <form id="formBerita" method="POST" action="api/handle_berita.php" enctype="multipart/form-data">
            <?php if ($mode === 'edit'): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>

            <!-- Judul -->
            <div class="form-group">
                <label for="judul" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control" 
                       id="judul" 
                       name="judul" 
                       placeholder="Masukkan judul berita"
                       value="<?php echo ($edit_data) ? htmlspecialchars($edit_data['judul']) : ''; ?>"
                       required>
                <small class="text-muted">Judul akan ditampilkan di halaman utama</small>
            </div>

            <!-- Konten -->
            <div class="form-group">
                <label for="konten" class="form-label">Konten Berita <span class="text-danger">*</span></label>
                <textarea class="form-control" 
                          id="konten" 
                          name="konten" 
                          rows="8" 
                          placeholder="Masukkan konten berita"
                          required><?php echo ($edit_data) ? htmlspecialchars($edit_data['konten']) : ''; ?></textarea>
                <small class="text-muted">Gunakan editor di bawah untuk formatting</small>
            </div>

            <!-- Gambar -->
            <div class="form-group">
                <label for="gambar" class="form-label">Gambar (Opsional)</label>
                <input type="file" 
                       class="form-control" 
                       id="gambar" 
                       name="gambar" 
                       accept="image/*">
                <small class="text-muted">Format: JPG, PNG, GIF. Ukuran max: 2MB</small>
                <?php if ($edit_data && $edit_data['gambar']): ?>
                    <div class="mt-2">
                        <p class="text-muted mb-2">Gambar saat ini:</p>
                        <img src="<?php echo htmlspecialchars($edit_data['gambar']); ?>" 
                             alt="Gambar Berita" 
                             style="max-width: 200px; border-radius: 6px;">
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tanggal Publikasi -->
            <div class="form-group">
                <label for="tanggal_publikasi" class="form-label">Tanggal Publikasi <span class="text-danger">*</span></label>
                <input type="datetime-local" 
                       class="form-control" 
                       id="tanggal_publikasi" 
                       name="tanggal_publikasi"
                       value="<?php echo ($edit_data) ? substr($edit_data['tanggal_publikasi'], 0, 16) : date('Y-m-d\TH:i'); ?>"
                       required>
            </div>

            <!-- Penulis -->
            <div class="form-group">
                <label for="penulis" class="form-label">Penulis</label>
                <input type="text" 
                       class="form-control" 
                       id="penulis" 
                       name="penulis" 
                       placeholder="Nama penulis"
                       value="<?php echo ($edit_data) ? htmlspecialchars($edit_data['penulis']) : $_SESSION['admin_name'] ?? ''; ?>">
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" id="status" name="status" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="publikasi" <?php echo ($edit_data && $edit_data['status'] === 'publikasi') ? 'selected' : ''; ?>>Publikasi</option>
                    <option value="draft" <?php echo ($edit_data && $edit_data['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> 
                    <?php echo ($mode === 'edit') ? 'Perbarui' : 'Simpan'; ?> Berita
                </button>
                <a href="admin_data.php?table=berita" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Summernote
            $('#konten').summernote({
                height: 300,
                placeholder: 'Masukkan konten berita...',
                tabsize: 2,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Form validation and AJAX submit
            $('#formBerita').on('submit', function(e) {
                e.preventDefault();
                
                const judul = $('#judul').val().trim();
                const konten = $('#konten').summernote('code');
                const status = $('#status').val();

                if (!judul) {
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Judul berita harus diisi!',
                        icon: 'warning'
                    });
                    return false;
                }

                if (konten === '<p><br></p>' || !konten.trim()) {
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Konten berita harus diisi!',
                        icon: 'warning'
                    });
                    return false;
                }

                if (!status) {
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Status harus dipilih!',
                        icon: 'warning'
                    });
                    return false;
                }

                // Submit form via AJAX
                const formData = new FormData(this);
                formData.set('konten', konten); // Set the Summernote content

                $.ajax({
                    url: 'api/handle_berita.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Sukses!',
                                text: response.message,
                                icon: 'success',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: function() {
                                    // Auto-close after 2 seconds
                                    setTimeout(function() {
                                        Swal.close();
                                        window.location.href = response.redirect;
                                    }, 2000);
                                },
                                willClose: function() {
                                    window.location.href = response.redirect;
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || error || 'Terjadi kesalahan',
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
