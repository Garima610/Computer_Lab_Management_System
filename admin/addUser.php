<?php
session_start();
require_once '../dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
$message = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("
        INSERT INTO users (full_name, email, password, role)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("ssss", $full_name, $email, $password, $role);

    if ($stmt->execute()) {
        $message = "User added successfully.";
    } else {
        $error = "Failed to add user.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User - CLMS</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="navbar">
    <div>
        CLMS &mdash; Add User
    </div>

    <a href="manageUsers.php">Manage Users</a>
</div>

<div class="dashboard-content">

  <h2 class="section-title">Add User</h2>
  <?php if ($message): ?>
    <div class="error-msg" style="background: #d1fae5; color: #047857; border-left-color: #047857;">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="error-msg">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="table-card" style="padding: 24px; max-width: 600px;">

    <form method="POST" action="addUser.php">

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <label>Role</label>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="lab_assistant">Lab Assistant</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>

        <button type="submit" class="btn-primary">
            Add User
        </button>

    </form>

</div>

</div>

</body>
</html>