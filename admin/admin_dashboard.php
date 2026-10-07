<?php
session_start();
require_once '../dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
$total_labs = $conn->query("SELECT COUNT(*) AS c FROM labs")->fetch_assoc()['c'];

$total_computers = $conn->query("SELECT COUNT(*) AS c FROM computers")->fetch_assoc()['c'];

$pending_bookings = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE status = 'pending'")->fetch_assoc()['c'];

$recent_bookings = $conn->query("
    SELECT 
        b.booking_date,
        b.time_slot,
        b.status,
        l.lab_name,
        u.full_name
    FROM bookings b
    JOIN labs l ON b.lab_id = l.id
    JOIN users u ON b.teacher_id = u.id
    ORDER BY b.id DESC
    LIMIT 5
");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - CLMS</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="navbar">
    <div>
        CLMS &mdash; Welcome, 
        <?php echo htmlspecialchars($_SESSION['full_name']); ?> 
        (Admin)
    </div>

    <a href="../logout.php">Logout</a>
</div>

<div class="dashboard-content">

    <h2 class="section-title">Overview</h2>

    <div class="card-grid">

        <div class="stat-card accent-blue">
            <h3><?php echo $total_labs; ?></h3>
            <p>Total Labs</p>
        </div>

        <div class="stat-card accent-green">
            <h3><?php echo $total_computers; ?></h3>
            <p>Total Computers</p>
        </div>

        <div class="stat-card accent-amber">
            <h3><?php echo $pending_bookings; ?></h3>
            <p>Pending Bookings</p>
        </div>

    </div>
    <h2 class="section-title">Quick Actions</h2>

<div class="card-grid">

    <a class="action-card" href="manageUsers.php">
    <span class="action-icon">👥</span>
    <strong>Manage Users</strong>
    <small>Manage laboratory assistants and teachers</small>
</a>

        <a class="action-card" href="manageLabs.php">
            <span class="action-icon">🏫</span>
            <strong>Manage Laboratories</strong>
            <small>Add and manage laboratory information</small>
        </a>

    <a class="action-card" href="manageEquipment.php">
        <span class="action-icon">🖥️</span>
        <strong>Manage Equipment</strong>
        <small>View and manage computer equipment</small>
    </a>
 <a class="action-card" href="manageBookings.php">
        <span class="action-icon">📅</span>
        <strong>Manage Bookings</strong>
        <small>View laboratory booking requests</small>
    </a>

   <a class="action-card" href="reports.php">
    <span class="action-icon">📊</span>
    <strong>View Reports</strong>
    <small>View laboratory management reports</small>
</a>
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
            <?php if ($recent_bookings->num_rows > 0): ?>

                <?php while ($row = $recent_bookings->fetch_assoc()): ?>

                    <tr>
                        <td>
                            <?php echo htmlspecialchars($row['full_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['lab_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['booking_date']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['time_slot']); ?>
                        </td>

                        <td>
                            <?php
                            $status = $row['status'];

                            if ($status === 'pending') {
                                echo '<span class="badge badge-pending">Pending</span>';
                            } elseif ($status === 'approved') {
                                echo '<span class="badge badge-approved">Approved</span>';
                            } elseif ($status === 'rejected') {
                                echo '<span class="badge badge-rejected">Rejected</span>';
                            } else {
                                echo htmlspecialchars(ucwords(str_replace('_', ' ', $status)));
                            }
                            ?>
                        </td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5" class="empty-row">
                        No booking requests yet.
                    </td>
                </tr>

            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>

</body>
</html>