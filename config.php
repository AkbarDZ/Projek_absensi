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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Config - Set Office Location</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 400px; width: 100%; margin-top: 20px; }
        /* General Reset & Typography */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f4f6f8;
    padding: 20px;
}

/* Navbar Styling */
nav {
    background-color: #2f3542;
    padding: 15px 30px;
    border-radius: 8px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.nav-left a {
    color: #ffffff;
    margin-right: 20px;
    text-decoration: none;
    font-weight: 500;
}

.nav-left a:hover {
    text-decoration: underline;
}

.nav-right {
    font-size: 0.9rem;
    color: #fff;
    margin-top: 10px;
}

/* Headings */
h2 {
    margin-bottom: 20px;
    color: #2f3542;
}

/* Form Styling */
form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    max-width: 600px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
}

label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 500;
}

input[type="number"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}

/* Button */
button {
    background-color: #1e90ff;
    border: none;
    color: white;
    padding: 12px 25px;
    text-align: center;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: rgb(65, 37, 223);
}

/* Map */
#map {
    height: 400px;
    width: 100%;
    margin-top: 20px;
    border-radius: 8px;
}

/* Status Message */
p {
    background-color: #ffffff;
    padding: 20px;
    border-left: 5px solid #2ed573;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    max-width: 500px;
    margin-top: 20px;
    line-height: 1.6;
}

/* Logout Button */
.logout-btn {
    background-color: #ff6b6b;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.85rem;
    margin-left: 10px;
}

.logout-btn:hover {
    background-color: #e84141;
}

/* Responsive */
@media (max-width: 600px) {
    nav {
        flex-direction: column;
        align-items: flex-start;
    }

    .nav-right {
        margin-top: 10px;
        text-align: left;
    }

    form {
        padding: 15px;
    }

    button {
        width: 100%;
    }
}


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
