<?php
include 'utils/db.php';

// Redirect jika belum login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lat = $_POST['latitude'] ?? '';
    $lon = $_POST['longitude'] ?? '';

    if (!$lat || !$lon) {
        $error = 'Gagal mendapatkan lokasi. Pastikan Anda mengizinkan akses lokasi.';
    } else {
        // Reverse geocode

        // cek apakah user telah melakukan absen masuk hari ini
        $userId = $_SESSION['user']['id'];
        $tanggal = date("Y-m-d");

        $stmt = $conn->prepare("SELECT * FROM absensi WHERE user_id = ? AND  jam_masuk IS NOT NULL AND jam_pulang IS NULL AND tanggal = ?");
        $stmt->bind_param("is", $userId, $tanggal);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0) {
            $error = 'Anda belum absen pulang.';
            
        } else {
            $base = 'http://localhost/absensi/reverse_geocode.php';
            $lokasi = file_get_contents("{$base}?lat={$lat}&lon={$lon}");

            $jam = date("Y-m-d H:i:s");

            $stmt = $conn->prepare("
            INSERT INTO absensi (user_id, tanggal, jam_masuk, lokasi_masuk)
            VALUES (?, ?, ?, ?)
        ");
            $stmt->bind_param("isss", $userId, $tanggal, $jam, $lokasi);

            if ($stmt->execute()) {
                $success = 'Absen masuk berhasil pada ' . date('H:i:s') . ' dengan lokasi: ' . htmlspecialchars($lokasi);
            } else {
                $error = 'Terjadi kesalahan, silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absen Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Absen Masuk</h1>

        <?php if ($success): ?>
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php elseif ($error): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="absenForm" class="space-y-4">
            <input type="hidden" name="latitude" id="lat">
            <input type="hidden" name="longitude" id="lon">

            <div id="status" class="text-center text-gray-600">Menunggu akses lokasi…</div>

            <button type="submit" id="btnSubmit"
                class="w-full py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
                disabled>
                Absen Sekarang
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="index.php" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>
        </div>
    </div>

    <script>
        const statusEl = document.getElementById('status');
        const btn = document.getElementById('btnSubmit');
        const latInput = document.getElementById('lat');
        const lonInput = document.getElementById('lon');

        // Minta izin dan ambil posisi
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    const { latitude, longitude } = pos.coords;
                    latInput.value = latitude;
                    lonInput.value = longitude;
                    statusEl.textContent = `Lokasi terdeteksi: (${latitude.toFixed(5)}, ${longitude.toFixed(5)})`;
                    btn.disabled = false;
                },
                err => {
                    statusEl.textContent = 'Gagal mengakses lokasi. Izinkan akses lokasi di browser.';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            statusEl.textContent = 'Geolocation tidak didukung oleh browser.';
        }
    </script>
</body>

</html>