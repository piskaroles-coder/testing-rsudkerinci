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

$table = htmlspecialchars($_POST['table'] ?? '');
$id = intval($_POST['id'] ?? 0);
$allowed_tables = ['pasien', 'dokter', 'kontak', 'jadwal_dokter', 'berita', 'pengumuman'];

if (!in_array($table, $allowed_tables)) {
    echo json_encode(['success' => false, 'message' => 'Invalid table']);
    exit();
}

if (empty($id) || $id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit();
}

// Handle special cases where we need to delete related data
if ($table === 'dokter') {
    // Delete jadwal_dokter associated with this dokter
    $stmt = $conn->prepare("DELETE FROM jadwal_dokter WHERE dokter_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Delete the main record
$stmt = $conn->prepare("DELETE FROM `" . $table . "` WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Data berhasil dihapus']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus data: ' . $conn->error]);
}

$stmt->close();
$conn->close();
