<?php
session_start();
require_once 'dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lab_assistant') {
    header("Location: login.php");
    exit();
}

// Handle new maintenance record
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_record'])) {
    $computer_id = $_POST['computer_id'];
    $issue = trim($_POST['issue_description']);
    $logged_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO maintenance_records (computer_id, logged_by, issue_description, status) VALUES (?, ?, ?, 'open')");
    $stmt->bind_param("iis", $computer_id, $logged_by, $issue);
    $stmt->execute();
    $stmt->close();

    header("Location: assistant_maintenance.php");
    exit();
}

// Handle status update on existing record
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_status'])) {
    $record_id = $_POST['record_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE maintenance_records SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $record_id);
    $stmt->execute();
    $stmt->close();

    header("Location: assistant_maintenance.php");
    exit();
}

$computers = $conn->query("SELECT c.id, c.pc_number, l.lab_name FROM computers c JOIN labs l ON c.lab_id = l.id ORDER BY l.lab_name, c.pc_number");

$records = $conn->query("
    SELECT m.id, m.issue_description, m.status, m.logged_at, c.pc_number, l.lab_name
    FROM maintenance_records m
    JOIN computers c ON m.computer_id = c.id
    JOIN labs l ON c.lab_id = l.id
    ORDER BY m.logged_at DESC
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
<title>Maintenance Records - CLMS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <div><a href="labassistant_dashboard.php">&larr; Dashboard</a></div>
    <a href="logout.php">Logout</a>
</div>

<div class="dashboard-content">
    <h2 class="section-title">Record a New Issue</h2>

    <form method="POST" class="form-card">
        <div class="form-group">
            <label>Computer</label>
            <select name="computer_id" required>
                <option value="">-- Select computer --</option>
                <?php while ($c = $computers->fetch_assoc()): ?>
                    <option value="<?php echo $c['id']; ?>">
                        <?php echo htmlspecialchars($c['lab_name'] . ' - ' . $c['pc_number']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Issue Description</label>
            <input type="text" name="issue_description" placeholder="e.g. Monitor not turning on" required>
        </div>
        <button type="submit" name="add_record" class="btn-small">Add Record</button>
    </form>

    <h2 class="section-title">All Maintenance Records</h2>
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
                <?php if ($records->num_rows > 0): ?>
                    <?php while ($r = $records->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['lab_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['pc_number']); ?></td>
                            <td><?php echo htmlspecialchars($r['issue_description']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($r['logged_at']))); ?></td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="record_id" value="<?php echo $r['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="open" <?php echo $r['status'] === 'open' ? 'selected' : ''; ?>>Open</option>
                                        <option value="in_progress" <?php echo $r['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="resolved" <?php echo $r['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
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