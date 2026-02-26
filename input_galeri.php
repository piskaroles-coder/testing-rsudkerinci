<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.html');
    exit;
}

require_once 'config/database.php';

// Get ID untuk edit mode - get berita with gambar
$edit_id = $_GET['id'] ?? null;
$edit_data = null;
$mode = 'create';

if ($edit_id) {
    $query = "SELECT id, judul, konten, gambar, tanggal_publikasi, penulis, status FROM berita WHERE id = ?";
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
    <title><?php echo ($mode === 'edit') ? 'Edit Galeri' : 'Tambah Galeri'; ?> - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container-form {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            max-width: 600px;
        }
        .header-section {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-section h1 {
            color: #667eea;
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
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        .btn-primary {
            background: #667eea;
            border: none;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-group {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }
        .form-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-info i {
            color: #667eea;
            margin-right: 10px;
        }
        .image-preview {
            max-width: 100%;
            max-height: 250px;
            border-radius: 6px;
            margin-top: 10px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .preview-container {
            display: none;
        }
        .preview-container.show {
            display: block;
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
                    <i class="fas fa-image"></i>
                    <?php echo ($mode === 'edit') ? 'Edit Galeri' : 'Tambah Galeri Baru'; ?>
                </h1>
                <a href="galeri.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Form Info -->
        <div class="form-info">
            <i class="fas fa-info-circle"></i>
            <strong>Informasi:</strong> Galeri foto berasal dari berita yang memiliki gambar. Unggah berita dengan gambar untuk menambah foto ke galeri.
        </div>

        <!-- Form -->
        <form id="formGaleri" method="POST" action="api/handle_berita.php" enctype="multipart/form-data">
            <?php if ($mode === 'edit'): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>

            <!-- Judul -->
            <div class="form-group">
                <label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control" 
                       id="judul" 
                       name="judul" 
                       placeholder="Masukkan judul galeri"
                       value="<?php echo ($edit_data) ? htmlspecialchars($edit_data['judul']) : ''; ?>"
                       required>
                <small class="text-muted">Judul deskriptif untuk foto</small>
            </div>

            <!-- Konten/Deskripsi -->
            <div class="form-group">
                <label for="konten" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control" 
                          id="konten" 
                          name="konten" 
                          rows="4" 
                          placeholder="Masukkan deskripsi untuk foto"
                          required><?php echo ($edit_data) ? htmlspecialchars($edit_data['konten']) : ''; ?></textarea>
                <small class="text-muted">Jelaskan isi dan konteks foto ini</small>
            </div>

            <!-- Gambar -->
            <div class="form-group">
                <label for="gambar" class="form-label">Foto <span class="text-danger">*</span></label>
                <input type="file" 
                       class="form-control" 
                       id="gambar" 
                       name="gambar" 
                       accept="image/*"
                       <?php echo (!$edit_data) ? 'required' : ''; ?>>
                <small class="text-muted">Format: JPG, PNG, GIF. Ukuran maksimal: 5MB</small>
                
                <?php if ($edit_data && $edit_data['gambar']): ?>
                    <div class="preview-container show">
                        <h6 class="mt-3 mb-2">Preview Foto Saat Ini:</h6>
                        <img src="<?php echo htmlspecialchars($edit_data['gambar']); ?>" alt="Current Image" class="image-preview">
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tanggal Publikasi -->
            <div class="form-group">
                <label for="tanggal_publikasi" class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="datetime-local" 
                       class="form-control" 
                       id="tanggal_publikasi" 
                       name="tanggal_publikasi"
                       value="<?php echo ($edit_data) ? substr($edit_data['tanggal_publikasi'], 0, 16) : date('Y-m-d\TH:i'); ?>"
                       required>
            </div>

            <!-- Penulis -->
            <div class="form-group">
                <label for="penulis" class="form-label">Penulis/Fotografer</label>
                <input type="text" 
                       class="form-control" 
                       id="penulis" 
                       name="penulis" 
                       placeholder="Nama penulis atau fotografer"
                       value="<?php echo ($edit_data) ? htmlspecialchars($edit_data['penulis']) : $_SESSION['admin_name'] ?? ''; ?>">
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select" id="status" name="status" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="publikasi" <?php echo ($edit_data && $edit_data['status'] === 'publikasi') ? 'selected' : ''; ?>>Publikasi (Tampil di Galeri)</option>
                    <option value="draft" <?php echo ($edit_data && $edit_data['status'] === 'draft') ? 'selected' : ''; ?>>Draft (Tersimpan, Tidak Tampil)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> 
                    <?php echo ($mode === 'edit') ? 'Perbarui' : 'Simpan'; ?> Galeri
                </button>
                <a href="galeri.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Preview image on file select
            $('#gambar').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let previewHtml = '<div class="preview-container show">';
                        previewHtml += '<h6 class="mt-3 mb-2">Preview Foto Baru:</h6>';
                        previewHtml += '<img src="' + e.target.result + '" alt="Preview" class="image-preview">';
                        previewHtml += '</div>';
                        
                        $('#gambar').after(previewHtml);
                        $('#gambar').next('.preview-container').prev('.preview-container').remove();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Form validation and AJAX submit
            $('#formGaleri').on('submit', function(e) {
                e.preventDefault();
                
                const judul = $('#judul').val().trim();
                const konten = $('#konten').val().trim();
                const status = $('#status').val();

                if (!judul) {
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Judul harus diisi!',
                        icon: 'warning'
                    });
                    return false;
                }

                if (!konten) {
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Deskripsi harus diisi!',
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
                                    setTimeout(function() {
                                        Swal.close();
                                        window.location.href = 'galeri.php';
                                    }, 2000);
                                },
                                willClose: function() {
                                    window.location.href = 'galeri.php';
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
