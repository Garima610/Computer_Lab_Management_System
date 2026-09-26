<?php
// Database connection settings for XAMPP (default MySQL: user root, no password)
$host = "localhost";
$dbname = "clms";
$username = "root";
$password = "";

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>
