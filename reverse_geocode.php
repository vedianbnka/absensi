<?php
$lat = $_GET['lat'];
$lon = $_GET['lon'];
$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lon";
$response = file_get_contents($url);
$data = json_decode($response, true);
echo $data['display_name'] ?? "Lokasi tidak ditemukan";
