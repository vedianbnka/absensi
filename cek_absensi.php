<?php
include 'utils/db.php'; 
if (!isset($_SESSION['user'])) header('Location: login.php');

$userId = $_SESSION['user']['id'];
$query = "SELECT * FROM absensi WHERE user_id = $userId ORDER BY tanggal DESC";
$result = $conn->query($query);

echo "<table border='1'>
<tr><th>ID</th><th>Nama</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Lokasi Masuk</th><th>Lokasi Pulang</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$_SESSION['user']['name']}</td>
        <td>{$row['jam_masuk']}</td>
        <td>{$row['jam_pulang']}</td>
        <td>{$row['lokasi_masuk']}</td>
        <td>{$row['lokasi_pulang']}</td>
    </tr>";
}
echo "</table>";
?>
