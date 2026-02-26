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
$pasien = null;

if ($edit_mode) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM pasien WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pasien = $result->fetch_assoc();
    
    if (!$pasien) {
        echo "Data pasien tidak ditemukan";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit Data Pasien' : 'Input Data Pasien'; ?> - RSUD Kabupaten Kerinci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .two-column {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">
                <i class="fas fa-user-injured"></i>
                <?php echo $edit_mode ? 'Edit Data Pasien' : 'Input Data Pasien Baru'; ?>
            </h2>

            <form id="pasienForm">
                <!-- Data Pribadi -->
                <div class="form-section">
                    <h5 class="section-title"><i class="fas fa-user"></i> Data Pribadi</h5>
                    <div class="two-column">
                        <div class="form-group">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['nama']) : ''; ?>" 
                                placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="form-group">
                            <label for="noktp" class="form-label">No. KTP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="noktp" name="noktp" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['noktp']) : ''; ?>" 
                                placeholder="Masukkan No. KTP" <?php echo $edit_mode ? '' : 'required'; ?>>
                        </div>
                    </div>
                    <div class="two-column">
                        <div class="form-group">
                            <label for="tgllahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tgllahir" name="tgllahir" 
                                value="<?php echo $edit_mode ? $pasien['tgllahir'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="jeniskelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select" id="jeniskelamin" name="jeniskelamin" required>
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" <?php echo $edit_mode && $pasien['jeniskelamin'] == 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?php echo $edit_mode && $pasien['jeniskelamin'] == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="two-column">
                        <div class="form-group">
                            <label for="goldarah" class="form-label">Golongan Darah</label>
                            <select class="form-select" id="goldarah" name="goldarah">
                                <option value="">-- Pilih --</option>
                                <option value="A" <?php echo $edit_mode && $pasien['goldarah'] == 'A' ? 'selected' : ''; ?>>A</option>
                                <option value="B" <?php echo $edit_mode && $pasien['goldarah'] == 'B' ? 'selected' : ''; ?>>B</option>
                                <option value="AB" <?php echo $edit_mode && $pasien['goldarah'] == 'AB' ? 'selected' : ''; ?>>AB</option>
                                <option value="O" <?php echo $edit_mode && $pasien['goldarah'] == 'O' ? 'selected' : ''; ?>>O</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="agama" class="form-label">Agama</label>
                            <select class="form-select" id="agama" name="agama">
                                <option value="">-- Pilih --</option>
                                <option value="Islam" <?php echo $edit_mode && $pasien['agama'] == 'Islam' ? 'selected' : ''; ?>>Islam</option>
                                <option value="Kristen Protestan" <?php echo $edit_mode && $pasien['agama'] == 'Kristen Protestan' ? 'selected' : ''; ?>>Kristen Protestan</option>
                                <option value="Kristen Katolik" <?php echo $edit_mode && $pasien['agama'] == 'Kristen Katolik' ? 'selected' : ''; ?>>Kristen Katolik</option>
                                <option value="Hindu" <?php echo $edit_mode && $pasien['agama'] == 'Hindu' ? 'selected' : ''; ?>>Hindu</option>
                                <option value="Budha" <?php echo $edit_mode && $pasien['agama'] == 'Budha' ? 'selected' : ''; ?>>Budha</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="form-section">
                    <h5 class="section-title"><i class="fas fa-map-marker-alt"></i> Alamat</h5>
                    <div class="form-group">
                        <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap" required><?php echo $edit_mode ? htmlspecialchars($pasien['alamat']) : ''; ?></textarea>
                    </div>
                    <div class="two-column">
                        <div class="form-group">
                            <label for="kota" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kota" name="kota" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['kota']) : ''; ?>" 
                                placeholder="Masukkan kota" required>
                        </div>
                        <div class="form-group">
                            <label for="kodepos" class="form-label">Kode Pos</label>
                            <input type="text" class="form-control" id="kodepos" name="kodepos" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['kodepos']) : ''; ?>" 
                                placeholder="Masukkan kode pos">
                        </div>
                    </div>
                </div>

                <!-- Kontak -->
                <div class="form-section">
                    <h5 class="section-title"><i class="fas fa-phone"></i> Kontak</h5>
                    <div class="two-column">
                        <div class="form-group">
                            <label for="telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="telepon" name="telepon" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['telepon']) : ''; ?>" 
                                placeholder="Contoh: 0821-xxxx-xxxx" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['email']) : ''; ?>" 
                                placeholder="Masukkan email">
                        </div>
                    </div>
                </div>

                <!-- Kesehatan -->
                <div class="form-section">
                    <h5 class="section-title"><i class="fas fa-heartbeat"></i> Kesehatan</h5>
                    <div class="form-group">
                        <label for="penyakit" class="form-label">Riwayat Penyakit</label>
                        <textarea class="form-control" id="penyakit" name="penyakit" rows="2" placeholder="Masukkan riwayat penyakit"><?php echo $edit_mode ? htmlspecialchars($pasien['penyakit']) : ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="alergi" class="form-label">Alergi</label>
                        <textarea class="form-control" id="alergi" name="alergi" rows="2" placeholder="Masukkan informasi alergi"><?php echo $edit_mode ? htmlspecialchars($pasien['alergi']) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Penanggung Jawab -->
                <div class="form-section">
                    <h5 class="section-title"><i class="fas fa-users"></i> Penanggung Jawab</h5>
                    <div class="two-column">
                        <div class="form-group">
                            <label for="namapj" class="form-label">Nama Penanggung Jawab</label>
                            <input type="text" class="form-control" id="namapj" name="namapj" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['namapj']) : ''; ?>" 
                                placeholder="Masukkan nama">
                        </div>
                        <div class="form-group">
                            <label for="hubungan" class="form-label">Hubungan</label>
                            <input type="text" class="form-control" id="hubungan" name="hubungan" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['hubungan']) : ''; ?>" 
                                placeholder="Contoh: Istri, Anak, Orang Tua">
                        </div>
                    </div>
                    <div class="two-column">
                        <div class="form-group">
                            <label for="teleponpj" class="form-label">Telepon Penanggung Jawab</label>
                            <input type="tel" class="form-control" id="teleponpj" name="teleponpj" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['teleponpj']) : ''; ?>" 
                                placeholder="Nomor telepon">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="alamatpj" class="form-label">Alamat Penanggung Jawab</label>
                        <textarea class="form-control" id="alamatpj" name="alamatpj" rows="2" placeholder="Alamat lengkap"><?php echo $edit_mode ? htmlspecialchars($pasien['alamatpj']) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Asuransi -->
                <div class="form-section">
                    <h5 class="section-title"><i class="fas fa-shield-alt"></i> Asuransi</h5>
                    <div class="two-column">
                        <div class="form-group">
                            <label for="asuransi" class="form-label">Jenis Asuransi</label>
                            <input type="text" class="form-control" id="asuransi" name="asuransi" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['asuransi']) : ''; ?>" 
                                placeholder="Contoh: BPJS, Asuransi Swasta">
                        </div>
                        <div class="form-group">
                            <label for="nomorasuransi" class="form-label">Nomor Asuransi</label>
                            <input type="text" class="form-control" id="nomorasuransi" name="nomorasuransi" 
                                value="<?php echo $edit_mode ? htmlspecialchars($pasien['nomorasuransi']) : ''; ?>" 
                                placeholder="Nomor polis">
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="form-section">
                    <div class="form-group">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="aktif" <?php echo $edit_mode && $pasien['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="nonaktif" <?php echo $edit_mode && $pasien['status'] == 'nonaktif' ? 'selected' : ''; ?>>Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div id="responseMessage" class="mt-3"></div>

                <div class="button-group">
                    <a href="admin_data.php?table=pasien" class="btn btn-secondary btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Data Pasien
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
    <script>
        $(document).ready(function() {
            $('#pasienForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const url = '<?php echo $edit_mode ? 'api/handle_pasien.php?action=update&id=' . $_GET['id'] : 'api/handle_pasien.php?action=create'; ?>';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            const messageDiv = $('#responseMessage');

                            if (data.success) {
                                messageDiv.html(`
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i> ${data.message}
                                    </div>
                                `);
                                setTimeout(() => {
                                    window.location.href = 'admin_data.php?table=pasien';
                                }, 1500);
                            } else {
                                messageDiv.html(`
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-circle"></i> ${data.message}
                                    </div>
                                `);
                            }
                        } catch(err) {
                            console.error('Error parsing response:', err);
                            $('#responseMessage').html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan saat memproses data
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#responseMessage').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> Gagal mengirim data. Periksa koneksi Anda.
                            </div>
                        `);
                    }
                });
            });
        });
    </script>
</body>
</html>
