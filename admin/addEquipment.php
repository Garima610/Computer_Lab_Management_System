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

    $lab_id = $_POST['lab_id'];
    $pc_number = trim($_POST['pc_number']);
    $specs = trim($_POST['specs']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("
        INSERT INTO computers (lab_id, pc_number, specs, status)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("isss", $lab_id, $pc_number, $specs, $status);

    if ($stmt->execute()) {
        $message = "Equipment added successfully.";
    } else {
        $error = "Failed to add equipment.";
    }

    $stmt->close();
}

$labs = $conn->query("
    SELECT id, lab_name
    FROM labs
    ORDER BY lab_name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Equipment - CLMS</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="navbar">
    <div>
        CLMS &mdash; Add Equipment
    </div>

    <a href="manageEquipment.php">Manage Equipment</a>
</div>

<div class="dashboard-content">

    <h2 class="section-title">Add Equipment</h2>
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

        <form method="POST" action="addEquipment.php">

            <div class="form-group">
                <label>Laboratory</label>

                <select name="lab_id" required>
                    <option value="">Select Laboratory</option>

                    <?php while ($lab = $labs->fetch_assoc()): ?>

                        <option value="<?php echo $lab['id']; ?>">
                            <?php echo htmlspecialchars($lab['lab_name']); ?>
                        </option>

                    <?php endwhile; ?>

                </select>
            </div>

            <div class="form-group">
                <label>PC Number</label>
                <input type="text" name="pc_number" required>
            </div>

            <div class="form-group">
                <label>Specifications</label>
                <input type="text" name="specs">
            </div>

            <div class="form-group">
                <label>Status</label>

                <select name="status" required>
                    <option value="working">Working</option>
                    <option value="faulty">Faulty</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>

            <button type="submit" class="btn-primary">
                Add Equipment
            </button>

        </form>

    </div>

</div>

</body>
</html>