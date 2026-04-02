<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$user_id = $_SESSION['user_id'];
$ride_id = $_POST['ride_id'] ?? 0;

// Check owner
$stmt = $conn->prepare("SELECT user_id FROM rides WHERE id=?");
$stmt->bind_param("i", $ride_id);
$stmt->execute();
$ride = $stmt->get_result()->fetch_assoc();

if (!$ride || $ride['user_id'] != $user_id) {
    die("Unauthorized!");
}

// Delete bookings
$conn->query("DELETE FROM bookings WHERE ride_id=$ride_id");

// Delete ride
$conn->query("DELETE FROM rides WHERE id=$ride_id");

header("Location: view_rides.php");
exit();
?>