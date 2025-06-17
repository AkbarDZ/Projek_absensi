<?php
session_start();
require 'db.php';
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Get POST data
if (!isset($_POST['latitude'], $_POST['longitude'], $_POST['action'])) {
    echo "❌ Location data missing.";
    exit;
}

$lat = (float)$_POST['latitude'];
$lng = (float)$_POST['longitude'];
$action = $_POST['action']; // 'in' or 'out'

// 🔄 Fetch office location and limit from DB
$stmt = $pdo->query("SELECT latitude, longitude, distance_limit_km FROM config WHERE id = 1");
$config = $stmt->fetch();

if (!$config) {
    echo "⚠️ Office location config not found.";
    exit;
}

$officeLat = $config['latitude'];
$officeLng = $config['longitude'];
$distanceLimit = $config['distance_limit_km'];

// 🔍 Distance calculation
function haversine($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // in kilometers
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $lat1 = deg2rad($lat1);
    $lat2 = deg2rad($lat2);

    $a = sin($dLat/2) * sin($dLat/2) +
         sin($dLon/2) * sin($dLon/2) * cos($lat1) * cos($lat2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earthRadius * $c;
}

$distance = haversine($officeLat, $officeLng, $lat, $lng);
if ($distance > $distanceLimit) {
    echo "❌ You're too far from the office to check in/out (Distance: " . round($distance, 2) . " km, limit: {$distanceLimit} km).";
    echo "<br><a href='../home.php'>Back</a>";
    exit;
}

// Attendance logic
$user_id = $_SESSION['user_id'];
$tanggal = date('Y-m-d');
$now = date('Y-m-d H:i:s');
$jam_sekarang = date('H:i:s');

// Check today's record
$stmt = $pdo->prepare("SELECT * FROM absensi WHERE user_id = ? AND tanggal = ?");
$stmt->execute([$user_id, $tanggal]);
$absen = $stmt->fetch();

if (!$absen && $action === 'in') {
    // FIRST TIME: Check-in
    $jam_telat = strtotime("09:00:00");
    $status = (strtotime($jam_sekarang) > $jam_telat) ? 'terlambat' : 'hadir';

    $insert = $pdo->prepare("
        INSERT INTO absensi (user_id, tanggal, jam_masuk, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $insert->execute([$user_id, $tanggal, $jam_sekarang, $status, $now, $now]);

    echo "✅ Check-in recorded at $jam_sekarang.";
} elseif ($absen && $absen['jam_keluar'] === null && $action === 'out') {
    // SECOND TIME: Check-out
    $update = $pdo->prepare("
        UPDATE absensi SET jam_keluar = ?, updated_at = ? 
        WHERE id = ?
    ");
    $update->execute([$jam_sekarang, $now, $absen['id']]);

    echo "✅ Check-out recorded at $jam_sekarang.";
} else {
    echo "⚠️ Attendance already recorded or invalid action.";
}

echo "<br><a href='../home.php'>Back</a>";

