<?php
session_start();
include "db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];

    // Redirect to dashboard
    header("Location: dashboard.php");
    exit();
} else {
    $error = "❌ Invalid email or password!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login Error</title>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #ff6a6a, #ff3d3d);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.box {
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    width: 350px;
}

h2 {
    color: red;
}

button {
    margin-top: 20px;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: #ff3d3d;
    color: white;
    cursor: pointer;
}
</style>
</head>

<body>

<div class="box">
    <h2><?php echo $error; ?></h2>

    <form action="login.html">
        <button type="submit">Try Again</button>
    </form>
</div>

</body>
</html>