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

// Fetch the latest teacher data
$query = $conn->prepare("SELECT * FROM teacherprofile WHERE teacher_id = ?");
$query->bind_param("s", $teacher_id);
$query->execute();
$result = $query->get_result();
$teacherData = $result->fetch_assoc();
$query->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="teacherprofileinfinal.css">
    <style>
        .back-button {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: #007BFF;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">My Mento</div>

    <!-- Back Button -->
    <a href="final.php" class="back-button">Back</a>

    <div class="profile-container">
        <h2>My Profile</h2>
        <?php if ($teacherData): ?>
            <p><strong>Teacher ID:</strong> <?php echo htmlspecialchars($teacherData['teacher_id']); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($teacherData['full_name']); ?></p>
            <p><strong>Mentoring Class:</strong> <?php echo htmlspecialchars($teacherData['mentoringclass']); ?></p>
            <p><strong>Qualification:</strong> <?php echo htmlspecialchars($teacherData['qualification']); ?></p>
            <?php if (!empty($teacherData['profile_photo_path'])): ?>
                <img src="<?php echo htmlspecialchars($teacherData['profile_photo_path']); ?>" alt="Profile Photo" width="100">
            <?php endif; ?>
        <?php else: ?>
            <p>No profile data available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
