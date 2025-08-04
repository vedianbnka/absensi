<?php
include '../utils/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$id = intval($_GET['id'] ?? 0);

// Ambil data karyawan
$employee = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();
if (!$employee) {
    die("Karyawan tidak ditemukan.");
}

// Ambil data absensinya
$absensi = $conn->query("SELECT * FROM absensi WHERE user_id = $id ORDER BY tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi <?= htmlspecialchars($employee['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Absensi: <?= htmlspecialchars($employee['name']) ?></h1>
            <a href="index.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">← Kembali ke Dashboard</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg overflow-hidden text-sm">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Tanggal</th>
                        <th class="py-3 px-4 text-left">Jam Masuk</th>
                        <th class="py-3 px-4 text-left">Jam Pulang</th>
                        <th class="py-3 px-4 text-left">Lokasi Masuk</th>
                        <th class="py-3 px-4 text-left">Lokasi Pulang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($row = $absensi->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="py-3 px-4"><?= $row['tanggal'] ?></td>
                            <td class="py-3 px-4"><?= $row['jam_masuk'] ?></td>
                            <td class="py-3 px-4"><?= $row['jam_pulang'] ?: '-' ?></td>
                            <td class="py-3 px-4">
                                <?php if ($row['lat_masuk'] && $row['lon_masuk']): ?>
                                    <span class="text-blue-600 cursor-pointer hover:underline"
                                          onclick="showMap(<?= htmlspecialchars($row['lat_masuk']) ?>, <?= htmlspecialchars($row['lon_masuk']) ?>)">
                                        <?= htmlspecialchars($row['lokasi_masuk']) ?>
                                    </span>
                                <?php else: ?>
                                    <?= htmlspecialchars($row['lokasi_masuk']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php if ($row['lat_pulang'] && $row['lon_pulang']): ?>
                                    <span class="text-blue-600 cursor-pointer hover:underline"
                                          onclick="showMap(<?= htmlspecialchars($row['lat_pulang']) ?>, <?= htmlspecialchars($row['lon_pulang']) ?>)">
                                        <?= htmlspecialchars($row['lokasi_pulang'] ?: '-') ?>
                                    </span>
                                <?php else: ?>
                                    <?= htmlspecialchars($row['lokasi_pulang'] ?: '-') ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Modal Peta -->
            <div id="mapModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg overflow-hidden w-96 shadow-lg">
                    <div class="p-4 font-semibold border-b">Lokasi pada Peta</div>
                    <div id="map" style="height: 300px;"></div>
                    <div class="p-4 text-right border-t">
                        <button onclick="closeMap()" class="text-blue-600 hover:underline">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let mapInstance = null;

        function showMap(lat, lon) {
            document.getElementById('mapModal').classList.remove('hidden');

            if (mapInstance) {
                mapInstance.remove();
            }

            mapInstance = L.map('map').setView([lat, lon], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(mapInstance);

            L.marker([lat, lon]).addTo(mapInstance)
                .bindPopup("Lokasi Absen")
                .openPopup();
        }

        function closeMap() {
            document.getElementById('mapModal').classList.add('hidden');
        }
    </script>
</body>
</html>
