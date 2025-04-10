<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'mymento';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$studentProfile = null;
$studentData = null;
$marksheetData = null;
$collegeMarksheets = [];
$collegeFeeReceipts = [];

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
    $sqlClgMarksheets = "SELECT semester, file_path FROM clg_marksheets WHERE enrollment_no = ? ORDER BY semester ASC";
    $stmtClgMarksheets = $conn->prepare($sqlClgMarksheets);
    $stmtClgMarksheets->bind_param("s", $enrollment_no);
    $stmtClgMarksheets->execute();
    $resultClgMarksheets = $stmtClgMarksheets->get_result();
    $stmtClgMarksheets->close();

    while ($row = $resultClgMarksheets->fetch_assoc()) {
        $collegeMarksheets[] = $row;
    }

    // Fetch college fee receipts
    $sqlClgFees = "SELECT semester, file_path FROM clg_fees_receipts WHERE enrollment_no = ? ORDER BY semester ASC";
    $stmtClgFees = $conn->prepare($sqlClgFees);
    $stmtClgFees->bind_param("s", $enrollment_no);
    $stmtClgFees->execute();
    $resultClgFees = $stmtClgFees->get_result();
    $stmtClgFees->close();
    
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
    <link rel="stylesheet" href="parentdata.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="main-container">
        <h2>Search Student Profile</h2>
        <form method="POST">
            <label for="enrollment_no">Enter Enrollment Number:</label>
            <input type="text" id="enrollment_no" name="enrollment_no" required>
            <button type="submit">Search</button>
        </form>
        <form action="months.php" method="GET">
                <input type="hidden" name="enrollment_no" value="<?php echo $studentProfile['enrollment_no']; ?>">
                <button type="submit" class="attendance-button">Show Attendance</button>
            </form>
        <?php if ($message): ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Display Student Profile -->
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

         
        <?php endif; ?>

        <!-- Display Aadhaar Details -->
        <?php if (!empty($studentData)): ?>
            <h3>Aadhaar Details</h3>
            <p><strong>Student Aadhaar:</strong></p>
            <img src="<?php echo $studentData['student_aadhar_image']; ?>" width="150px" alt="Student Aadhaar">
            <p><strong>Father's Aadhaar:</strong></p>
            <img src="<?php echo $studentData['father_aadhar_image']; ?>" width="150px" alt="Father Aadhaar">
            <p><strong>Mother's Aadhaar:</strong></p>
            <img src="<?php echo $studentData['mother_aadhar_image']; ?>" width="150px" alt="Mother Aadhaar">
        <?php endif; ?>

        <!-- Display School Marksheets -->
        <?php if (!empty($marksheetData)): ?>
            <h3>School Marksheets</h3>
            <p><strong>10th Marksheet:</strong></p>
            <img src="<?php echo $marksheetData['marksheet_10']; ?>" width="150px" alt="10th Marksheet">
            <p><strong>12th Marksheet:</strong></p>
            <img src="<?php echo $marksheetData['marksheet_12']; ?>" width="150px" alt="12th Marksheet">
        <?php endif; ?>

        <!-- Display College Marksheets -->
        <?php if (!empty($collegeMarksheets)): ?>
            <h3>College Marksheets</h3>
            <ul>
                <?php foreach ($collegeMarksheets as $marksheet): ?>
                    <li>
                        <strong>Semester <?php echo $marksheet['semester']; ?>:</strong>
                        <img src="<?php echo $marksheet['file_path']; ?>" width="150px" alt="Semester <?php echo $marksheet['semester']; ?> Marksheet">
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Display College Fee Receipts -->
        <?php if (!empty($collegeFeeReceipts)): ?>
            <h3>College Fee Receipts</h3>
            <ul>
                <?php foreach ($collegeFeeReceipts as $feeReceipt): ?>
                    <li>
                        <strong>Semester <?php echo $feeReceipt['semester']; ?> Fee Receipt:</strong>
                        <img src="<?php echo $feeReceipt['file_path']; ?>" width="150px" alt="Semester <?php echo $feeReceipt['semester']; ?> Fee Receipt">
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
