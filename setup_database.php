<?php
require_once 'utils/db.php';

echo "<h2>Setup Database</h2>";

// Buat database jika belum ada
$conn->query("CREATE DATABASE IF NOT EXISTS absensi");
$conn->select_db('absensi');

// Buat tabel users
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users)) {
    echo "<p>✅ Tabel users berhasil dibuat/sudah ada</p>";
} else {
    echo "<p>❌ Error membuat tabel users: " . $conn->error . "</p>";
}

// Buat tabel absensi
$sql_absensi = "CREATE TABLE IF NOT EXISTS absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk DATETIME NULL,
    jam_pulang DATETIME NULL,
    lat_masuk DECIMAL(10, 8) NULL,
    lon_masuk DECIMAL(11, 8) NULL,
    lat_pulang DECIMAL(10, 8) NULL,
    lon_pulang DECIMAL(11, 8) NULL,
    lokasi_masuk TEXT NULL,
    lokasi_pulang TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, tanggal)
)";

if ($conn->query($sql_absensi)) {
    echo "<p>✅ Tabel absensi berhasil dibuat/sudah ada</p>";
} else {
    echo "<p>❌ Error membuat tabel absensi: " . $conn->error . "</p>";
}

// Cek apakah ada user test
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Buat user test
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $name = 'Administrator';
    
    $stmt = $conn->prepare("INSERT INTO users (username, password, name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $name);
    
    if ($stmt->execute()) {
        echo "<p>✅ User test berhasil dibuat (username: admin, password: admin123)</p>";
    } else {
        echo "<p>❌ Error membuat user test: " . $stmt->error . "</p>";
    }
}

// Tampilkan struktur tabel
echo "<h3>Struktur Tabel Absensi:</h3>";
$result = $conn->query("DESCRIBE absensi");
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><a href='login.php'>Kembali ke Login</a></p>";
?>
