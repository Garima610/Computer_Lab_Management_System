<?php
session_start();
require_once 'dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

$total_labs = $conn->query("SELECT COUNT(*) as c FROM labs")->fetch_assoc()['c'];

$stmt = $conn->prepare("SELECT COUNT(*) as c FROM bookings WHERE teacher_id = ? AND status = 'pending'");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$pending_count = $stmt->get_result()->fetch_assoc()['c'];

$stmt2 = $conn->prepare("SELECT COUNT(*) as c FROM bookings WHERE teacher_id = ? AND status = 'approved'");
$stmt2->bind_param("i", $teacher_id);
$stmt2->execute();
$approved_count = $stmt2->get_result()->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher Dashboard - CLMS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <div>CLMS &mdash; Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (Teacher)</div>
    <a href="logout.php">Logout</a>
</div>

<div class="dashboard-content">
    <h2>Overview</h2>
    <div class="card-grid">
        <div class="stat-card">
            <h3><?php echo $total_labs; ?></h3>
            <p>Total Labs Available</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $pending_count; ?></h3>
            <p>My Pending Requests</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $approved_count; ?></h3>
            <p>My Approved Bookings</p>
        </div>
    </div>

    <h2 style="margin-top:32px;">Quick Actions</h2>
    <div class="card-grid">
        <div class="stat-card"><a href="teacher_availability.php">Check Lab Availability</a></div>
        <div class="stat-card"><a href="teacher_book.php">Request a Booking</a></div>
        <div class="stat-card"><a href="teacher_my_bookings.php">View My Bookings</a></div>
    </div>
</div>
</body>
</html>