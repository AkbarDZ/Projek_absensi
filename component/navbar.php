<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nama = $_SESSION['nama'] ?? 'Unknown';
$role = $_SESSION['role'] ?? 'guest';
?>

<nav>
    <a href="home.php">Home</a>
    <a href="statistik.php">Statistik</a>
    <?php if ($role === 'admin'): ?>
        <a href="config.php">Config</a>
    <?php endif; ?>
    <span style="float: right;">
        Logged in as: <strong><?= htmlspecialchars($nama) ?></strong> (<?= $role ?>) |
        <a href="logout.php">Logout</a>
    </span>
</nav>
