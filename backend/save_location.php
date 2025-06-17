<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../home.php");
    exit;
}

require 'db.php';

$latitude = $_POST['latitude'] ?? null;
$longitude = $_POST['longitude'] ?? null;
$limit = $_POST['limit'] ?? null;

if ($latitude && $longitude && $limit) {
    $stmt = $pdo->prepare("
        INSERT INTO config (id, latitude, longitude, distance_limit_km)
        VALUES (1, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            latitude = VALUES(latitude),
            longitude = VALUES(longitude),
            distance_limit_km = VALUES(distance_limit_km),
            updated_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([$latitude, $longitude, $limit]);
}

header("Location: ../config.php");
exit;
