<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmpassword'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Store password as plain text (Not recommended)
        $plain_password = $password;

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO teacherreg (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $plain_password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='loginteacher.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <title>Teacher Registration</title>
    <link rel="stylesheet" href="mohit.css">
    <script>
        // Function to handle client-side registration checks
        function handleRegistration() {
            const username = document.getElementById("username").value;
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmpassword").value;
            const confirmError = document.getElementById("confirm-error");

            // Clear any previous error messages
            confirmError.textContent = '';

            // Check if all fields are filled
            if (!username || !email || !password || !confirmPassword) {
                alert("Please fill in all fields!");
                return false; // Prevent form submission if fields are not filled
            }
            const allowedDomains = ["@gmail.com", "@email.com"];
    const isValidDomain = allowedDomains.some(domain => email.endsWith(domain));

    if (!isValidDomain) {
        alert("Only @gmail.com or @email.com emails are allowed!");
        return false;
    }
            // Check if passwords match
            if (password !== confirmPassword) {
                confirmError.textContent = 'Passwords do not match!'; // Show error message
                return false; // Prevent form submission if passwords don't match
            }
            return true; // Allow form submission if all checks pass
        }
    </script>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <img src="MOHIT.png" alt="Logo" class="logo">
                <h2>Register as a Teacher</h2>
            </div>
            <form id="register-form" action="registerteacher.php" method="POST" onsubmit="return handleRegistration();">
                <div class="form-group">
                    <label for="username">Username :</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address :</label>
                    <input type="email" id="email" name="email" required 
                           pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$" 
                           title="Please enter a valid email address with a .com domain (e.g., user@example.com)">
                </div>
                <div class="form-group">
                    <label for="password">Password :</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirmpassword">Confirm Password :</label>
                    <input type="password" id="confirmpassword" name="confirmpassword" required>
                    <div id="confirm-error" class="error" style="color: red;"></div> 
                </div>
                <button type="submit" name="submit" class="register-btn">Register</button>
            </form>

            <p class="login-link">Already Registered? <a href="loginteacher.php">Login Now</a></p>
        </div>
    </div>
</body>
</html>