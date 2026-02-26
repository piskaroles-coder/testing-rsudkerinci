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
    $spesialisasi = htmlspecialchars($_POST['spesialisasi'] ?? '');
    $nip = htmlspecialchars($_POST['nip'] ?? '');
    $no_str = htmlspecialchars($_POST['no_str'] ?? '');
    $telepon = htmlspecialchars($_POST['telepon'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $alamat = htmlspecialchars($_POST['alamat'] ?? '');
    $status = htmlspecialchars($_POST['status'] ?? 'aktif');

    // Validation
    if (empty($nama) || empty($spesialisasi) || empty($nip) || empty($no_str) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }

    // Check if NIP already exists
    $stmt = $conn->prepare("SELECT id FROM dokter WHERE nip = ?");
    $stmt->bind_param("s", $nip);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'NIP sudah terdaftar']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO dokter (nama, spesialisasi, nip, no_str, telepon, email, alamat, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $nama, $spesialisasi, $nip, $no_str, $telepon, $email, $alamat, $status);

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Data dokter berhasil disimpan'];
    } else {
        $response = ['success' => false, 'message' => 'Gagal menyimpan data: ' . $conn->error];
    }
} 
elseif ($action === 'update') {
    $id = intval($_GET['id'] ?? 0);
    $nama = htmlspecialchars($_POST['nama'] ?? '');
    $spesialisasi = htmlspecialchars($_POST['spesialisasi'] ?? '');
    $nip = htmlspecialchars($_POST['nip'] ?? '');
    $no_str = htmlspecialchars($_POST['no_str'] ?? '');
    $telepon = htmlspecialchars($_POST['telepon'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $alamat = htmlspecialchars($_POST['alamat'] ?? '');
    $status = htmlspecialchars($_POST['status'] ?? 'aktif');

    if (empty($id) || empty($nama) || empty($spesialisasi) || empty($nip) || empty($no_str) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }

    // Check if NIP is already used by another doctor
    $stmt = $conn->prepare("SELECT id FROM dokter WHERE nip = ? AND id != ?");
    $stmt->bind_param("si", $nip, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'NIP sudah digunakan dokter lain']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE dokter SET nama = ?, spesialisasi = ?, nip = ?, no_str = ?, 
                           telepon = ?, email = ?, alamat = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssssssi", $nama, $spesialisasi, $nip, $no_str, $telepon, $email, $alamat, $status, $id);

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Data dokter berhasil diperbarui'];
    } else {
        $response = ['success' => false, 'message' => 'Gagal memperbarui data: ' . $conn->error];
    }
}
elseif ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit();
    }

    // Delete related schedules first
    $stmt = $conn->prepare("DELETE FROM jadwal_dokter WHERE dokter_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Delete the doctor
    $stmt = $conn->prepare("DELETE FROM dokter WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Data dokter berhasil dihapus'];
    } else {
        $response = ['success' => false, 'message' => 'Gagal menghapus data: ' . $conn->error];
    }
}

echo json_encode($response);
