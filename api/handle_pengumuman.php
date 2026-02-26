<?php
header('Content-Type: application/json');

session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    http_response_code(401);
    exit;
}

require_once '../config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and sanitize input data
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $judul = htmlspecialchars(trim($_POST['judul'] ?? ''));
    $isi = $_POST['isi'] ?? '';
    $tanggal_publikasi = $_POST['tanggal_publikasi'] ?? '';
    $penulis = htmlspecialchars(trim($_POST['penulis'] ?? ''));
    $status = $_POST['status'] ?? 'aktif';
    
    // Validation
    $errors = array();
    
    if (empty($judul)) {
        $errors[] = "Judul pengumuman harus diisi";
    }
    
    if (empty($isi) || $isi === '<p><br></p>') {
        $errors[] = "Isi pengumuman harus diisi";
    }
    
    if (empty($tanggal_publikasi)) {
        $errors[] = "Tanggal publikasi harus diisi";
    }
    
    if (!in_array($status, ['aktif', 'arsip'])) {
        $errors[] = "Status tidak valid";
    }
    
    // If there are errors, return error response
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        http_response_code(400);
        exit;
    }
    
    // Format tanggal_publikasi
    $tanggal_publikasi = date('Y-m-d H:i:s', strtotime($tanggal_publikasi));
    
    try {
        if ($id) {
            // Update mode
            $query = "UPDATE pengumuman SET judul = ?, isi = ?, tanggal_publikasi = ?, penulis = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssi", $judul, $isi, $tanggal_publikasi, $penulis, $status, $id);
        } else {
            // Create mode
            $query = "INSERT INTO pengumuman (judul, isi, tanggal_publikasi, penulis, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $judul, $isi, $tanggal_publikasi, $penulis, $status);
        }
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => ($id ? 'Pengumuman berhasil diperbarui' : 'Pengumuman berhasil ditambahkan'),
                'redirect' => 'admin_data.php?table=pengumuman'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $stmt->error
            ]);
            http_response_code(400);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
        http_response_code(500);
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    http_response_code(405);
}
?>
