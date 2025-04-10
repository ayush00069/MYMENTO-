<?php
// Database connection
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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginUsername = $_POST['login-username'];
    $loginPassword = $_POST['login-password'];

    // Use studentreg table instead of rname
    $stmt = $conn->prepare("SELECT password FROM studentreg WHERE username = ?");
    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($storedPassword);
        $stmt->fetch();

        // Compare hashed password
      // Compare the entered password directly with the stored password
if ($loginPassword === $storedPassword) {
    echo "<script>alert('Login Successful!');</script>";
    header("Location: parentdata.php"); 
    exit();
} else {
    echo "<script>alert('Invalid password!'); window.history.back();</script>";
}

    } else {
        echo "<script>alert('Username does not exist! Redirecting to registration.');</script>";
        echo "<script>window.location.href='registerstudent.php';</script>";
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
    <title>Parent Login</title>
    <link rel="stylesheet" href="dev.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <img src="mohit.png" alt="Logo" class="logo">
                <h2>Login as a Parent</h2>
            </div>
            <form id="login-form" action="loginparent.php" method="POST">
                <div class="form-group">
                    <label for="login-username">Student Username</label>
                    <input type="text" id="login-username" name="login-username" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Student Password :</label>
                    <input type="password" id="login-password" name="login-password" required>
                </div>
                <button type="submit" name="submit" class="login-btn">Login</button>
            </form>
            <p class="register-link">Don't have an account? <a href="registerstudent.php">Register Now</a></p>
        </div>
    </div>
</body>
</html>
