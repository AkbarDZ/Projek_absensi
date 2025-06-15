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
    <style>
        nav {
            background-color: #eee;
            padding: 10px;
        }
        nav a {
            margin-right: 15px;
            text-decoration: none;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>


    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>


</head>

<body>

<nav>
    <a href="home.php">Home</a>
    <a href="statistik.php">Statistik</a>
    <span style="float: right;">
    Logged in as: <strong><?php echo htmlspecialchars($nama); ?></strong> (<?php echo $role; ?>) |
    <a href="logout.php">Logout</a>
</nav>

<h2>Attendance Statistics</h2>


<!-- Date filter inputs -->
<table border="0" cellspacing="5" cellpadding="5">
    <tr>
        <td>Minimum date:</td>
        <td><input type="text" id="min" name="min"></td>
    </tr>
    <tr>
        <td>Maximum date:</td>
        <td><input type="text" id="max" name="max"></td>
    </tr>
</table>

<table id="attendanceTable">
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


<script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable({
            "order": [[1, "desc"]] // order by date column descending
        });
    });
</script>


<script>
    $(document).ready(function() {
    // Destroy existing instance if any (prevents reinitialization error)
    if ($.fn.DataTable.isDataTable('#attendanceTable')) {
        $('#attendanceTable').DataTable().destroy();
    }

    // Initialize DataTable
    const table = $('#attendanceTable').DataTable({
        order: [[<?php echo ($role === 'admin') ? 1 : 0; ?>, 'desc']]
    });

    // Initialize datepickers
    $("#min, #max").datepicker({
        dateFormat: "yy-mm-dd"
    });

    // Redraw table on filter change
    $('#min, #max').change(function() {
        table.draw();
    });

    // Custom date filtering logic
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        const min = $('#min').datepicker("getDate");
        const max = $('#max').datepicker("getDate");
        const date = new Date(data[<?php echo ($role === 'admin') ? 1 : 0; ?>]);

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
});

</script>



</body>
</html>
