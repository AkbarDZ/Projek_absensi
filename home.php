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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            padding: 1rem;
        }

        .container {
            max-width: 800px;
            padding: 1rem;
        }

        nav {
            background-color: #2f3542;
            padding: 1rem 2rem;
            border-radius: 8px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .nav-left a {
            color: #ffffff;
            margin-right: 1rem;
            text-decoration: none;
            font-weight: 500;
        }

        .nav-left a:hover {
            text-decoration: underline;
        }

        .nav-right {
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        h2 {
            margin-bottom: 1rem;
            color: #2f3542;
            font-size: 1.75rem;
        }

        button {
            background-color: #1e90ff;
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 1rem;
        }

        button:hover {
            background-color: #4125df;
        }

        p {
            background-color: #ffffff;
            padding: 1.2rem;
            border-left: 5px solid #2ed573;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            max-width: 100%;
            margin-top: 1rem;
            line-height: 1.6;
        }

        .logout-btn {
            background-color: #ff6b6b;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            margin-left: 0.5rem;
        }

        .logout-btn:hover {
            background-color: #e84141;
        }

        @media (max-width: 600px) {
            nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-left,
            .nav-right {
                width: 100%;
                margin-top: 0.5rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            button {
                width: 100%;
                font-size: 1rem;
            }

            .logout-btn {
                margin-top: 0.5rem;
                display: inline-block;
            }
        }
    </style>
</head>

<body>
    <?php include 'component/navbar.php'; ?>
        
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($nama); ?>!</h2>

        <?php if (!$absen): ?>
            <button onclick="submitAttendance('in')">Check In</button>
        <?php elseif ($absen['jam_keluar'] === null): ?>
            <button onclick="submitAttendance('out')">Check Out</button>
        <?php else: ?>
            <p>✅ You have completed your attendance today.<br>
                ⏰ Checked in at : <strong><?php echo $absen['jam_masuk']; ?></strong><br>
                ⏹ Checked out at : <strong><?php echo $absen['jam_keluar']; ?></strong><br>
                Status : <strong><?php echo $absen['status']; ?></strong></p>
        <?php endif; ?>
    </div>

    <script>
        navigator.geolocation.getCurrentPosition(pos => {
            console.log("Your current location is:");
            console.log("Latitude: ", pos.coords.latitude);
            console.log("Longitude:", pos.coords.longitude);
        });

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
