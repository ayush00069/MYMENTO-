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

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: loginstudent.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch the latest student profile data
$sql = "SELECT * FROM student_profile WHERE email = ? ORDER BY updated_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<p>No profile data found. Please update your profile.</p>";
    exit();
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="studentprofileinfinal.css">
</head>
<body>
    <div class="header">Student Profile</div>
    <a href="stufinal.php" class="back-button">Back to Dashboard</a>
</div>
    <div class="profile-container">
        <div class="profile-photo">
            <img src="<?php echo !empty($row['profile_photo']) ? htmlspecialchars($row['profile_photo']) : 'default-avatar.png'; ?>" 
                 alt="Profile Photo" 
                 style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
        </div>

        <div class="input-container">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($row['full_name'] ?? 'N/A'); ?></p>
            <p><strong>Enrollment Number:</strong> <?php echo htmlspecialchars($row['enrollment_no'] ?? 'N/A'); ?></p>
            <p><strong>Division:</strong> <?php echo htmlspecialchars($row['division'] ?? 'N/A'); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($row['dob'] ?? 'N/A'); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($row['age'] ?? 'N/A'); ?></p>
            <p><strong>Last Updated:</strong> <?php echo htmlspecialchars($row['updated_at'] ?? 'N/A'); ?></p>
        </div>
    </div>
   
</body>
</html>
