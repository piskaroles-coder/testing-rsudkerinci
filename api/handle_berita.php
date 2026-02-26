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
    $konten = $_POST['konten'] ?? '';
    $tanggal_publikasi = $_POST['tanggal_publikasi'] ?? '';
    $penulis = htmlspecialchars(trim($_POST['penulis'] ?? ''));
    $status = $_POST['status'] ?? 'draft';
    
    // Validation
    $errors = array();
    
    if (empty($judul)) {
        $errors[] = "Judul berita harus diisi";
    }
    
    if (empty($konten) || $konten === '<p><br></p>') {
        $errors[] = "Konten berita harus diisi";
    }
    
    if (empty($tanggal_publikasi)) {
        $errors[] = "Tanggal publikasi harus diisi";
    }
    
    if (!in_array($status, ['publikasi', 'draft'])) {
        $errors[] = "Status tidak valid";
    }
    
    // Check gambar if uploaded
    $gambar_path = null;
    if (!empty($_FILES['gambar']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['gambar']['type'], $allowed_types)) {
            $errors[] = "Tipe gambar tidak didukung. Gunakan JPG, PNG, atau GIF.";
        }
        
        if ($_FILES['gambar']['size'] > $max_size) {
            $errors[] = "Ukuran gambar terlalu besar. Maksimal 2MB.";
        }
        
        if (empty($errors)) {
            // Upload gambar
            $uploads_dir = '../uploads/berita';
            
            // Create directory if not exists
            if (!is_dir($uploads_dir)) {
                mkdir($uploads_dir, 0755, true);
            }
            
            $filename = time() . '_' . basename($_FILES['gambar']['name']);
            $filepath = $uploads_dir . '/' . $filename;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $filepath)) {
                $gambar_path = 'uploads/berita/' . $filename;
            } else {
                $errors[] = "Gagal mengupload gambar";
            }
        }
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
            if ($gambar_path) {
                $query = "UPDATE berita SET judul = ?, konten = ?, gambar = ?, tanggal_publikasi = ?, penulis = ?, status = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssssi", $judul, $konten, $gambar_path, $tanggal_publikasi, $penulis, $status, $id);
            } else {
                $query = "UPDATE berita SET judul = ?, konten = ?, tanggal_publikasi = ?, penulis = ?, status = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssssi", $judul, $konten, $tanggal_publikasi, $penulis, $status, $id);
            }
        } else {
            // Create mode
            $query = "INSERT INTO berita (judul, konten, gambar, tanggal_publikasi, penulis, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssss", $judul, $konten, $gambar_path, $tanggal_publikasi, $penulis, $status);
        }
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => ($id ? 'Berita berhasil diperbarui' : 'Berita berhasil ditambahkan'),
                'redirect' => 'admin_data.php?table=berita'
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
