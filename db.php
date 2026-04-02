<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "carpool";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $password, $db);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log("Database Connection Failed: " . $e->getMessage());
    die("⚠️ Unable to connect to database. Please try again later.");
}
?>