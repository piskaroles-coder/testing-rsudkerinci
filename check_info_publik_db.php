<?php
require 'config/database.php';

// Check apakah table informasi_publik ada
$result = $conn->query("SHOW TABLES LIKE 'informasi_publik'");

if ($result && $result->num_rows > 0) {
    echo "<h2 style='color: green;'>✅ Database Table 'informasi_publik' EXISTS</h2>";
    
    // Get table structure
    $structure = $conn->query("DESCRIBE informasi_publik");
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Count records
    $count = $conn->query("SELECT COUNT(*) as cnt FROM informasi_publik");
    $count_result = $count->fetch_assoc();
    echo "<p><strong>Total Records:</strong> {$count_result['cnt']}</p>";
} else {
    echo "<h2 style='color: red;'>❌ Database Table 'informasi_publik' NOT FOUND</h2>";
    echo "<p>Table akan dibuat otomatis saat mengakses input_info_publik.php</p>";
}
?>
<hr>
<p><a href="input_info_publik.php">Klik di sini untuk membuat table jika belum ada</a></p>
