<?php
session_start(); // Start session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mymento";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Handle login submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $loginUsername = trim($_POST['login-username']);
    $loginPassword = trim($_POST['login-password']);

    // Prepared statement to prevent SQL injection
    $sql = "SELECT id, username, email, password FROM studentreg WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in SQL statement: " . $conn->error);
    }

    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $dbUsername, $dbEmail, $storedPassword);
        $stmt->fetch();

        // ✅ Plain text password comparison
        if (trim($loginPassword) === trim($storedPassword)) {
            // Store user details in session
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $dbUsername;
            $_SESSION['email'] = $dbEmail;

            // Optional: Remember Me Feature (30 days)
            if (isset($_POST['remember'])) {
                setcookie("username", $dbUsername, time() + (86400 * 30), "/");
            }

            echo "<script>alert('Login Successful!'); window.location.href='stufinal.php';</script>";
            exit();
        } else {
            echo "<script>alert('Invalid password! Try again.');</script>";
        }
    } else {
        echo "<script>alert('Username does not exist!');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Login</title>
    <link rel="stylesheet" href="dev.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <img src="mohit.png" alt="Logo" class="logo">
                <h2>Login as a Student</h2>
            </div>
            <form id="login-form" action="loginstudent.php" method="POST">
                <div class="form-group">
                    <label for="login-username">Username :</label>
                    <input type="text" id="login-username" name="login-username" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password :</label>
                    <input type="password" id="login-password" name="login-password" required>
                </div>
                <button type="submit" name="submit" class="login-btn">Login</button>
            </form>
          
            <p class="register-link">Don't have an account? <a href="registerstudent.php">Register Now</a></p>
        </div>
    </div>
</body>
</html>
