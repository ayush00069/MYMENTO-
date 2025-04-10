<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mymento";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if student is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in first!'); window.location.href='loginstudent.php';</script>";
    exit();
}

$email = $_SESSION['email']; // Email is used as the session identifier

// Fetch student profile details from `studentreg` table
$profile = null;
$sql = "SELECT * FROM studentreg WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="stufinal.css">
</head>
<body>

<div class="header">Student Profile</div>

<div class="menu-container">
    <a href="#" onclick="showSection('home')">🏠 Home</a>
    <a href="updatestudentinfinal.php">Update Profile</a>
    <a href="studentprofileinfinal.php">My Profile</a>
    <a href="aadharupdate.php">Aadhaar Card</a>
    <a href="schoolmarksheet.php">School Marksheet</a>
    <a href="marksheet.php">Semester Marksheet</a>
    <a href="fees_receipt.php">Semester Fees Receipts</a>
    <a href="months.php">Attendance</a>
  
</div>

<div class="main-container" id="home">
    <p class="welcome-text">Welcome to My Mento, <b><?php echo htmlspecialchars($profile['username']); ?></b>! 🎓</p>
    <p class="message">🚀 Students, stay updated and keep learning! 📚✨</p>

    <h2>Profile Details</h2>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($profile['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
        <!-- Logout Button Moved Here -->
        <a href="logout.php" class="logout-button">Logout</a>
</div>
</div>

<script>
    function showSection(sectionId) {
        document.querySelectorAll('.main-container').forEach(section => section.style.display = 'none');
        document.getElementById(sectionId).style.display = 'block';
    }
    showSection('home');
</script>

</body>
</html>
