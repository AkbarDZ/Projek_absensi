<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit;
}
require 'backend/db.php';

$stmt = $pdo->query("SELECT * FROM config WHERE id = 1");
$config = $stmt->fetch();

$latitude = $config ? $config['latitude'] : -6.200000;
$longitude = $config ? $config['longitude'] : 106.816666;
$limit = $config ? $config['distance_limit_km'] : 1.0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Config - Set Office Location</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 400px; width: 100%; margin-top: 20px; }
        nav { background-color: #eee; padding: 10px; }
        nav a { margin-right: 15px; text-decoration: none; }
    </style>
</head>
<body>

<?php include 'component/navbar.php'; ?>

<h2>Set Office Location & Check-in Distance Limit</h2>

<form method="POST" action="backend/save_location.php">
    <input type="hidden" name="latitude" id="latitude" value="<?= $latitude ?>">
    <input type="hidden" name="longitude" id="longitude" value="<?= $longitude ?>">

    <label>Distance Limit (KM):</label>
    <input type="number" name="limit" step="0.1" min="0.1" value="<?= $limit ?>" required>

    <div id="map"></div><br>
    <button type="submit">Save</button>
</form>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([<?= $latitude ?>, <?= $longitude ?>], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const marker = L.marker([<?= $latitude ?>, <?= $longitude ?>], { draggable: true }).addTo(map);
    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        document.getElementById('latitude').value = pos.lat;
        document.getElementById('longitude').value = pos.lng;
    });
</script>

</body>
</html>
