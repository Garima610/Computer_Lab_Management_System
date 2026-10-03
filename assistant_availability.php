<?php
session_start();
require_once 'dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lab_assistant') {
    header("Location: login.php");
    exit();
}

$selected_date = (isset($_GET['date']) && $_GET['date'] !== '') ? $_GET['date'] : date('Y-m-d');

$labs = $conn->query("
    SELECT l.id, l.lab_name, l.location, l.capacity,
           COUNT(c.id) AS total_pc,
           COALESCE(SUM(c.status = 'working'), 0) AS working_pc,
           COALESCE(SUM(c.status = 'faulty'), 0) AS faulty_pc,
           COALESCE(SUM(c.status = 'maintenance'), 0) AS maint_pc
    FROM labs l
    LEFT JOIN computers c ON c.lab_id = l.id
    GROUP BY l.id
    ORDER BY l.lab_name
");

$stmt = $conn->prepare("SELECT lab_id, time_slot, status FROM bookings WHERE booking_date = ? AND status IN ('pending', 'approved')");
$stmt->bind_param("s", $selected_date);
$stmt->execute();
$result = $stmt->get_result();

$booked = [];
while ($b = $result->fetch_assoc()) {
    $booked[$b['lab_id']][] = $b;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lab Availability - CLMS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="navbar">
    <div><a href="labassistant_dashboard.php">&larr; Dashboard</a></div>
    <a href="logout.php">Logout</a>
</div>

<div class="dashboard-content">
    <h2 class="section-title">Lab Availability</h2>

    <form method="GET" class="filter-bar">
        <label for="date">Select date:</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">
        <button type="submit" class="btn-small">Check</button>
    </form>

    <div class="lab-grid">
        <?php if ($labs->num_rows > 0): ?>
            <?php while ($lab = $labs->fetch_assoc()): ?>
                <?php $is_booked = isset($booked[$lab['id']]); ?>
                <div class="lab-card">
                    <div class="lab-card-header">
                        <div>
                            <h3><?php echo htmlspecialchars($lab['lab_name']); ?></h3>
                            <small><?php echo htmlspecialchars($lab['location']); ?> &middot; <?php echo (int)$lab['capacity']; ?> seats</small>
                        </div>
                        <span class="badge <?php echo $is_booked ? 'badge-pending' : 'badge-approved'; ?>">
                            <?php echo $is_booked ? 'Booked' : 'Free'; ?>
                        </span>
                    </div>

                    <div class="pc-stats">
                        <div><strong><?php echo $lab['total_pc']; ?></strong><span>Total PCs</span></div>
                        <div><strong class="text-green"><?php echo $lab['working_pc']; ?></strong><span>Working</span></div>
                        <div><strong class="text-red"><?php echo $lab['faulty_pc']; ?></strong><span>Faulty</span></div>
                        <div><strong class="text-amber"><?php echo $lab['maint_pc']; ?></strong><span>Maintenance</span></div>
                    </div>

                    <?php if ($is_booked): ?>
                        <ul class="slot-list">
                            <?php foreach ($booked[$lab['id']] as $slot): ?>
                                <li>
                                    <?php echo htmlspecialchars($slot['time_slot']); ?>
                                    <span class="badge <?php echo $slot['status'] === 'approved' ? 'badge-approved' : 'badge-pending'; ?>">
                                        <?php echo ucfirst($slot['status']); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-row">No labs added yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>