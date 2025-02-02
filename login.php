<?php
session_start();
$servername = "localhost";
$username = "root";  // Change this if necessary
$password = "";  // Change this if necessary
$dbname = "teahaven"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, full_name, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $full_name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $user_id;
            $_SESSION["email"] = $email;
            $_SESSION["full_name"] = $full_name;

            // Redirect to dashboard or homepage after successful login
            header("Location: dashboard.php"); 
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "User not found!";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tea Haven</title>
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
        .login-container {
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
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .btn {
            background-color: #d81b60;
            color: white;
            border: none;
            padding: 10px;
            width: 150px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #ad1457;
        }
        .btn-secondary {
            background-color: white;
            color: #d81b60;
            border: 2px solid #d81b60;
        }
        a {
            text-decoration: none;
            color: #d81b60;
            font-size: 12px;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <form action="login.php" method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="btn-group">
                <a href="register.php"><button type="button" class="btn">CREATE ACCOUNT</button></a>
                <button type="submit" class="btn btn-secondary" name="login">LOG IN</button>
            </div>
        </form>
        <p><a href="reset_password.php">Forgot your password?</a></p>
    </div>
</body>
</html>
