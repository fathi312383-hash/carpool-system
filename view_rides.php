<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$result = $conn->query("SELECT rides.*, users.name FROM rides 
JOIN users ON rides.user_id = users.id ORDER BY ride_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Available Rides</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
* { box-sizing: border-box; margin:0; padding:0; font-family:'Poppins', sans-serif; }

body { background:#f4f7fb; }

.header {
    background: linear-gradient(135deg,#4facfe,#00f2fe);
    color:white;
    padding:20px 40px;
    display:flex;
    justify-content:space-between;
}

.header a { color:white; text-decoration:none; margin-left:20px; }

.container {
    padding:40px;
}

h2 {
    text-align:center;
    margin-bottom:30px;
}

.cards {
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    justify-content:center;
}

.card {
    background:white;
    width:300px;
    padding:20px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    transition:0.3s;
}

.card:hover { transform: translateY(-5px); }

.route {
    font-size:18px;
    font-weight:600;
    margin-bottom:10px;
}

.info {
    font-size:14px;
    color:#555;
    margin-bottom:5px;
}

/* Booking input */
input[type=number] {
    width:100%;
    padding:8px;
    border-radius:6px;
    border:1px solid #ccc;
    margin-top:5px;
}

/* Buttons */
button {
    width:100%;
    margin-top:10px;
    padding:10px;
    border:none;
    border-radius:8px;
    background:#4facfe;
    color:white;
    cursor:pointer;
}

button:hover { background:#00c6ff; }

.delete-btn {
    background:red;
}

.delete-btn:hover {
    background:darkred;
}

.no-rides {
    text-align:center;
    font-size:18px;
    color:#777;
}
</style>
</head>

<body>

<div class="header">
    <div><strong>🚗 Available Rides</strong></div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Find Your Ride</h2>

    <div class="cards">

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>

        <?php
        // 🔥 Calculate booked seats
        $result2 = $conn->query("SELECT SUM(seats_booked) as total FROM bookings WHERE ride_id=".$row['id']);
        $row2 = $result2->fetch_assoc();

        $booked = $row2['total'] ?? 0;
        $remaining = $row['seats'] - $booked;
        ?>

        <div class="card">

            <div class="route">
                <?php echo $row['source']; ?> → <?php echo $row['destination']; ?>
            </div>

            <div class="info">👤 Driver: <?php echo $row['name']; ?></div>
            <div class="info">📅 Date: <?php echo $row['ride_date']; ?></div>

            <?php if (!empty($row['ride_time'])): ?>
            <div class="info">⏰ Time: <?php echo $row['ride_time']; ?></div>
            <?php endif; ?>

            <?php if (!empty($row['price'])): ?>
            <div class="info">💰 Price: ₹<?php echo $row['price']; ?></div>
            <?php endif; ?>

            <div class="info">💺 Seats Available: <?php echo $remaining; ?></div>

            <!-- BOOKING -->
            <?php if ($remaining > 0): ?>
                <form method="POST" action="book_ride.php">
                    <input type="hidden" name="ride_id" value="<?php echo $row['id']; ?>">

                    <input type="number" name="seats" min="1" max="<?php echo $remaining; ?>" 
                           placeholder="Enter seats" required>

                    <button type="submit">Book Ride</button>
                </form>
            <?php else: ?>
                <div class="info" style="color:red;">❌ Ride Full</div>
            <?php endif; ?>

            <!-- OWNER OPTIONS -->
            <?php if ($row['user_id'] == $_SESSION['user_id']): ?>

                <!-- PASSENGERS -->
                <div style="margin-top:10px;">
                    <strong>Passengers:</strong>

                    <?php
                    $users = $conn->query("
                        SELECT users.name, bookings.seats_booked, bookings.cost 
                        FROM bookings 
                        JOIN users ON bookings.user_id = users.id 
                        WHERE bookings.ride_id=".$row['id']
                    );

                    if ($users->num_rows > 0) {
                        while ($u = $users->fetch_assoc()) {
                            echo "<div class='info'>👤 {$u['name']} | Seats: {$u['seats_booked']} | ₹{$u['cost']}</div>";
                        }
                    } else {
                        echo "<div class='info'>No bookings yet</div>";
                    }
                    ?>
                </div>

                <!-- DELETE BUTTON -->
                <form method="POST" action="delete_ride.php">
                    <input type="hidden" name="ride_id" value="<?php echo $row['id']; ?>">
                    <button class="delete-btn">Delete Ride</button>
                </form>

            <?php endif; ?>

        </div>

        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-rides">No rides available right now.</p>
    <?php endif; ?>

    </div>
</div>

</body>
</html>