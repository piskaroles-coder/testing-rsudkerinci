<?php
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action == 'create') {
        // Create new information
        $judul = htmlspecialchars($_POST['judul'] ?? '');
        $isi = $_POST['isi'] ?? '';
        $kategori = htmlspecialchars($_POST['kategori'] ?? '');
        $penulis = htmlspecialchars($_POST['penulis'] ?? '');
        $sumber = htmlspecialchars($_POST['sumber'] ?? '');
        $status = htmlspecialchars($_POST['status'] ?? 'aktif');

        // Validation
        if (empty($judul) || empty($isi) || empty($kategori) || empty($penulis)) {
            echo json_encode(['success' => false, 'message' => 'Semua field yang wajib harus diisi']);
            exit;
        }

        try {
            $stmt = $conn->prepare("INSERT INTO informasi_publik (judul, isi, kategori, penulis, sumber, status) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $judul, $isi, $kategori, $penulis, $sumber, $status);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Informasi publik berhasil disimpan']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data: ' . $stmt->error]);
            }
            $stmt->close();
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } 
    elseif ($action == 'update') {
        // Update information
        $id = intval($_POST['id'] ?? 0);
        $judul = htmlspecialchars($_POST['judul'] ?? '');
        $isi = $_POST['isi'] ?? '';
        $kategori = htmlspecialchars($_POST['kategori'] ?? '');
        $penulis = htmlspecialchars($_POST['penulis'] ?? '');
        $sumber = htmlspecialchars($_POST['sumber'] ?? '');
        $status = htmlspecialchars($_POST['status'] ?? 'aktif');

        if (empty($id) || empty($judul) || empty($isi)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }

        try {
            $stmt = $conn->prepare("UPDATE informasi_publik 
                                   SET judul = ?, isi = ?, kategori = ?, penulis = ?, sumber = ?, status = ? 
                                   WHERE id = ?");
            $stmt->bind_param("ssssssi", $judul, $isi, $kategori, $penulis, $sumber, $status, $id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Informasi publik berhasil diperbarui']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data']);
            }
            $stmt->close();
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } 
    elseif ($action == 'delete') {
        // Delete information
        $id = intval($_POST['id'] ?? 0);

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit;
        }

        try {
            $stmt = $conn->prepare("DELETE FROM informasi_publik WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Informasi publik berhasil dihapus']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus data']);
            }
            $stmt->close();
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak didukung']);
}
?>
