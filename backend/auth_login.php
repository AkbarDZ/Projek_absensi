<?php
session_start();
require 'db.php';

$identifier = $_POST['identifier'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($identifier) || empty($password)) {
    header("Location: ../login.php?error=Please fill in all fields");
    exit;
}

// Query by name or email
$stmt = $pdo->prepare("SELECT * FROM user WHERE email = :id OR nama = :id LIMIT 1");
$stmt->execute(['id' => $identifier]);
$user = $stmt->fetch();

if ($user && $password === $user['password']) {
    // Login success
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];
    header("Location: ../home.php");
    exit;
} else {
    // Invalid login
    header("Location: ../login.php?error=Invalid credentials");
    exit;
}
