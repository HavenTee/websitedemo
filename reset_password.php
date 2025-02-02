<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teahaven";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";

// Handle password reset
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $email = trim($_POST["email"]);
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            $success = "Password reset successfully! You can now <a href='login.php'>log in</a>.";
        } else {
            $error = "Error: " . $conn->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fce4ec;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
        }
        h2 {
            color: #d81b60;
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .input-group input {
            width: 90%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 25px;
            font-weight: bold;
            text-align: center;
            background-color: #b39ddb;
            color: white;
        }
        .btn {
            background-color: #d81b60;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #ad1457;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Reset Password</h2>
    <form action="reset_password.php" method="POST">
        <div class="input-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-group">
            <input type="password" name="new_password" placeholder="New Password" required>
        </div>
        <div class="input-group">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="btn" name="reset_password">Reset Password</button>
    </form>
</div>

</body>
</html>

