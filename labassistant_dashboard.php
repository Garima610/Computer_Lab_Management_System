<?php
session_start();
require_once 'dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lab_assistant') {
    header("Location: login.php");
    exit();
}

$total_labs      = $conn->query("SELECT COUNT(*) AS c FROM labs")->fetch_assoc()['c'];
$total_computers = $conn->query("SELECT COUNT(*) AS c FROM computers")->fetch_assoc()['c'];
$faulty_count    = $conn->query("SELECT COUNT(*) AS c FROM computers WHERE status = 'faulty'")->fetch_assoc()['c'];
$open_issues     = $conn->query("SELECT COUNT(*) AS c FROM maintenance_records WHERE status != 'resolved'")->fetch_assoc()['c'];

$recent = $conn->query("
    SELECT m.issue_description, m.status, m.logged_at, c.pc_number, l.lab_name
    FROM maintenance_records m
    JOIN computers c ON m.computer_id = c.id
    JOIN labs l ON c.lab_id = l.id
    ORDER BY m.logged_at DESC
    LIMIT 5
");

$badge = [
    'open'        => 'badge-rejected',
    'in_progress' => 'badge-pending',
    'resolved'    => 'badge-approved'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lab Assistant Dashboard - CLMS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <div>CLMS &mdash; Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (Lab Assistant)</div>
    <a href="logout.php">Logout</a>
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
            <h3><?php echo $faulty_count; ?></h3>
            <p>Faulty Computers</p>
        </div>
        <div class="stat-card accent-amber">
            <h3><?php echo $open_issues; ?></h3>
            <p>Unresolved Maintenance Issues</p>
        </div>
    </div>

    <h2 class="section-title">Quick Actions</h2>
    <div class="card-grid">
        <a class="action-card" href="assistant_availability.php">
            <span class="action-icon">📅</span>
            <strong>View Lab Availability</strong>
            <small>See which labs and computers are free</small>
        </a>
        <a class="action-card" href="assistant_equipment.php">
            <span class="action-icon">🖥️</span>
            <strong>Update Equipment Status</strong>
            <small>Mark computers working, faulty or in maintenance</small>
        </a>
        <a class="action-card" href="assistant_maintenance.php">
            <span class="action-icon">🛠️</span>
            <strong>Record Maintenance</strong>
            <small>Log issues and repair progress</small>
        </a>
    </div>

    <h2 class="section-title">Recent Maintenance Records</h2>
    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Lab</th>
                    <th>Computer</th>
                    <th>Issue</th>
                    <th>Logged On</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent->num_rows > 0): ?>
                    <?php while ($row = $recent->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['pc_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['issue_description']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['logged_at']))); ?></td>
                            <td>
                                <span class="badge <?php echo $badge[$row['status']]; ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $row['status'])); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="empty-row">No maintenance records yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>