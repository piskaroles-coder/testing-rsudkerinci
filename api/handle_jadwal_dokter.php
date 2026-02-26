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
    $dokter_id = intval($_POST['dokter_id'] ?? 0);
    $hari = htmlspecialchars($_POST['hari'] ?? '');
    $jam_mulai = htmlspecialchars($_POST['jam_mulai'] ?? '');
    $jam_selesai = htmlspecialchars($_POST['jam_selesai'] ?? '');
    $lokasi = htmlspecialchars($_POST['lokasi'] ?? '');
    $kuota = !empty($_POST['kuota']) ? intval($_POST['kuota']) : NULL;

    // Validation
    if (empty($dokter_id) || empty($hari) || empty($jam_mulai) || empty($jam_selesai) || empty($lokasi)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }

    // Verify doctor exists
    $stmt = $conn->prepare("SELECT id FROM dokter WHERE id = ?");
    $stmt->bind_param("i", $dokter_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Dokter tidak ditemukan']);
        exit();
    }

    // Check if schedule already exists
    $stmt = $conn->prepare("SELECT id FROM jadwal_dokter WHERE dokter_id = ? AND hari = ? AND jam_mulai = ? AND jam_selesai = ?");
    $stmt->bind_param("isss", $dokter_id, $hari, $jam_mulai, $jam_selesai);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Jadwal dokter ini sudah ada']);
        exit();
    }

    if ($kuota === NULL) {
        $stmt = $conn->prepare("INSERT INTO jadwal_dokter (dokter_id, hari, jam_mulai, jam_selesai, lokasi) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $dokter_id, $hari, $jam_mulai, $jam_selesai, $lokasi);
    } else {
        $stmt = $conn->prepare("INSERT INTO jadwal_dokter (dokter_id, hari, jam_mulai, jam_selesai, lokasi, kuota) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssi", $dokter_id, $hari, $jam_mulai, $jam_selesai, $lokasi, $kuota);
    }

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Jadwal dokter berhasil disimpan'];
    } else {
        $response = ['success' => false, 'message' => 'Gagal menyimpan jadwal: ' . $conn->error];
    }
}
elseif ($action === 'update') {
    $id = intval($_GET['id'] ?? 0);
    $dokter_id = intval($_POST['dokter_id'] ?? 0);
    $hari = htmlspecialchars($_POST['hari'] ?? '');
    $jam_mulai = htmlspecialchars($_POST['jam_mulai'] ?? '');
    $jam_selesai = htmlspecialchars($_POST['jam_selesai'] ?? '');
    $lokasi = htmlspecialchars($_POST['lokasi'] ?? '');
    $kuota = !empty($_POST['kuota']) ? intval($_POST['kuota']) : NULL;

    if (empty($id) || empty($dokter_id) || empty($hari) || empty($jam_mulai) || empty($jam_selesai) || empty($lokasi)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }

    // Verify doctor exists
    $stmt = $conn->prepare("SELECT id FROM dokter WHERE id = ?");
    $stmt->bind_param("i", $dokter_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Dokter tidak ditemukan']);
        exit();
    }

    // Check if same schedule already exists for another record
    $stmt = $conn->prepare("SELECT id FROM jadwal_dokter WHERE dokter_id = ? AND hari = ? AND jam_mulai = ? AND jam_selesai = ? AND id != ?");
    $stmt->bind_param("isssi", $dokter_id, $hari, $jam_mulai, $jam_selesai, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Jadwal dokter ini sudah ada']);
        exit();
    }

    if ($kuota === NULL) {
        $stmt = $conn->prepare("UPDATE jadwal_dokter SET dokter_id = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, lokasi = ? WHERE id = ?");
        $stmt->bind_param("issssi", $dokter_id, $hari, $jam_mulai, $jam_selesai, $lokasi, $id);
    } else {
        $stmt = $conn->prepare("UPDATE jadwal_dokter SET dokter_id = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, lokasi = ?, kuota = ? WHERE id = ?");
        $stmt->bind_param("issssi", $dokter_id, $hari, $jam_mulai, $jam_selesai, $lokasi, $kuota, $id);
    }

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Jadwal dokter berhasil diperbarui'];
    } else {
        $response = ['success' => false, 'message' => 'Gagal memperbarui jadwal: ' . $conn->error];
    }
}
elseif ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM jadwal_dokter WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Jadwal dokter berhasil dihapus'];
    } else {
        $response = ['success' => false, 'message' => 'Gagal menghapus jadwal: ' . $conn->error];
    }
}

echo json_encode($response);
