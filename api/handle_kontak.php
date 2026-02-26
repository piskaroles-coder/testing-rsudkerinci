<?php
header('Content-Type: application/json');

require_once '../config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and sanitize input data
    $nama = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $pesan = htmlspecialchars(trim($_POST['message'] ?? ''));
    
    // Validation
    $errors = array();
    
    if (empty($nama)) {
        $errors[] = "Nama harus diisi";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid atau kosong";
    }
    
    if (empty($pesan)) {
        $errors[] = "Pesan harus diisi";
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
    
    // Prepare insert statement
    $insert_sql = "INSERT INTO kontak (nama, email, pesan) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($insert_sql);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Kesalahan server: ' . $conn->error
        ]);
        exit;
    }
    
    $stmt->bind_param("sss", $nama, $email, $pesan);
    
    // Execute statement
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Pesan Anda telah terkirim! Terima kasih telah menghubungi kami.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Kesalahan saat menyimpan pesan: ' . $stmt->error
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
