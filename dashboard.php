<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Carpool</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: #f4f7fb;
}

.header {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: white;
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h1 {
    font-size: 22px;
}

.header a {
    color: white;
    text-decoration: none;
    margin-left: 20px;
    font-weight: 500;
}

.container {
    padding: 40px;
    text-align: center;
}

h2 {
    margin-bottom: 30px;
    color: #333;
}

.cards {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.card {
    background: white;
    padding: 25px;
    width: 250px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.card h3 {
    margin-bottom: 10px;
}

.card p {
    font-size: 14px;
    color: #666;
    margin-bottom: 15px;
}

.card a {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 8px;
    background: #4facfe;
    color: white;
    text-decoration: none;
    font-size: 14px;
}

.logout {
    margin-top: 30px;
}

.logout a {
    color: red;
    text-decoration: none;
    font-weight: 500;
}
</style>
</head>

<body>

<div class="header">
    <h1>🚗 Carpool Dashboard</h1>
    <div>
        <a href="index.html">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Welcome! What would you like to do?</h2>

    <div class="cards">

        <div class="card">
            <h3>🚘 Offer a Ride</h3>
            <p>Share your trip and earn money by offering seats.</p>
            <a href="create_ride.html">Create Ride</a>
        </div>

        <div class="card">
            <h3>🔍 Find a Ride</h3>
            <p>Search and book rides from other users easily.</p>
            <a href="view_rides.php">Search Rides</a>
        </div>

    </div>

    <div class="logout">
        <p><a href="logout.php">Logout</a></p>
    </div>
</div>

</body>
</html>