<?php
session_start();
require_once '../dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$total_labs = $conn->query("SELECT COUNT(*) AS c FROM labs")->fetch_assoc()['c'];

$total_computers = $conn->query("SELECT COUNT(*) AS c FROM computers")->fetch_assoc()['c'];

$working_computers = $conn->query("
    SELECT COUNT(*) AS c
    FROM computers
    WHERE status = 'working'
")->fetch_assoc()['c'];

$faulty_computers = $conn->query("
    SELECT COUNT(*) AS c
    FROM computers
    WHERE status = 'faulty'
")->fetch_assoc()['c'];

$pending_bookings = $conn->query("
    SELECT COUNT(*) AS c
    FROM bookings
    WHERE status = 'pending'
")->fetch_assoc()['c'];

$approved_bookings = $conn->query("
    SELECT COUNT(*) AS c
    FROM bookings
    WHERE status = 'approved'
")->fetch_assoc()['c'];

$open_maintenance = $conn->query("
    SELECT COUNT(*) AS c
    FROM maintenance_records
    WHERE status != 'resolved'
")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - CLMS</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="navbar">
    <div>CLMS &mdash; Reports</div>
    <a href="admin_dashboard.php">Dashboard</a>
</div>

<div class="dashboard-content">

    <h2 class="section-title">Laboratory Management Reports</h2>

    <div class="card-grid">

        <div class="stat-card">
            <h3>Total Laboratories</h3>
            <p><?php echo $total_labs; ?></p>
        </div>

    <div class="stat-card">
            <h3>Total Computers</h3>
            <p><?php echo $total_computers; ?></p>
        </div>
 <div class="stat-card">
            <h3>Working Computers</h3>
            <p><?php echo $working_computers; ?></p>
        </div>

        <div class="stat-card">
            <h3>Faulty Computers</h3>
            <p><?php echo $faulty_computers; ?></p>
        </div>

        <div class="stat-card">
            <h3>Pending Bookings</h3>
            <p><?php echo $pending_bookings; ?></p>
        </div>

       <div class="stat-card">
            <h3>Approved Bookings</h3>
            <p><?php echo $approved_bookings; ?></p>
        </div>

        <div class="stat-card">
            <h3>Open Maintenance Issues</h3>
            <p><?php echo $open_maintenance; ?></p>
        </div>

    </div>
<h2 class="section-title">Recent Booking Requests</h2>

<div class="table-card">

    <table class="data-table">
        <thead>
            <tr>
                <th>Teacher</th>
                <th>Laboratory</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $booking_report = $conn->query("
                SELECT
                    u.full_name,
                    l.lab_name,
                    b.booking_date,
                    b.time_slot,
                    b.status
                FROM bookings b
                JOIN users u ON b.teacher_id = u.id
                JOIN labs l ON b.lab_id = l.id
                ORDER BY b.id DESC
                LIMIT 10
            ");

            while ($booking = $booking_report->fetch_assoc()) {
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['lab_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['time_slot']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo htmlspecialchars($booking['status']); ?>">
                            <?php echo htmlspecialchars($booking['status']); ?>
                        </span>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</div>
</div>

</body>
</html>