<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'backend/db.php';
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

        h2 {
            margin-bottom: 20px;
            color: #2f3542;
        }

        /* Tombol */
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
            margin-top: 20px;
        }

        button:hover {
            background-color: rgb(65, 37, 223);
        }

        /* Pesan status absensi */
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

        /* RESPONSIVE */
        @media (max-width: 600px) {
            nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-right {
                margin-top: 10px;
                text-align: left;
            }
        }
    </style>
</head>

<body>

<?php include 'component/navbar.php'; ?>

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

<script>
    navigator.geolocation.getCurrentPosition(pos => {
    console.log("Your current location is:");
    console.log("Latitude: ", pos.coords.latitude);
    console.log("Longitude:", pos.coords.longitude);
});
</script>

<!-- JS: Geolocation + Form submission -->
<script>
function submitAttendance(actionType) {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser.");
        return;
    }

            navigator.geolocation.getCurrentPosition(function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

        const form = document.createElement("form");
        form.method = "POST";
        form.action = "backend/submit_absen.php";

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
            }, function () {
                alert("Failed to get your location. Please allow location access.");
            });
        }
    </script>

</body>

</html>