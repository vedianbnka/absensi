<?php
include '../utils/db.php';
session_start();

// Redirect jika belum login atau bukan admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Ambil semua karyawan
$employees = $conn->query("SELECT * FROM users WHERE role = 'karyawan'");

// Proses Hapus Karyawan
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $id");
    $conn->query("DELETE FROM absensi WHERE user_id = $id");
    header("Location: index.php");
    exit();
}

// Proses Tambah Karyawan
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $name = $_POST['name'] ?? '';

    if ($username && $passwordInput && $name) {
        // Cek apakah username sudah digunakan
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = 'Username sudah digunakan.';
        } else {
            $password = password_hash($passwordInput, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, 'karyawan')");
            $stmt->bind_param("sss", $username, $password, $name);
            $stmt->execute();
            $success = 'Karyawan berhasil ditambahkan.';
            header("Location: index.php?success=1");
            exit();
        }
    } else {
        $error = 'Semua field harus diisi.';
    }
}


$userName = htmlspecialchars($_SESSION['user']['name']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Navbar -->
    <header class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-800">Dashboard Admin</h1>
            <div class="flex items-center gap-4">
                <span class="text-gray-600">Halo, <?= $userName ?>!</span>
                <a href="../logout.php" class="text-red-600 hover:underline">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-6 space-y-6">
        <!-- Form Tambah Karyawan -->
        <div class="bg-white p-6 rounded-2xl shadow">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Tambah Karyawan</h2>
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= $error ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">Karyawan berhasil ditambahkan.</div>
            <?php endif; ?>

            <form method="POST" class="grid gap-4 sm:grid-cols-3">
                <input type="text" name="name" placeholder="Nama" required class="border p-2 rounded w-full">
                <input type="text" name="username" placeholder="Username" required class="border p-2 rounded w-full">
                <input type="password" name="password" placeholder="Password" required
                    class="border p-2 rounded w-full">
                <div class="sm:col-span-3">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah</button>
                </div>
            </form>
        </div>

        <!-- Tabel Karyawan -->
        <div class="bg-white p-6 rounded-2xl shadow">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Daftar Karyawan</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 border">Nama</th>
                            <th class="p-3 border">Username</th>
                            <th class="p-3 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $employees->fetch_assoc()): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-3 border"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-3 border"><?= htmlspecialchars($row['username']) ?></td>
                                <td class="p-3 border">
                                    <a href="index.php?delete=<?= $row['id'] ?>" class="text-red-600 hover:underline mr-2"
                                        onclick="return confirm('Hapus karyawan ini?')">Hapus</a>
                                    <a href="lihat_absensi.php?id=<?= $row['id'] ?>"
                                        class="text-blue-600 hover:underline">Lihat Absensi</a>
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-inner py-4">
        <div class="container mx-auto text-center text-gray-500 text-sm">
            &copy; <?= date('Y') ?> Sistem Absensi Karyawan
        </div>
    </footer>
</body>

</html>