<?php
session_start();
require_once '../dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$bookings = $conn->query("
    SELECT
        b.id,
        u.full_name AS teacher_name,
        l.lab_name,
        b.booking_date,
        b.time_slot,
        b.status
    FROM bookings b
    JOIN users u ON b.teacher_id = u.id
    JOIN labs l ON b.lab_id = l.id
    ORDER BY b.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings - CLMS</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="navbar">
    <div>CLMS &mdash; Manage Bookings</div>
    <a href="admin_dashboard.php">Dashboard</a>
</div>

<div class="dashboard-content">

    <h2 class="section-title">Manage Bookings</h2>

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
                <?php while ($booking = $bookings->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['teacher_name']); ?></td>

                        <td><?php echo htmlspecialchars($booking['lab_name']); ?></td>

                        <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>

                        <td><?php echo htmlspecialchars($booking['time_slot']); ?></td>

                        <td>
                            <span class="badge badge-<?php echo htmlspecialchars($booking['status']); ?>">
                                <?php echo htmlspecialchars($booking['status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>

        </table>

    </div>

</div>

</body>
</html>