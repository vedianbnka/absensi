<?php
session_start();
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'absensi';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}
?>
