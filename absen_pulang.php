<?php
include 'config/db.php';
if (!isset($_SESSION['user']))
    header('Location: login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lat = $_POST['latitude'];
    $long = $_POST['longitude'];
    $lokasi = file_get_contents("reverse_geocode.php?lat=$lat&lon=$long");

    $userId = $_SESSION['user']['id'];
    $tanggal = date("Y-m-d");
    $jam = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("UPDATE absensi SET jam_pulang = ?, lokasi_pulang = ? WHERE user_id = ? AND tanggal = ?");
    $stmt->bind_param("ssis", $jam, $lokasi, $userId, $tanggal);
    $stmt->execute();

    echo "Absen masuk berhasil!";
}
?>

<script>
    navigator.geolocation.getCurrentPosition(function (position) {
        document.getElementById('lat').value = position.coords.latitude;
        document.getElementById('lon').value = position.coords.longitude;
    });
</script>

<form method="POST">
    <input type="hidden" name="latitude" id="lat">
    <input type="hidden" name="longitude" id="lon">
    <button type="submit">Absen Masuk</button>
</form>