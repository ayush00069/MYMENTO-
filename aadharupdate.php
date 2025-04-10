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

// Ensure student is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in first!'); window.location.href='loginstudent.php';</script>";
    exit();
}

$email = $_SESSION['email'];
$message = "";

// Get Enrollment Number
$getEnrollment = $conn->prepare("SELECT enrollment_no FROM student_profile WHERE email = ?");
$getEnrollment->bind_param("s", $email);
$getEnrollment->execute();
$result = $getEnrollment->get_result();
$getEnrollment->close();

$enrollment_no = "";
if ($row = $result->fetch_assoc()) {
    $enrollment_no = $row['enrollment_no'];
} else {
    echo "<script>alert('Enrollment number not found!'); window.location.href='stufinal.php';</script>";
    exit();
}

// Handle file uploads
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    function sanitizeFileName($fileName) {
        return preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $fileName);
    }

    $studentAadharPath = $uploadDir . time() . "_student_" . sanitizeFileName(basename($_FILES["student_aadhar"]["name"]));
    $fatherAadharPath = $uploadDir . time() . "_father_" . sanitizeFileName(basename($_FILES["father_aadhar"]["name"]));
    $motherAadharPath = $uploadDir . time() . "_mother_" . sanitizeFileName(basename($_FILES["mother_aadhar"]["name"]));

    move_uploaded_file($_FILES["student_aadhar"]["tmp_name"], $studentAadharPath);
    move_uploaded_file($_FILES["father_aadhar"]["tmp_name"], $fatherAadharPath);
    move_uploaded_file($_FILES["mother_aadhar"]["tmp_name"], $motherAadharPath);

    $stmt = $conn->prepare("INSERT INTO student_aadhar (enrollment_no, student_aadhar_image, father_aadhar_image, mother_aadhar_image, status) VALUES (?, ?, ?, ?, 'pending') ON DUPLICATE KEY UPDATE student_aadhar_image=?, father_aadhar_image=?, mother_aadhar_image=?, status='pending'");
    $stmt->bind_param("sssssss", $enrollment_no, $studentAadharPath, $fatherAadharPath, $motherAadharPath, $studentAadharPath, $fatherAadharPath, $motherAadharPath);
    
    if ($stmt->execute()) {
        $message = "Aadhaar update request sent for approval.";
    } else {
        $message = "Error updating Aadhaar: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch Aadhaar details and status
$status = "";
$studentAadhar = $fatherAadhar = $motherAadhar = "placeholder.png";
$query = $conn->prepare("SELECT student_aadhar_image, father_aadhar_image, mother_aadhar_image, status FROM student_aadhar WHERE enrollment_no = ?");
$query->bind_param("s", $enrollment_no);
$query->execute();
$result = $query->get_result();
$query->close();

if ($row = $result->fetch_assoc()) {
    $studentAadhar = $row['student_aadhar_image'];
    $fatherAadhar = $row['father_aadhar_image'];
    $motherAadhar = $row['mother_aadhar_image'];
    $status = ucfirst($row['status']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Aadhaar Card</title>
    <link rel="stylesheet" href="aadharupdate.css">
    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(previewId).src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</head>
<body>

<div class="header">Update Aadhaar Card</div>

<div class="sidebar">
    <a href="stufinal.php" class="dashboard-link">Back to Dashboard</a>
</div>

<div class="main-container">
    <h2>Aadhaar Card Upload</h2>
    <?php if ($message): ?>
        <p class="status-message"> <?php echo $message; ?> </p>
    <?php endif; ?>

    <p class="status">Current Status: <strong><?php echo $status; ?></strong></p>

    <h3>Uploaded Aadhaar Details</h3>
    <p><strong>Student Aadhaar:</strong></p>
    <img src="<?php echo $studentAadhar; ?>" class="preview-img">
    
    <p><strong>Father's Aadhaar:</strong></p>
    <img src="<?php echo $fatherAadhar; ?>" class="preview-img">
    
    <p><strong>Mother's Aadhaar:</strong></p>
    <img src="<?php echo $motherAadhar; ?>" class="preview-img">

    <form method="POST" enctype="multipart/form-data">
        <label for="student_aadhar">Student's Aadhaar:</label>
        <input type="file" id="student_aadhar" name="student_aadhar" accept="image/*" onchange="previewImage(this, 'studentPreview')">
        
        <label for="father_aadhar">Father's Aadhaar:</label>
        <input type="file" id="father_aadhar" name="father_aadhar" accept="image/*" onchange="previewImage(this, 'fatherPreview')">
        
        <label for="mother_aadhar">Mother's Aadhaar:</label>
        <input type="file" id="mother_aadhar" name="mother_aadhar" accept="image/*" onchange="previewImage(this, 'motherPreview')">
        
        <button type="submit">Submit</button>
    </form>
</div>

</body>
</html>
