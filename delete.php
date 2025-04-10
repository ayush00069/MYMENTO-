<?php
session_start();
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'mymento';

// Connect to database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure type and enrollment number are set
if (!isset($_GET['type']) || !isset($_GET['enrollment_no'])) {
    die("Invalid request.");
}

$type = $_GET['type'];
$enrollment_no = $_GET['enrollment_no'];

// Perform deletion based on type
switch ($type) {
    case "aadhar":
        // Delete from student_aadhar
        $sql1 = "DELETE FROM student_aadhar WHERE enrollment_no = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("s", $enrollment_no);
        $stmt1->execute();
        $stmt1->close();

     
    case "school_marksheets":
        $sql = "DELETE FROM school_marksheets WHERE enrollment_no = ?";
        break;
    
    case "college_marksheets":
        $sql = "DELETE FROM clg_marksheets WHERE enrollment_no = ?";
        break;
    
    case "college_fees":
        $sql = "DELETE FROM clg_fees_receipts WHERE enrollment_no = ?";
        break;
    
    default:
        die("Invalid type.");
}

// Execute deletion if not handled in the "aadhar" case
if ($type !== "aadhar") {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $enrollment_no);
    if ($stmt->execute()) {
        echo "<script>alert('Data deleted successfully!'); window.location.href='displaystudentdata.php';</script>";
    } else {
        echo "<script>alert('Error deleting data.'); window.location.href='displaystudentdata.php';</script>";
    }
    $stmt->close();
} else {
    echo "<script>alert('Aadhaar data deleted successfully!'); window.location.href='displaystudentdata.php';</script>";
}

$conn->close();
?>
