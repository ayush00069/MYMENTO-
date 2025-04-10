<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Start session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mymento";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: loginstudent.php"); // Redirect to login if not logged in
    exit();
}

$email = $_SESSION['email']; // Get email from session
$student = null;
$profile = null;

// Fetch basic student details from `studentreg`
$sql = "SELECT * FROM studentreg WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Fetch detailed profile from `student_profile`
$sql = "SELECT * FROM student_profile WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();



// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $division = trim($_POST['division']);
    $dob = trim($_POST['dob']);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
        echo "<script>alert('Invalid DOB format. Use YYYY-MM-DD.');</script>";
        exit();
    }
    
    $dobDate = new DateTime($dob);
$today = new DateTime();
$age = $today->diff($dobDate)->y;

    $enrollment_number = $profile['enrollment_no'] ?? trim($_POST['enrollment_number']);

    if (!$dob || $dob === '0000-00-00') {
        echo "<script>alert('Please enter a valid Date of Birth.');</script>";
        exit();
    }

    // Profile photo handling
    $profile_photo = $profile['profile_photo'] ?? 'default.jpg';
    if (!empty($_FILES['profile_photo']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES["profile_photo"]["type"], $allowed_types)) {
            $target_dir = "uploads/";
            $profile_photo = $target_dir . uniqid() . "_" . basename($_FILES["profile_photo"]["name"]);
            move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $profile_photo);
        } else {
            echo "<script>alert('Only JPG, PNG, and GIF files are allowed!');</script>";
        }
    }

    if ($profile) {
        $sql = "UPDATE student_profile SET full_name = ?, division = ?, dob = ?, age = ?, profile_photo = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiss", $full_name, $division, $dob, $age, $profile_photo, $email);
    } else {
        $sql = "INSERT INTO student_profile (enrollment_no, email, full_name, division, dob, age, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssiss", $enrollment_number, $email, $full_name, $division, $dob, $age, $profile_photo);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='studentprofileinfinal.php';</script>";
    } else {
        echo "Error updating profile: " . $conn->error;
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
    <title>Update Student Profile</title>
    <link rel="stylesheet" href="updatestudentinfinal.css">
</head>
<body>
    <div class="header">Update Student Profile</div>

    <form action="" method="post" enctype="multipart/form-data" class="update-form">
        <label>Enrollment Number:</label>
        <input type="text" name="enrollment_number" required value="<?= htmlspecialchars($profile['enrollment_no'] ?? '') ?>">

        <label>Full Name:</label>
        <input type="text" name="full_name" required value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>">

        <label>Division:</label>
        <select name="division" required>
            <option value="A" <?= isset($profile['division']) && $profile['division'] == 'A' ? 'selected' : '' ?>>A</option>
            <option value="B" <?= isset($profile['division']) && $profile['division'] == 'B' ? 'selected' : '' ?>>B</option>
            <option value="C" <?= isset($profile['division']) && $profile['division'] == 'C' ? 'selected' : '' ?>>C</option>
        </select>

        <label>Date of Birth:</label>
        <input type="date" name="dob" id="dob"
    value="<?= htmlspecialchars($profile['dob'] ?? '') ?>"
    placeholder="YYYY-MM-DD"
    required onchange="calculateAge()">

        <label>Age:</label>
        <input type="number" name="age" id="age" required value="<?= htmlspecialchars($profile['age'] ?? '') ?>" readonly>

        <label>Profile Photo:</label>
        <input type="file" name="profile_photo" accept="image/*">

        <button type="submit">Update Profile</button>
    </form>

    <script>
    function calculateAge() {
        let dob = document.getElementById('dob').value;
        if (dob) {
            let dobDate = new Date(dob);
            let today = new Date();
            let age = today.getFullYear() - dobDate.getFullYear();
            let monthDiff = today.getMonth() - dobDate.getMonth();
            
            // Adjust age if the birthday hasn't occurred yet this year
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                age--;
            }

            document.getElementById('age').value = age;
        }
    }
    </script>
</body>
</html>
