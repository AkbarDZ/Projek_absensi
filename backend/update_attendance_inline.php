<?php
require 'db.php';

$id = $_POST['id'] ?? null;
$field = $_POST['field'] ?? null;
$value = $_POST['value'] ?? null;

$allowedFields = ['jam_masuk', 'jam_keluar', 'status'];
if (in_array($field, ['jam_masuk', 'jam_keluar'])) {
    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
        echo "Invalid time format. Use HH:MM:SS";
        exit;
    }
}

$stmt = $pdo->prepare("UPDATE absensi SET $field = ?, updated_at = NOW() WHERE id = ?");
$stmt->execute([$value, $id]);

echo json_encode(['success' => true]);