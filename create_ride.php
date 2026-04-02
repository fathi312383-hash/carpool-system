<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: create_ride.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Clean inputs (fixed duplicate issue)
$source = htmlspecialchars($_POST['source'] ?? '');
$destination = htmlspecialchars($_POST['destination'] ?? '');
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$seats = isset($_POST['seats']) ? intval($_POST['seats']) : 0;
$price = isset($_POST['price']) ? intval($_POST['price']) : 0;
$vehicle = htmlspecialchars($_POST['vehicle'] ?? '');

if (empty($source) || empty($destination) || empty($date) || empty($time)) {
    $message = "All required fields must be filled!";
    $type = "error";
} else {

    $stmt = $conn->prepare("INSERT INTO rides (user_id, source, destination, ride_date, ride_time, seats, price, vehicle) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("isssssis", $user_id, $source, $destination, $date, $time, $seats, $price, $vehicle);

    if ($stmt->execute()) {
        $message = "Ride Published Successfully!";
        $type = "success";
    } else {
        $message = "Something went wrong!";
        $type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ride Status</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.card {
    background: white;
    padding: 35px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(0,0,0,0.25);
    width: 360px;
    animation: fadeIn 0.6s ease;
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}

.icon {
    font-size: 50px;
    margin-bottom: 10px;
}

.success-icon {
    color: #28a745;
}

.error-icon {
    color: #dc3545;
}

h2 {
    margin-bottom: 10px;
}

p {
    color: #666;
    font-size: 14px;
    margin-bottom: 20px;
}

.buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

button {
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
}

.primary {
    background: linear-gradient(135deg, #667eea, #5a67d8);
    color: white;
}

.secondary {
    background: #eee;
}

button:hover {
    transform: translateY(-2px);
}
</style>

<?php if ($type == "success"): ?>
<script>
setTimeout(() => {
    window.location.href = "dashboard.php";
}, 3000);
</script>
<?php endif; ?>

</head>

<body>

<div class="card">

    <div class="icon <?php echo $type == 'success' ? 'success-icon' : 'error-icon'; ?>">
        <?php echo $type == "success" ? "✅" : "⚠️"; ?>
    </div>

    <h2><?php echo $message; ?></h2>

    <?php if ($type == "success"): ?>
        <p>Your ride has been published successfully. Redirecting...</p>
    <?php else: ?>
        <p>Please check your input and try again.</p>
    <?php endif; ?>

    <div class="buttons">
        <form action="dashboard.php">
            <button class="primary">Go to Dashboard</button>
        </form>

        <form action="create_ride.html">
            <button class="secondary">Create Another Ride</button>
        </form>
    </div>

</div>

</body>
</html>