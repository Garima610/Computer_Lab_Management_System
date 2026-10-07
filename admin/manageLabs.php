<?php
session_start();
require_once '../dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$labs = $conn->query("
    SELECT id, lab_name, location, capacity
    FROM labs
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Laboratories - CLMS</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="navbar">
    <div>
        CLMS &mdash; Manage Laboratories
    </div>

    <a href="admin_dashboard.php">Dashboard</a>
</div>

<div class="dashboard-content">

    <h2 class="section-title">Manage Laboratories</h2>

    <div style="margin-top: 16px;">
        <a href="addLab.php" class="btn-primary" style="display: inline-block; width: auto; text-decoration: none;">
            Add Laboratory
        </a>
    </div>

    <div class="table-card">

        <table class="data-table">

            <thead>
                <tr>
                    <th>Laboratory</th>
                    <th>Location</th>
                    <th>Capacity</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($labs->num_rows > 0): ?>

                <?php while ($lab = $labs->fetch_assoc()): ?>

                <tr>
                 <td>
                   <?php echo htmlspecialchars($lab['lab_name']); ?>
                   </td>

                            <td>
                                <?php echo htmlspecialchars($lab['location']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($lab['capacity']); ?>
                            </td>
                            <td>
    <a href="deleteLab.php?id=<?php echo $lab['id']; ?>"
       onclick="return confirm('Are you sure you want to delete this laboratory?');">
        Delete
    </a>
</td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4" class="empty-row">
                            No laboratories found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>