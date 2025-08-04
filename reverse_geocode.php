<?php
$lat = $_GET['lat'];
$lon = $_GET['lon'];

$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lon";

// Tambahkan User-Agent
$options = [
    "http" => [
        "header" => "User-Agent: Absensi/1.0 \r\n"
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);


$data = json_decode($response, true);

echo $data['display_name'] ?? null;
?>
