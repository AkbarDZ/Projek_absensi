<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';
date_default_timezone_set('Asia/Jakarta');

$nama = $_SESSION['nama'];
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$tanggal = date('Y-m-d');

// Check today's attendance
$stmt = $pdo->prepare("SELECT * FROM absensi WHERE user_id = ? AND tanggal = ?");
$stmt->execute([$user_id, $tanggal]);
$absen = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Attendance App</title>
    <style>
        nav {
            background-color: #eee;
            padding: 10px;
        }
        nav a {
            margin-right: 15px;
            text-decoration: none;
        }
        button {
            padding: 10px 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<nav>
    <a href="home.php">Home</a>
    <a href="statistik.php">Statistik</a>
    <span style="float: right;">
        Logged in as: <strong><?php echo htmlspecialchars($nama); ?></strong> (<?php echo $role; ?>) |
        <a href="logout.php">Logout</a>
    </span>
</nav>

<h2>Welcome, <?php echo htmlspecialchars($nama); ?>!</h2>

<?php if (!$absen): ?>
    <!-- User has not checked in -->
    <button onclick="submitAttendance('in')">Check In</button>
<?php elseif ($absen['jam_keluar'] === null): ?>
    <!-- User has checked in but not out -->
    <button onclick="submitAttendance('out')">Check Out</button>
<?php else: ?>
    <!-- User already checked in and out -->
    <p>✅ You have completed your attendance today.<br>
    ⏰ Checked in at: <strong><?php echo $absen['jam_masuk']; ?></strong><br>
    ⏹ Checked out at: <strong><?php echo $absen['jam_keluar']; ?></strong></p>
<?php endif; ?>

<!-- JS: Geolocation + Form submission -->
<script>
function submitAttendance(actionType) {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser.");
        return;
    }

    navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        const form = document.createElement("form");
        form.method = "POST";
        form.action = "submit_absen.php";

        const latInput = document.createElement("input");
        latInput.type = "hidden";
        latInput.name = "latitude";
        latInput.value = lat;
        form.appendChild(latInput);

        const lngInput = document.createElement("input");
        lngInput.type = "hidden";
        lngInput.name = "longitude";
        lngInput.value = lng;
        form.appendChild(lngInput);

        const actionInput = document.createElement("input");
        actionInput.type = "hidden";
        actionInput.name = "action";
        actionInput.value = actionType;
        form.appendChild(actionInput);

        document.body.appendChild(form);
        form.submit();
    }, function() {
        alert("Failed to get your location. Please allow location access.");
    });
}
</script>

</body>
</html>
