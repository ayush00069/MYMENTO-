<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'mymento';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if not logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: loginteacher.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Mento - Profile</title>
    <link rel="stylesheet" href="finalcss.css?v=<?php echo time(); ?>"> <!-- Prevents caching -->
</head>
<body>
    <div class="header">My Mento</div>

    <div class="menu-container">
        <a href="#" onclick="showSection('welcome-section')">Home</a>
        <a href="updateprofileteacher.php">Update Profile</a>
        <a href="teacherprofileinfinal.php">My Profile</a>
        <a href="#" onclick="showSection('attendance-section')">Attendance</a>
        <a href="notification.php">Notifications</a>
        <a href="displaystudentdata.php">Student Profile</a>

    </div>

    <div class="main-container">
        <div class="welcome-container" id="welcome-section">
            <h1>Welcome to My Mento</h1>
            <p>Have a Good Day!</p>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>



        <div class="attendance-container" id="attendance-section" style="display: none;">
            <h2>Attendance</h2>
            <button onclick="window.location.href='months.php'">Upload Attendance</button>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            document.getElementById('welcome-section').style.display = 'none';
            document.getElementById('attendance-section').style.display = 'none';

            document.getElementById(sectionId).style.display = 'block';

            if (sectionId === 'welcome-section') {
                const message = document.getElementById('home-message');
                message.style.display = 'block';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 5000);
            }
        }
    </script>
</body>
</html>
