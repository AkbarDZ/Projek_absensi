<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nama = $_SESSION['nama'] ?? 'Unknown';
$role = $_SESSION['role'] ?? 'guest';
?>

<nav>
    <div class="nav-left">
        <a href="home.php">Home</a>
        <a href="statistik.php">Statistik</a>
        
        <?php if ($role === 'admin'): ?>
            <a href="config.php">Config</a>
        <?php endif; ?>
    </div>

    <div class="nav-right">
        <span style="float: right;">
            Logged in as: <strong><?php echo htmlspecialchars($nama); ?></strong> (<?php echo $role; ?>) |
            <a href="logout.php" class="logout-btn">Logout</a>
        </span>
    </div>
</nav>
