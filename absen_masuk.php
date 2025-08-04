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

        $stmt = $conn->prepare("SELECT * FROM absensi WHERE user_id = ? AND tanggal = ? AND jam_masuk IS NOT NULL AND jam_pulang IS NULL");
        $stmt->bind_param("is", $userId, $tanggal);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
                $error = 'Anda sudah absen masuk hari ini tetapi belum absen pulang.';
            
        } else {
            $base = 'http://localhost/absensi/reverse_geocode.php';
            $lokasi = file_get_contents("{$base}?lat={$lat}&lon={$lon}");

            $jam = date("Y-m-d H:i:s");

            $stmt = $conn->prepare("
            INSERT INTO absensi (user_id, tanggal, jam_masuk, lat_masuk, lon_masuk, lokasi_masuk)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

            if (!$stmt) {
                $error = 'Error preparing statement: ' . $conn->error;
            } else {
                $stmt->bind_param("issdds", $userId, $tanggal, $jam, $lat, $lon, $lokasi);

                if ($stmt->execute()) {
                    $success = 'Absen masuk berhasil pada ' . date('H:i:s') . ' dengan lokasi: ' . htmlspecialchars($lokasi);
                } else {
                    $error = 'Error executing statement: ' . $stmt->error . ' | SQL Error: ' . $conn->error;
                }
                $stmt->close();
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

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 250px;
            border-radius: 0.75rem;
        }

        /* Tailwind rounded-xl */
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-6 space-y-4">
        <h1 class="text-2xl font-semibold text-gray-800 text-center">Absen Masuk</h1>

        <!-- MAP -->
        <div id="map" class="w-full bg-gray-200"></div>

        <?php if ($success): ?>
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php elseif ($error): ?>
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded">
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

        <div class="text-center">
            <a href="index.php" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        const statusEl = document.getElementById('status');
        const btn = document.getElementById('btnSubmit');
        const latInput = document.getElementById('lat');
        const lonInput = document.getElementById('lon');

        let map, marker;

        function initMap(lat = 0, lon = 0) {
            map = L.map('map').setView([lat, lon], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            marker = L.marker([lat, lon]).addTo(map);
        }

        function updateMap(lat, lon) {
            if (map && marker) {
                marker.setLatLng([lat, lon]);
                map.setView([lat, lon], 17);
            }
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    const { latitude, longitude } = pos.coords;
                    latInput.value = latitude;
                    lonInput.value = longitude;

                    statusEl.textContent = `Lokasi terdeteksi: (${latitude.toFixed(5)}, ${longitude.toFixed(5)})`;
                    btn.disabled = false;

                    initMap(latitude, longitude);
                },
                err => {
                    statusEl.textContent = 'Gagal mengakses lokasi. Izinkan akses lokasi di browser.';
                    initMap(); // Default map
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            statusEl.textContent = 'Geolocation tidak didukung oleh browser.';
        }
    </script>
</body>

</html>