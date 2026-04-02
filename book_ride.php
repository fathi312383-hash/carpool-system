<?php
session_start();
include "db.php";

// Login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// POST validation
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['ride_id'])) {
    header("Location: view_rides.php");
    exit();
}

$ride_id = intval($_POST['ride_id']);

// Check duplicate booking
$stmt = $conn->prepare("SELECT id FROM bookings WHERE user_id=? AND ride_id=?");
$stmt->bind_param("ii", $user_id, $ride_id);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    $message = "⚠️ You already booked this ride!";
    $type = "error";
} else {

    // Get ride details
    $stmt = $conn->prepare("SELECT seats, price FROM rides WHERE id=?");
    $stmt->bind_param("i", $ride_id);
    $stmt->execute();
    $ride = $stmt->get_result()->fetch_assoc();

    if (!$ride) {
        $message = "Ride not found!";
        $type = "error";

    } else {

        // ✅ Dynamic seat check (REAL FIX)
        $count = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE ride_id=$ride_id");
        $row = $count->fetch_assoc();

        if ($row['total'] >= $ride['seats']) {
            $message = "❌ No seats available!";
            $type = "error";

        } else {

            // ✅ Insert booking
            $stmt = $conn->prepare("INSERT INTO bookings (ride_id,user_id,cost) VALUES (?,?,0)");
            $stmt->bind_param("ii", $ride_id, $user_id);
            $stmt->execute();

            // ✅ Count AFTER insert
            $result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE ride_id=$ride_id");
            $row = $result->fetch_assoc();
            $total_users = $row['total'];

            // ✅ Calculate cost
            $cost_per_person = ($ride['price'] > 0) ? ($ride['price'] / $total_users) : 0;

            // ✅ Update ALL users cost
            $conn->query("UPDATE bookings SET cost=$cost_per_person WHERE ride_id=$ride_id");

            $message = "🎉 Ride booked successfully! Cost per person: ₹" . number_format($cost_per_person, 2);
            $type = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Status</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Card */
.box {
    background: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(0,0,0,0.25);
    width: 380px;
    animation: fadeIn 0.6s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px);}
    to { opacity: 1; transform: translateY(0);}
}

.success {
    color: #28a745;
}

.error {
    color: #dc3545;
}

.icon {
    font-size: 50px;
    margin-bottom: 10px;
}

/* Buttons */
button {
    margin-top: 12px;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #4facfe, #00c6ff);
    color: white;
    cursor: pointer;
    font-size: 14px;
    transition: 0.3s;
    width: 100%;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
</style>
</head>

<body>

<div class="box">

    <div class="icon">
        <?php echo ($type == "success") ? "🎉" : "⚠️"; ?>
    </div>

    <h2 class="<?php echo $type; ?>">
        <?php echo $message; ?>
    </h2>

    <form action="view_rides.php">
        <button type="submit">🔍 Back to Rides</button>
    </form>

    <form action="dashboard.php">
        <button type="submit">🏠 Dashboard</button>
    </form>

</div>

</body>
</html>