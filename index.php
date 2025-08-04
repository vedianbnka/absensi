<?php include 'config/db.php'; if (!isset($_SESSION['user'])) header('Location: login.php'); ?>
<h2>Selamat Datang, <?= $_SESSION['user']['name'] ?></h2>
<ul>
    <li><a href="absen_masuk.php">Absen Masuk</a></li>
    <li><a href="absen_pulang.php">Absen Pulang</a></li>
    <li><a href="cek_absensi.php">Cek Absensi</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
