<?php
// index.php
session_start();
include 'utils/db.php';

// Redirect jika belum login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$userName = htmlspecialchars($_SESSION['user']['name']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Absensi</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Navbar -->
    <header class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-800">Sistem Absensi</h1>
            <span class="text-gray-600">Halo, <?php echo $userName; ?>!</span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Absen Masuk -->
            <a href="absen_masuk.php" class="block bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-lg font-medium text-gray-700">Absen Masuk</span>
                </div>
            </a>

            <!-- Absen Pulang -->
            <a href="absen_pulang.php" class="block bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-lg font-medium text-gray-700">Absen Pulang</span>
                </div>
            </a>

            <!-- Cek Absensi -->
            <a href="cek_absensi.php" class="block bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2v-7H3v7a2 2 0 002 2z" />
                    </svg>
                    <span class="text-lg font-medium text-gray-700">Cek Absensi</span>
                </div>
            </a>

            <!-- Logout -->
            <a href="logout.php" class="block bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg>
                    <span class="text-lg font-medium text-gray-700">Logout</span>
                </div>
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-inner py-4">
        <div class="container mx-auto text-center text-gray-500 text-sm">
            &copy; <?php echo date('Y'); ?> Sistem Absensi Karyawan
        </div>
    </footer>
</body>
</html>
