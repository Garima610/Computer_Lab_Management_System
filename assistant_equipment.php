<?php
session_start();
require_once 'dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lab_assistant') {
    header("Location: login.php");
    exit();
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $computer_id = $_POST['computer_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE computers SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $computer_id);
    $stmt->execute();
    $stmt->close();

    header("Location: assistant_equipment.php");
    exit();
}

$selected_lab = isset($_GET['lab_id']) ? $_GET['lab_id'] : '';

$labs = $conn->query("SELECT id, lab_name FROM labs ORDER BY lab_name");

if ($selected_lab !== '') {
    $stmt = $conn->prepare("SELECT c.id, c.pc_number, c.specs, c.status, l.lab_name FROM computers c JOIN labs l ON c.lab_id = l.id WHERE c.lab_id = ? ORDER BY c.pc_number");
    $stmt->bind_param("i", $selected_lab);
    $stmt->execute();
    $computers = $stmt->get_result();
} else {
    $computers = $conn->query("SELECT c.id, c.pc_number, c.specs, c.status, l.lab_name FROM computers c JOIN labs l ON c.lab_id = l.id ORDER BY l.lab_name, c.pc_number");
}

$badge = [
    'working'     => 'badge-approved',
    'faulty'      => 'badge-rejected',
    'maintenance' => 'badge-pending'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Equipment Status - CLMS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <div><a href="labassistant_dashboard.php">&larr; Dashboard</a></div>
    <a href="logout.php">Logout</a>
</div>

<div class="dashboard-content">
    <h2 class="section-title">Update Equipment Status</h2>

    <form method="GET" class="filter-bar">
        <label for="lab_id">Filter by lab:</label>
        <select name="lab_id" id="lab_id" onchange="this.form.submit()">
            <option value="">All Labs</option>
            <?php while ($lab = $labs->fetch_assoc()): ?>
                <option value="<?php echo $lab['id']; ?>" <?php echo ($selected_lab == $lab['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($lab['lab_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <div class="table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Lab</th>
                    <th>PC Number</th>
                    <th>Specs</th>
                    <th>Current Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($computers->num_rows > 0): ?>
                    <?php while ($c = $computers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['lab_name']); ?></td>
                            <td><?php echo htmlspecialchars($c['pc_number']); ?></td>
                            <td><?php echo htmlspecialchars($c['specs'] ?? '-'); ?></td>
                            <td>
                                <span class="badge <?php echo $badge[$c['status']]; ?>">
                                    <?php echo ucfirst($c['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="computer_id" value="<?php echo $c['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="working" <?php echo $c['status'] === 'working' ? 'selected' : ''; ?>>Working</option>
                                        <option value="faulty" <?php echo $c['status'] === 'faulty' ? 'selected' : ''; ?>>Faulty</option>
                                        <option value="maintenance" <?php echo $c['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="empty-row">No computers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>