<?php
session_start();
require_once '../dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {

    $computer_id = $_GET['id'];

    $stmt = $conn->prepare("
        DELETE FROM computers
        WHERE id = ?
    ");

    $stmt->bind_param("i", $computer_id);
    $stmt->execute();

    $stmt->close();
}

header("Location: manageEquipment.php");
exit();
?>