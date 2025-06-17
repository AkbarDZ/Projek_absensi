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

// Fetch attendance data
if ($role === 'admin') {
    $stmt = $pdo->query("SELECT a.*, u.nama FROM absensi a JOIN user u ON a.user_id = u.id ORDER BY tanggal DESC");
    $absensi = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT * FROM absensi WHERE user_id = ? ORDER BY tanggal DESC");
    $stmt->execute([$user_id]);
    $absensi = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Statistik - Attendance App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- DataTables & jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

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

        h2 {
            margin-bottom: 20px;
            color: #2f3542;
        }

        table.dataTable {
            border-radius: 8px;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        #dateFilter {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        #dateFilter label {
            font-weight: 500;
        }

        #dateFilter input {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        th, td {
            text-align: center;
            font-size: 0.9rem;
            padding: 10px;
        }

        /* DataTables Pagination Styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 4px 8px !important;
            margin: 2px;
            font-size: 0.85rem;
            background-color: #f1f1f1 !important;
            color: #333 !important;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #d0e6ff !important;
            border-color: #339af0;
            color: #000 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #339af0 !important;
            color: white !important;
            border-color: #339af0;
            font-weight: bold;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_filter {
            font-size: 0.9rem;
            color: #444;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-right {
                margin-top: 10px;
                text-align: left;
            }

            #dateFilter {
                flex-direction: column;
                align-items: flex-start;
            }

            #attendanceTable {
                width: 100% !important;
                overflow-x: auto;
                display: block;
            }
        }
    </style>
</head>

<body>

<?php include 'component/navbar.php'; ?>

<h2>Attendance Statistics</h2>

<div id="dateFilter">
    <label for="min">Minimum date:</label>
    <input type="text" id="min" name="min">

    <label for="max">Maximum date:</label>
    <input type="text" id="max" name="max">
</div>

<table id="attendanceTable" class="display">
    <thead>
        <tr>
            <?php if ($role === 'admin'): ?>
                <th>Name</th>
            <?php endif; ?>
            <th>Date</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($absensi as $row): ?>
            <tr>
                <?php if ($role === 'admin'): ?>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                <?php endif; ?>
                <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                <td><?php echo $row['jam_masuk'] ?? '-'; ?></td>
                <td><?php echo $row['jam_keluar'] ?? '-'; ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- DataTables + Date Filter -->
<script>
$(document).ready(function() {
    const dateColumn = <?php echo ($role === 'admin') ? 1 : 0; ?>;

    const table = $('#attendanceTable').DataTable({
        order: [[dateColumn, 'desc']],
        pageLength: 10
    });

    $("#min, #max").datepicker({ dateFormat: "yy-mm-dd" });

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        const min = $('#min').datepicker("getDate");
        const max = $('#max').datepicker("getDate");
        const date = new Date(data[dateColumn]);

        if (
            (!min && !max) ||
            (!min && date <= max) ||
            (min <= date && !max) ||
            (min <= date && date <= max)
        ) {
            return true;
        }
        return false;
    });

    $('#min, #max').change(function() {
        table.draw();
    });
});
</script>

</body>
</html>
