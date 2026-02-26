<?php
header('Content-Type: application/json');

require_once '../config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and sanitize input data
    $nama = htmlspecialchars(trim($_POST['nama'] ?? ''));
    $noktp = htmlspecialchars(trim($_POST['noktp'] ?? ''));
    $tgllahir = $_POST['tgllahir'] ?? '';
    $jeniskelamin = $_POST['jeniskelamin'] ?? '';
    $goldarah = htmlspecialchars(trim($_POST['goldarah'] ?? ''));
    $agama = htmlspecialchars(trim($_POST['agama'] ?? ''));
    $alamat = htmlspecialchars(trim($_POST['alamat'] ?? ''));
    $kota = htmlspecialchars(trim($_POST['kota'] ?? ''));
    $kodepos = htmlspecialchars(trim($_POST['kodepos'] ?? ''));
    $telepon = htmlspecialchars(trim($_POST['telepon'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $penyakit = htmlspecialchars(trim($_POST['penyakit'] ?? ''));
    $alergi = htmlspecialchars(trim($_POST['alergi'] ?? ''));
    $namapj = htmlspecialchars(trim($_POST['namapj'] ?? ''));
    $hubungan = htmlspecialchars(trim($_POST['hubungan'] ?? ''));
    $teleponpj = htmlspecialchars(trim($_POST['teleponpj'] ?? ''));
    $alamatpj = htmlspecialchars(trim($_POST['alamatpj'] ?? ''));
    $asuransi = htmlspecialchars(trim($_POST['asuransi'] ?? ''));
    $nomorasuransi = htmlspecialchars(trim($_POST['nomorasuransi'] ?? ''));
    
    // Validation
    $errors = array();
    
    if (empty($nama)) {
        $errors[] = "Nama lengkap harus diisi";
    }
    
    if (empty($noktp)) {
        $errors[] = "Nomor KTP harus diisi";
    }
    
    if (empty($tgllahir)) {
        $errors[] = "Tanggal lahir harus diisi";
    }
    
    if (empty($jeniskelamin)) {
        $errors[] = "Jenis kelamin harus dipilih";
    }
    
    if (empty($alamat)) {
        $errors[] = "Alamat harus diisi";
    }
    
    if (empty($kota)) {
        $errors[] = "Kota/Kabupaten harus diisi";
    }
    
    if (empty($telepon)) {
        $errors[] = "Nomor telepon harus diisi";
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid";
    }
    
    // If validation fails
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $errors
        ]);
        exit;
    }
    
    // Check if KTP already exists
    $check_sql = "SELECT id FROM pasien WHERE noktp = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $noktp);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Nomor KTP sudah terdaftar di sistem kami'
        ]);
        $check_stmt->close();
        $conn->close();
        exit;
    }
    $check_stmt->close();
    
    // Prepare insert statement
    $insert_sql = "INSERT INTO pasien (nama, noktp, tgllahir, jeniskelamin, goldarah, agama, alamat, kota, kodepos, telepon, email, penyakit, alergi, namapj, hubungan, teleponpj, alamatpj, asuransi, nomorasuransi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert_sql);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Kesalahan server: ' . $conn->error
        ]);
        exit;
    }
    
    $stmt->bind_param(
        "sssssssssssssssssss",
        $nama,
        $noktp,
        $tgllahir,
        $jeniskelamin,
        $goldarah,
        $agama,
        $alamat,
        $kota,
        $kodepos,
        $telepon,
        $email,
        $penyakit,
        $alergi,
        $namapj,
        $hubungan,
        $teleponpj,
        $alamatpj,
        $asuransi,
        $nomorasuransi
    );
    
    // Execute statement
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Pendaftaran berhasil! Kami akan menghubungi Anda segera.',
            'patient_id' => $conn->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Kesalahan saat menyimpan data: ' . $stmt->error
        ]);
    }
    
    $stmt->close();
    $conn->close();
    
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}
?>
