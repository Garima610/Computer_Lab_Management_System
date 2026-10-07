<?php
session_start();
require_once '../dbconnection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {

    $user_id = $_GET['id'];

    $stmt = $conn->prepare("
        DELETE FROM users
        WHERE id = ? AND role != 'admin'
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $stmt->close();
}

header("Location: manageUsers.php");
exit();
?>