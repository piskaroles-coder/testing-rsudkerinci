<?php
header('Content-Type: application/json');
session_start();
require '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

if ($action === 'create') {
    $nama = htmlspecialchars($_POST['nama'] ?? '');
    $noktp = htmlspecialchars($_POST['noktp'] ?? '');
    $tgllahir = htmlspecialchars($_POST['tgllahir'] ?? '');
    $jeniskelamin = htmlspecialchars($_POST['jeniskelamin'] ?? '');
    $goldarah = htmlspecialchars($_POST['goldarah'] ?? '');
    $agama = htmlspecialchars($_POST['agama'] ?? '');
    $alamat = htmlspecialchars($_POST['alamat'] ?? '');
    $kota = htmlspecialchars($_POST['kota'] ?? '');
    $kodepos = htmlspecialchars($_POST['kodepos'] ?? '');
    $telepon = htmlspecialchars($_POST['telepon'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $penyakit = htmlspecialchars($_POST['penyakit'] ?? '');
    $alergi = htmlspecialchars($_POST['alergi'] ?? '');
    $namapj = htmlspecialchars($_POST['namapj'] ?? '');
    $hubungan = htmlspecialchars($_POST['hubungan'] ?? '');
    $teleponpj = htmlspecialchars($_POST['teleponpj'] ?? '');
    $alamatpj = htmlspecialchars($_POST['alamatpj'] ?? '');
    $asuransi = htmlspecialchars($_POST['asuransi'] ?? '');
    $nomorasuransi = htmlspecialchars($_POST['nomorasuransi'] ?? '');
    $status = htmlspecialchars($_POST['status'] ?? 'aktif');

    // Validation
    if (empty($nama) || empty($tgllahir) || empty($jeniskelamin) || empty($alamat) || empty($kota) || empty($telepon) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }

    // Check if NoKTP already exists if provided
    if (!empty($noktp)) {
        $stmt = $conn->prepare("SELECT id FROM pasien WHERE noktp = ?");
        $stmt->bind_param("s", $noktp);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'No. KTP sudah terdaftar']);
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO pasien (nama, noktp, tgllahir, jeniskelamin, goldarah, agama, alamat, kota, kodepos, telepon, email, penyakit, alergi, namapj, hubungan, teleponpj, alamatpj, asuransi, nomorasuransi, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssssssssss", $nama, $noktp, $tgllahir, $jeniskelamin, $goldarah, $agama, $alamat, $kota, $kodepos, $telepon, $email, $penyakit, $alergi, $namapj, $hubungan, $teleponpj, $alamatpj, $asuransi, $nomorasuransi, $status);

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Data pasien berhasil disimpan'];
    } else {
        $response = ['success' => false, 'message' => 'Gagal menyimpan data: ' . $conn->error];
    }
}
elseif ($action === 'update') {
    $id = intval($_GET['id'] ?? 0);
    $nama = htmlspecialchars($_POST['nama'] ?? '');
    $noktp = htmlspecialchars($_POST['noktp'] ?? '');
    $tgllahir = htmlspecialchars($_POST['tgllahir'] ?? '');
    $jeniskelamin = htmlspecialchars($_POST['jeniskelamin'] ?? '');
    $goldarah = htmlspecialchars($_POST['goldarah'] ?? '');
    $agama = htmlspecialchars($_POST['agama'] ?? '');
    $alamat = htmlspecialchars($_POST['alamat'] ?? '');
    $kota = htmlspecialchars($_POST['kota'] ?? '');
    $kodepos = htmlspecialchars($_POST['kodepos'] ?? '');
    $telepon = htmlspecialchars($_POST['telepon'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $penyakit = htmlspecialchars($_POST['penyakit'] ?? '');
    $alergi = htmlspecialchars($_POST['alergi'] ?? '');
    $namapj = htmlspecialchars($_POST['namapj'] ?? '');
    $hubungan = htmlspecialchars($_POST['hubungan'] ?? '');
    $teleponpj = htmlspecialchars($_POST['teleponpj'] ?? '');
    $alamatpj = htmlspecialchars($_POST['alamatpj'] ?? '');
    $asuransi = htmlspecialchars($_POST['asuransi'] ?? '');
    $nomorasuransi = htmlspecialchars($_POST['nomorasuransi'] ?? '');
    $status = htmlspecialchars($_POST['status'] ?? 'aktif');

    if (empty($id) || empty($nama) || empty($tgllahir) || empty($jeniskelamin) || empty($alamat) || empty($kota) || empty($telepon) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }

    // Check if NoKTP is already used by another pasien
    if (!empty($noktp)) {
        $stmt = $conn->prepare("SELECT id FROM pasien WHERE noktp = ? AND id != ?");
        $stmt->bind_param("si", $noktp, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'No. KTP sudah digunakan pasien lain']);
            exit();
        }
    }

    $stmt = $conn->prepare("UPDATE pasien SET nama = ?, noktp = ?, tgllahir = ?, jeniskelamin = ?, goldarah = ?, agama = ?, alamat = ?, kota = ?, kodepos = ?, telepon = ?, email = ?, penyakit = ?, alergi = ?, namapj = ?, hubungan = ?, teleponpj = ?, alamatpj = ?, asuransi = ?, nomorasuransi = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssssssssssssssssssi", $nama, $noktp, $tgllahir, $jeniskelamin, $goldarah, $agama, $alamat, $kota, $kodepos, $telepon, $email, $penyakit, $alergi, $namapj, $hubungan, $teleponpj, $alamatpj, $asuransi, $nomorasuransi, $status, $id);

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Data pasien berhasil diperbarui'];
    } else {
        $response = ['success' => false, 'message' => 'Gagal memperbarui data: ' . $conn->error];
    }
}

echo json_encode($response);
