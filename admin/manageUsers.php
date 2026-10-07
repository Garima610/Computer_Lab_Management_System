<?php
session_start();
require_once '../dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
$users = $conn->query("
    SELECT id, full_name, email, role
    FROM users
    WHERE role != 'admin'
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - CLMS</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="navbar">
    <div>
        CLMS &mdash; Manage Users
    </div>

    <a href="admin_dashboard.php">Dashboard</a>
</div>

<div class="dashboard-content">

    <h2 class="section-title">Manage Users</h2>
    <div style="margin-top: 16px;">
    <a href="addUser.php" class="btn-primary" style="display: inline-block; width: auto; text-decoration: none;">
        Add User
    </a>
</div>
    <div class="table-card">

    <table class="data-table">

        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            <?php if ($users->num_rows > 0): ?>

                <?php while ($user = $users->fetch_assoc()): ?>

                    <tr>
                        <td>
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($user['email']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $user['role']))); ?>
                        </td>
                        <td>
    <a href="deleteUser.php?id=<?php echo $user['id']; ?>"
       onclick="return confirm('Are you sure you want to delete this user?');">
        Delete
    </a>
</td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="4" class="empty-row">
                        No users found.
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>

</div>

</body>
</html>