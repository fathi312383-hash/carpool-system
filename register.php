<?php
session_start();
include "db.php";

$name = htmlspecialchars($_POST['name']);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$vehicle = htmlspecialchars($_POST['vehicle']);

if (empty($name) || empty($email) || empty($_POST['password'])) {
    $message = "All required fields must be filled!";
    $type = "error";
} else {

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        $message = "Email already registered!";
        $type = "error";
    } else {

        $stmt = $conn->prepare("INSERT INTO users (name,email,password,vehicle) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $email, $password, $vehicle);

        if ($stmt->execute()) {
            $message = "🎉 Registration successful!";
            $type = "success";
        } else {
            $message = "Something went wrong!";
            $type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registration Status</title>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #43e97b, #38f9d7);
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

.success {
    color: green;
}

.error {
    color: red;
}

button {
    margin-top: 15px;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: #43e97b;
    color: white;
    cursor: pointer;
}
</style>
</head>

<body>

<div class="box">
    <h2 class="<?php echo $type; ?>">
        <?php echo $message; ?>
    </h2>

    <?php if ($type == "success"): ?>
        <form action="login.html">
            <button type="submit">Go to Login</button>
        </form>
    <?php else: ?>
        <form action="register.html">
            <button type="submit">Try Again</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>