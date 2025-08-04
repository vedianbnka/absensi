<?php
require_once 'utils/db.php';

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $res = $stmt->get_result();
  $user = $res->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    if ($user['role'] === 'admin') {
      header('Location: admin/index.php');
      exit();
    } else if ($user['role'] === 'karyawan') {
      header('Location: index.php');
      exit();
    }
  } else {
    $loginError = 'Username atau Password salah!';
  }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Absensi</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-blue-50 to-blue-200 min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-sm bg-white rounded-2xl shadow-md p-8">
    <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">
      Login Sistem Absensi
    </h2>
    <?php if ($loginError): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($loginError) ?>
      </div>
    <?php endif; ?>
    <form method="POST" class="space-y-4">
      <div>
        <label for="username" class="block text-gray-700 mb-1">Username</label>
        <input id="username" name="username" type="text" placeholder="Masukkan username" required autofocus
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
      <div>
        <label for="password" class="block text-gray-700 mb-1">Password</label>
        <input id="password" name="password" type="password" placeholder="Masukkan password" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
      <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        Masuk
      </button>
    </form>
  </div>
</body>

</html>