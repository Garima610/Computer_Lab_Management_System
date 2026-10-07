<?php
session_start();
require_once '../dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$computers = $conn->query("
    SELECT 
        c.id,
        c.pc_number,
        c.specs,
        c.status,
        l.lab_name
    FROM computers c
    JOIN labs l ON c.lab_id = l.id
    ORDER BY c.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Equipment - CLMS</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="navbar">
    <div>
        CLMS &mdash; Manage Equipment
    </div>

    <a href="admin_dashboard.php">Dashboard</a>
</div>

<div class="dashboard-content">

    <h2 class="section-title">Manage Equipment</h2>

    <div style="margin-top: 16px;">
        <a href="addEquipment.php" class="btn-primary"
           style="display: inline-block; width: auto; text-decoration: none;">
             Add Equipment
        </a>
    </div>

    <div class="table-card">

        <table class="data-table">

            <thead>
                <tr>
                    <th>Laboratory</th>
                    <th>PC Number</th>
                    <th>Specifications</th>
                    <th>Status</th>
                    <th>Action</th>

                </tr>
            </thead>

            <tbody>

                <?php if ($computers->num_rows > 0): ?>

                    <?php while ($computer = $computers->fetch_assoc()): ?>

                        <tr>
                            <td>
                                <?php echo htmlspecialchars($computer['lab_name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($computer['pc_number']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($computer['specs']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(ucwords($computer['status'])); ?>
                            </td>
                            <td>
    <a href="deleteEquipment.php?id=<?php echo $computer['id']; ?>"
       onclick="return confirm('Are you sure you want to delete this equipment?');">
        Delete
    </a>
</td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="empty-row">
                            No equipment found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>