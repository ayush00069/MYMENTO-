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

$message = "";
$studentProfile = null;
$studentData = null;
$marksheetData = null;
$collegeMarksheets = [];
$collegeFeeReceipts = [];

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['enrollment_no'])) {
    $enrollment_no = $_GET['enrollment_no'];
    
    // Delete student data from all related tables
    $tables = ['student_profile', 'student_aadhar', 'school_marksheets', 'clg_marksheets', 'clg_fees_receipts'];
    foreach ($tables as $table) {
        $sqlDelete = "DELETE FROM $table WHERE enrollment_no = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("s", $enrollment_no);
        $stmtDelete->execute();
        $stmtDelete->close();
    }
    
    echo "<script>alert('Student data deleted successfully!'); window.location.href='displaystudentdata.php';</script>";
    exit();
}

// Handle search request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enrollment_no = $_POST['enrollment_no'];

    // Fetch student profile details
    $sqlProfile = "SELECT email, enrollment_no, division, dob, age, full_name, profile_photo FROM student_profile WHERE enrollment_no = ?";
    $stmtProfile = $conn->prepare($sqlProfile);
    $stmtProfile->bind_param("s", $enrollment_no);
    $stmtProfile->execute();
    $resultProfile = $stmtProfile->get_result();
    $stmtProfile->close();

    if ($resultProfile->num_rows > 0) {
        $studentProfile = $resultProfile->fetch_assoc();
    }

    // Fetch student Aadhaar details
    $sqlAadhar = "SELECT student_aadhar_image, father_aadhar_image, mother_aadhar_image FROM student_aadhar WHERE enrollment_no = ?";
    $stmtAadhar = $conn->prepare($sqlAadhar);
    $stmtAadhar->bind_param("s", $enrollment_no);
    $stmtAadhar->execute();
    $resultAadhar = $stmtAadhar->get_result();
    $stmtAadhar->close();

    if ($resultAadhar->num_rows > 0) {
        $studentData = $resultAadhar->fetch_assoc();
    }

    // Fetch school marksheets
    $sqlMarksheets = "SELECT marksheet_10, marksheet_12 FROM school_marksheets WHERE enrollment_no = ?";
    $stmtMarksheets = $conn->prepare($sqlMarksheets);
    $stmtMarksheets->bind_param("s", $enrollment_no);
    $stmtMarksheets->execute();
    $resultMarksheets = $stmtMarksheets->get_result();
    $stmtMarksheets->close();

    if ($resultMarksheets->num_rows > 0) {
        $marksheetData = $resultMarksheets->fetch_assoc();
    }

    // Fetch college marksheets
    $sqlClgMarksheets = "SELECT semester, file_path FROM clg_marksheets WHERE enrollment_no = ? AND status = 'approved' ORDER BY semester ASC";
    $stmtClgMarksheets = $conn->prepare($sqlClgMarksheets);
    $stmtClgMarksheets->bind_param("s", $enrollment_no);
    $stmtClgMarksheets->execute();
    $resultClgMarksheets = $stmtClgMarksheets->get_result();
    $stmtClgMarksheets->close();

    while ($row = $resultClgMarksheets->fetch_assoc()) {
        $collegeMarksheets[] = $row;
    }

    $sqlClgFees = "SELECT semester, file_path FROM clg_fees_receipts WHERE enrollment_no = ? AND status = 'approved' ORDER BY semester ASC";
    $stmtClgFees = $conn->prepare($sqlClgFees);
    $stmtClgFees->bind_param("s", $enrollment_no);
    $stmtClgFees->execute();
    $resultClgFees = $stmtClgFees->get_result();
    $stmtClgFees->close();
    
    $collegeFeeReceipts = [];
    while ($row = $resultClgFees->fetch_assoc()) {
        $collegeFeeReceipts[] = $row;
    }
    
    if (!$studentProfile && !$studentData && !$marksheetData && empty($collegeMarksheets) && empty($collegeFeeReceipts)) {
        $message = "No student details found for this enrollment number.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Student Profile</title>
    <link rel="stylesheet" href="displaystudentdata.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="header">Teacher Dashboard - Student Profile</div>

    <div class="back-button">
        <a href="final.php">Back to Dashboard</a>
    </div>

    <div class="main-container">
        <h2>Search Student Profile</h2>

        <form method="POST">
            <label for="enrollment_no">Enter Enrollment Number:</label>
            <input type="text" id="enrollment_no" name="enrollment_no" required>
            <button type="submit">Search</button>
        </form>

        <?php if ($message): ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if (!empty($studentProfile)): ?>
            <h3>Student Profile</h3>
            <p><strong>Full Name:</strong> <?php echo $studentProfile['full_name']; ?></p>
            <p><strong>Email:</strong> <?php echo $studentProfile['email']; ?></p>
            <p><strong>Enrollment Number:</strong> <?php echo $studentProfile['enrollment_no']; ?></p>
            <p><strong>Division:</strong> <?php echo $studentProfile['division'] ?? 'N/A'; ?></p>
            <p><strong>Date of Birth:</strong> <?php echo $studentProfile['dob'] ?? 'N/A'; ?></p>
            <p><strong>Age:</strong> <?php echo $studentProfile['age'] ?? 'N/A'; ?></p>
            <p><strong>Profile Photo:</strong></p>
            <img src="<?php echo $studentProfile['profile_photo']; ?>" width="150px" alt="Profile Photo">
            <br>
            <a href="displaystudentdata.php?delete=true&enrollment_no=<?php echo $studentProfile['enrollment_no']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this student?');">Delete Student</a>
        <?php endif; ?>

        <?php if (!empty($studentData)): ?>
            <h3>Aadhaar Documents</h3>
            
            <p><strong>Student Aadhaar:</strong></p>
            <img src="<?php echo $studentData['student_aadhar_image']; ?>" width="250px" alt="Student Aadhaar">
            
            <p><strong>Father's Aadhaar:</strong></p>
            <img src="<?php echo $studentData['father_aadhar_image']; ?>" width="250px" alt="Father's Aadhaar">
            
            <p><strong>Mother's Aadhaar:</strong></p>
            <img src="<?php echo $studentData['mother_aadhar_image']; ?>" width="250px" alt="Mother's Aadhaar">

            <?php if (!empty($studentData)): ?>
    <h3>Aadhaar Documents <a href="delete.php?type=aadhar&enrollment_no=<?php echo $enrollment_no; ?>" class="delete-btn">Delete</a></h3>
<?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($marksheetData)): ?>
            <h3>School Marksheets</h3>

            <p><strong>10th Marksheet:</strong></p>
            <?php if (!empty($marksheetData['marksheet_10'])): ?>
                <img src="<?php echo $marksheetData['marksheet_10']; ?>" width="250px" alt="10th Marksheet">
            <?php else: ?>
                <p>No 10th marksheet available.</p>
            <?php endif; ?>

            <p><strong>12th Marksheet:</strong></p>
            <?php if (!empty($marksheetData['marksheet_12'])): ?>
                <img src="<?php echo $marksheetData['marksheet_12']; ?>" width="250px" alt="12th Marksheet">
            <?php else: ?>
                <p>No 12th marksheet available.</p>
            <?php endif; ?>
            <?php if (!empty($marksheetData)): ?>
    <h3>School Marksheets <a href="delete.php?type=school_marksheets&enrollment_no=<?php echo $enrollment_no; ?>" class="delete-btn">Delete</a></h3>
<?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($collegeMarksheets)): ?>
    <h3>College Marksheets</h3>
    <?php foreach ($collegeMarksheets as $marksheet): ?>
        <p><strong>Semester <?php echo $marksheet['semester']; ?>:</strong></p>
        <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $marksheet['file_path'])): ?>
            <img src="<?php echo $marksheet['file_path']; ?>" width="250px" alt="Semester <?php echo $marksheet['semester']; ?> Marksheet">
        <?php elseif (preg_match('/\.pdf$/i', $marksheet['file_path'])): ?>
            <embed src="<?php echo $marksheet['file_path']; ?>" type="application/pdf" width="500px" height="600px">
        <?php else: ?>
            <a href="<?php echo $marksheet['file_path']; ?>" target="_blank">View Marksheet</a>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if (!empty($collegeMarksheets)): ?>
    <h3>College Marksheets <a href="delete.php?type=college_marksheets&enrollment_no=<?php echo $enrollment_no; ?>" class="delete-btn">Delete</a></h3>
<?php endif; ?>
<?php endif; ?>

<?php if (!empty($collegeFeeReceipts)): ?>
    <h3>College Fee Receipts</h3>
    <?php foreach ($collegeFeeReceipts as $receipt): ?>
        <p><strong>Semester <?php echo $receipt['semester']; ?> Fee Receipt:</strong></p>
        <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $receipt['file_path'])): ?>
            <img src="<?php echo $receipt['file_path']; ?>" width="250px" alt="Semester <?php echo $receipt['semester']; ?> Fee Receipt">
        <?php elseif (preg_match('/\.pdf$/i', $receipt['file_path'])): ?>
            <embed src="<?php echo $receipt['file_path']; ?>" type="application/pdf" width="500px" height="600px">
        <?php else: ?>
            <a href="<?php echo $receipt['file_path']; ?>" target="_blank">View Semester <?php echo $receipt['semester']; ?> Fee Receipt</a>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if (!empty($collegeFeeReceipts)): ?>
    <h3>College Fee Receipts <a href="delete.php?type=college_fees&enrollment_no=<?php echo $enrollment_no; ?>" class="delete-btn">Delete</a></h3>
<?php endif; ?>
<?php endif; ?>

</body>
</html>
