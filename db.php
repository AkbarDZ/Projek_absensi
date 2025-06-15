<?php
$host = 'localhost';
$dbname = 'db_absensi_app';
$user = 'root';
$pass = ''; // or your password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Only show this if directly accessing db.php for testing
    if (basename($_SERVER['PHP_SELF']) == 'db.php') {
        echo "✅ Database connection successful.";
    }
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>