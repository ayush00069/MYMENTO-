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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Insert data into studentreg table using prepared statements
        $stmt = $conn->prepare("INSERT INTO studentreg (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!');</script>";
            echo "<script>window.location.href='loginstudent.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Registration</title>
    <link rel="stylesheet" href="dev.css">
    <script>
  function handleRegistration() {
    const username = document.getElementById("username").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;
    const confirmError = document.getElementById("confirm-error");

    confirmError.textContent = '';

    if (!username || !email || !password || !confirmPassword) {
        alert("Please fill in all fields!");
        return false;
    }

    const allowedDomains = ["@gmail.com", "@email.com"];
    const isValidDomain = allowedDomains.some(domain => email.endsWith(domain));

    if (!isValidDomain) {
        alert("Only @gmail.com or @email.com emails are allowed!");
        return false;
    }

    if (password !== confirmPassword) {
        confirmError.textContent = 'Passwords do not match!';
        return false;
    }
}

        
    </script>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <img src="mohit.png" alt="Logo" class="logo">
                <h2>Register as a Student</h2>
            </div>
            <form id="register-form" action="registerstudent.php" method="POST" onsubmit="return handleRegistration();">
                <div class="form-group">
                    <label for="username">Username :</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password :</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm Password :</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                    <div id="confirm-error" class="error" style="color: red;"></div> <!-- Error message for password mismatch -->
                </div>
                <button type="submit" name="submit"  class="register-btn">Register</button>
            </form>

            <p class="login-link">Already Registered? <a href="loginstudent.php">Login Now</a></p>
        </div>
    </div>
</body>
</html>
