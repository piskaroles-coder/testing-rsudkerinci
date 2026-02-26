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
    $query = "SELECT * FROM pengumuman WHERE id = ?";
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
    <title>Input Pengumuman - RSUD Kabupaten Kerinci</title>
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
            border-bottom: 3px solid #DC3545;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-section h1 {
            color: #DC3545;
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
            border-color: #DC3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        }
        .btn-primary {
            background: #DC3545;
            border: none;
            padding: 10px 30px;
            border-radius: 6px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #c82333;
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
            background: #ffeeee;
            border-left: 4px solid #DC3545;
            color: #DC3545;
            border-radius: 4px;
        }
        .form-info {
            background: #f8f9fa;
            border-left: 4px solid #DC3545;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-info i {
            color: #DC3545;
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
                    <i class="fas fa-bullhorn"></i>
                    <?php echo ($mode === 'edit') ? 'Edit Pengumuman' : 'Tambah Pengumuman Baru'; ?>
                </h1>
                <a href="admin_data.php?table=pengumuman" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Form Info -->
        <div class="form-info">
            <i class="fas fa-info-circle"></i>
            <strong>Informasi:</strong> Isi semua field yang diperlukan untuk <?php echo ($mode === 'edit') ? 'memperbarui' : 'membuat'; ?> pengumuman.
        </div>

        <!-- Form -->
        <form id="formPengumuman" method="POST" action="api/handle_pengumuman.php">
            <?php if ($mode === 'edit'): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>

            <!-- Judul -->
            <div class="form-group">
                <label for="judul" class="form-label">Judul Pengumuman <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control" 
                       id="judul" 
                       name="judul" 
                       placeholder="Masukkan judul pengumuman"
                       value="<?php echo ($edit_data) ? htmlspecialchars($edit_data['judul']) : ''; ?>"
                       required>
                <small class="text-muted">Judul akan ditampilkan di halaman utama</small>
            </div>

            <!-- Isi -->
            <div class="form-group">
                <label for="isi" class="form-label">Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea class="form-control" 
                          id="isi" 
                          name="isi" 
                          rows="8" 
                          placeholder="Masukkan isi pengumuman"
                          required><?php echo ($edit_data) ? htmlspecialchars($edit_data['isi']) : ''; ?></textarea>
                <small class="text-muted">Gunakan editor di bawah untuk formatting</small>
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
                    <option value="aktif" <?php echo ($edit_data && $edit_data['status'] === 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="arsip" <?php echo ($edit_data && $edit_data['status'] === 'arsip') ? 'selected' : ''; ?>>Arsip</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> 
                    <?php echo ($mode === 'edit') ? 'Perbarui' : 'Simpan'; ?> Pengumuman
                </button>
                <a href="admin_data.php?table=pengumuman" class="btn btn-secondary">
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
            $('#isi').summernote({
                height: 300,
                placeholder: 'Masukkan isi pengumuman...',
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
            $('#formPengumuman').on('submit', function(e) {
                e.preventDefault();
                
                const judul = $('#judul').val().trim();
                const isi = $('#isi').summernote('code');
                const status = $('#status').val();

                if (!judul) {
                    alert('Judul pengumuman harus diisi!');
                    return false;
                }

                if (isi === '<p><br></p>' || !isi.trim()) {
                    alert('Isi pengumuman harus diisi!');
                    return false;
                }

                if (!status) {
                    alert('Status harus dipilih!');
                    return false;
                }

                // Submit form via AJAX
                const formData = new FormData(this);
                formData.set('isi', isi); // Set the Summernote content

                $.ajax({
                    url: 'api/handle_pengumuman.php',
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
