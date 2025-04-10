<?php
session_start(); // Start session

$servername = "localhost";
$username = "root"; // Default for local server
$password = ""; // Leave empty if no password is set
$dbname = "mymento";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login submission
if (isset($_POST['submit'])) {
    $loginUsername = trim($_POST['login-username']);
    $loginPassword = trim($_POST['login-password']);

    // Prepared statement to prevent SQL injection
    $sql = "SELECT id, password FROM teacherreg WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in SQL statement: " . $conn->error);
    }

    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($teacher_id, $storedPassword);
        $stmt->fetch();

        // Debugging output
        echo "Entered Password: " . htmlspecialchars($loginPassword) . "<br>";
        echo "Stored Password: " . htmlspecialchars($storedPassword) . "<br>";

        // ✅ Plain text password comparison (trimmed)
        if (strcasecmp(trim($loginPassword), trim($storedPassword)) === 0) {
            $_SESSION['teacher_id'] = $teacher_id;
            $_SESSION['login_username'] = $loginUsername;

            // Optional: Remember Me Feature
            if (isset($_POST['remember'])) {
                setcookie("teacher_id", $teacher_id, time() + (86400 * 30), "/");
            }

            echo "<script>alert('Login Successful!'); window.location.href='final.php';</script>";
            exit();
        } else {
            echo "<script>alert('Invalid password! Check database and try again.');</script>";
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
    <title>Teacher Login</title>
    <link rel="stylesheet" href="mohit.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <img src="mohit.png" alt="Logo" class="logo">
                <h2>Login as a Teacher</h2>
            </div>
            <form id="login-form" action="loginteacher.php" method="POST"> <!-- Change the action if needed -->
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
           
            <p class="register-link">Don't have an account? <a href="registerteacher.php">Register Now</a></p>
        </div>
    </div>
</body>
</html>


