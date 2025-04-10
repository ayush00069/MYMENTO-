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

// Check if marksheet exists
$checkMarksheet = $conn->prepare("SELECT * FROM school_marksheets WHERE enrollment_no = ?");
$checkMarksheet->bind_param("s", $enrollment_no);
$checkMarksheet->execute();
$result = $checkMarksheet->get_result();
$marksheet = $result->fetch_assoc();
$status = $marksheet['status'] ?? 'none';
$checkMarksheet->close();

// Handle file uploads
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    function sanitizeFileName($fileName) {
        return preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $fileName);
    }

    // File paths
    $marksheet10Path = !empty($_FILES['marksheet_10']['name']) ? $uploadDir . time() . "_10th_" . sanitizeFileName(basename($_FILES["marksheet_10"]["name"])) : "";
    $marksheet12Path = !empty($_FILES['marksheet_12']['name']) ? $uploadDir . time() . "_12th_" . sanitizeFileName(basename($_FILES["marksheet_12"]["name"])) : "";

    // Move uploaded files
    if ($marksheet10Path) move_uploaded_file($_FILES["marksheet_10"]["tmp_name"], $marksheet10Path);
    if ($marksheet12Path) move_uploaded_file($_FILES["marksheet_12"]["tmp_name"], $marksheet12Path);

    if ($marksheet) {
        // Update existing entry
        $stmt = $conn->prepare("UPDATE school_marksheets SET marksheet_10 = ?, marksheet_12 = ?, status = 'pending' WHERE enrollment_no = ?");
        $stmt->bind_param("sss", $marksheet10Path, $marksheet12Path, $enrollment_no);
    } else {
        // Insert new entry
        $stmt = $conn->prepare("INSERT INTO school_marksheets (enrollment_no, marksheet_10, marksheet_12, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("sss", $enrollment_no, $marksheet10Path, $marksheet12Path);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Marksheet uploaded successfully! Waiting for approval.'); window.location.href='stufinal.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
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
    <title>Upload School Marksheet</title>
    <link rel="stylesheet" href="schoolmarksheet.css">
    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).style.display = "block";
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</head>
<body>

<div class="header">Upload School Marksheet</div>

<div class="main-container">
    <h2>School Marksheet Upload</h2>

    <p class="status-message">
        <?php 
            if ($status === 'approved') {
                echo "<strong>Status: Approved ✅</strong> Your marksheets have been verified.";
            } elseif ($status === 'pending') {
                echo "<strong>Status: Pending ⏳</strong> Your request is awaiting approval.";
            } elseif ($status === 'rejected') {
                echo "<strong>Status: Rejected ❌</strong> Please upload new documents.";
            } else {
                echo "<strong>Status: Not Submitted 📝</strong> Please upload your marksheets.";
            }
        ?>
    </p>

    <form method="POST" enctype="multipart/form-data">
        <label for="marksheet_10">10th Marksheet:</label>
        <input type="file" id="marksheet_10" name="marksheet_10" accept="image/*, application/pdf" onchange="previewImage(this, 'marksheet10Preview')">
        <div class="preview-container">
            <img id="marksheet10Preview" class="preview-img"
                 src="<?php echo (!empty($marksheet['marksheet_10'])) ? $marksheet['marksheet_10'] : ''; ?>"
                 style="display: <?php echo (!empty($marksheet['marksheet_10'])) ? 'block' : 'none'; ?>;">
        </div>

        <label for="marksheet_12">12th Marksheet:</label>
        <input type="file" id="marksheet_12" name="marksheet_12" accept="image/*, application/pdf" onchange="previewImage(this, 'marksheet12Preview')">
        <div class="preview-container">
            <img id="marksheet12Preview" class="preview-img"
                 src="<?php echo (!empty($marksheet['marksheet_12'])) ? $marksheet['marksheet_12'] : ''; ?>"
                 style="display: <?php echo (!empty($marksheet['marksheet_12'])) ? 'block' : 'none'; ?>;">
        </div>

        <button type="submit">Upload Marksheet</button>
    </form>

    <button onclick="window.location.href='stufinal.php'" class="dashboard-button">⬅ Back to Dashboard</button>
</div>

</body>
</html>
