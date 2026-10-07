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

    $lab_name = trim($_POST['lab_name']);
    $location = trim($_POST['location']);
    $capacity = $_POST['capacity'];

    $stmt = $conn->prepare("
        INSERT INTO labs (lab_name, location, capacity)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("ssi", $lab_name, $location, $capacity);

    if ($stmt->execute()) {
        $message = "Laboratory added successfully.";
    } else {
        $error = "Failed to add laboratory.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Laboratory - CLMS</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="navbar">
    <div>
        CLMS &mdash; Add Laboratory
    </div>

    <a href="manageLabs.php">Manage Laboratories</a>
</div>

<div class="dashboard-content">

    <h2 class="section-title">Add Laboratory</h2>
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

        <form method="POST" action="addLab.php">

            <div class="form-group">
                <label>Laboratory Name</label>
                <input type="text" name="lab_name" required>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" required>
            </div>

            <div class="form-group">
                <label>Capacity</label>
                <input type="number" name="capacity" min="1" required>
            </div>

            <button type="submit" class="btn-primary">
                Add Laboratory
            </button>

        </form>

    </div>

</div>

</body>
</html>