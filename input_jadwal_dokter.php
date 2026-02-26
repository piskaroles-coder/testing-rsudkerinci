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
$jadwal = null;

if ($edit_mode) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM jadwal_dokter WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $jadwal = $result->fetch_assoc();
    
    if (!$jadwal) {
        echo "Data jadwal tidak ditemukan";
        exit();
    }
}

// Get all doctors for dropdown
$doctors = [];
$stmt = $conn->prepare("SELECT id, nama, spesialisasi FROM dokter WHERE status = 'aktif' ORDER BY nama");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit Jadwal Dokter' : 'Input Jadwal Dokter'; ?> - RSUD Kabupaten Kerinci</title>
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
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">
                <i class="fas fa-calendar-alt"></i>
                <?php echo $edit_mode ? 'Edit Jadwal Dokter' : 'Input Jadwal Dokter Baru'; ?>
            </h2>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <small>Atur jadwal praktik dokter di rumah sakit</small>
            </div>

            <form id="jadwalForm">
                <div class="form-group">
                    <label for="dokter_id" class="form-label">
                        <i class="fas fa-user-md"></i> Pilih Dokter <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="dokter_id" name="dokter_id" required>
                        <option value="">-- Pilih Dokter --</option>
                        <?php foreach ($doctors as $dokter): ?>
                            <option value="<?php echo $dokter['id']; ?>" 
                                <?php echo $edit_mode && $jadwal['dokter_id'] == $dokter['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dokter['nama']) . ' (' . htmlspecialchars($dokter['spesialisasi']) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Pilih dokter yang akan dijadwalkan</small>
                </div>

                <div class="form-group">
                    <label for="hari" class="form-label">
                        <i class="fas fa-day-schedule"></i> Hari <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="hari" name="hari" required>
                        <option value="">-- Pilih Hari --</option>
                        <option value="Senin" <?php echo $edit_mode && $jadwal['hari'] == 'Senin' ? 'selected' : ''; ?>>Senin</option>
                        <option value="Selasa" <?php echo $edit_mode && $jadwal['hari'] == 'Selasa' ? 'selected' : ''; ?>>Selasa</option>
                        <option value="Rabu" <?php echo $edit_mode && $jadwal['hari'] == 'Rabu' ? 'selected' : ''; ?>>Rabu</option>
                        <option value="Kamis" <?php echo $edit_mode && $jadwal['hari'] == 'Kamis' ? 'selected' : ''; ?>>Kamis</option>
                        <option value="Jumat" <?php echo $edit_mode && $jadwal['hari'] == 'Jumat' ? 'selected' : ''; ?>>Jumat</option>
                        <option value="Sabtu" <?php echo $edit_mode && $jadwal['hari'] == 'Sabtu' ? 'selected' : ''; ?>>Sabtu</option>
                        <option value="Minggu" <?php echo $edit_mode && $jadwal['hari'] == 'Minggu' ? 'selected' : ''; ?>>Minggu</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jam_mulai" class="form-label">
                                <i class="fas fa-clock"></i> Jam Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" 
                                value="<?php echo $edit_mode ? $jadwal['jam_mulai'] : '08:00'; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jam_selesai" class="form-label">
                                <i class="fas fa-clock"></i> Jam Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" 
                                value="<?php echo $edit_mode ? $jadwal['jam_selesai'] : '12:00'; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="lokasi" class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Lokasi / Ruang Praktik <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="lokasi" name="lokasi" 
                        value="<?php echo $edit_mode ? htmlspecialchars($jadwal['lokasi']) : ''; ?>" 
                        placeholder="Contoh: Ruang Jantung A, Poli Anak B" required>
                </div>

                <div class="form-group">
                    <label for="kuota" class="form-label">
                        <i class="fas fa-users"></i> Kuota Pasien (per hari)
                    </label>
                    <input type="number" class="form-control" id="kuota" name="kuota" min="1" max="100"
                        value="<?php echo $edit_mode ? $jadwal['kuota'] : '20'; ?>" placeholder="Jumlah pasien maksimal">
                    <small class="form-text text-muted">Kosongkan jika tidak dibatasi</small>
                </div>

                <div id="responseMessage" class="mt-3"></div>

                <div class="button-group">
                    <a href="dashboard.php" class="btn btn-secondary btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 
                        <?php echo $edit_mode ? 'Update Jadwal' : 'Simpan Jadwal'; ?>
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
            $('#jadwalForm').on('submit', function(e) {
                e.preventDefault();

                // Validation
                const jamMulai = $('#jam_mulai').val();
                const jamSelesai = $('#jam_selesai').val();

                if (jamMulai >= jamSelesai) {
                    $('#responseMessage').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> Jam mulai harus lebih awal dari jam selesai
                        </div>
                    `);
                    return;
                }

                const formData = new FormData(this);
                const url = '<?php echo $edit_mode ? 'api/handle_jadwal_dokter.php?action=update&id=' . $_GET['id'] : 'api/handle_jadwal_dokter.php?action=create'; ?>';

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
