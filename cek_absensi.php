<?php
include 'utils/db.php'; 
if (!isset($_SESSION['user'])) header('Location: login.php');

$userId = $_SESSION['user']['id'];
$query = "SELECT * FROM absensi WHERE user_id = $userId ORDER BY tanggal DESC";
$result = $conn->query($query);

// Misalnya kamu ingin menampilkan pesan error (jika dikirim via GET misalnya)
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">Riwayat Absensi</h1>

        <?php if ($error): ?>
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700 border border-red-300">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif ($success): ?>
            <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 border border-green-300">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">ID</th>
                        <th class="py-3 px-4 text-left">Nama</th>
                        <th class="py-3 px-4 text-left">Jam Masuk</th>
                        <th class="py-3 px-4 text-left">Jam Pulang</th>
                        <th class="py-3 px-4 text-left">Lokasi Masuk</th>
                        <th class="py-3 px-4 text-left">Lokasi Pulang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="py-3 px-4"><?= $row['id'] ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($_SESSION['user']['name']) ?></td>
                            <td class="py-3 px-4"><?= $row['jam_masuk'] ?></td>
                            <td class="py-3 px-4"><?= $row['jam_pulang'] ?: '-' ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['lokasi_masuk']) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($row['lokasi_pulang']) ?: '-' ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
