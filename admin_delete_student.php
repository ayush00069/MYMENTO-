<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["username"])) {
    echo "<script>alert('Unauthorized access!'); window.location.href='loginadmin.php';</script>";
    exit();
}

if (!isset($_GET['type']) || !isset($_GET['enrollment_no'])) {
    echo "<script>alert('Invalid request!'); window.location.href='admin_dashboard.php';</script>";
    exit();
}

$type = $_GET['type'];
$enrollment_no = $_GET['enrollment_no'];

switch ($type) {
    case 'aadhaar':
        $conn->query("DELETE FROM student_aadhar WHERE enrollment_no = '$enrollment_no'");
        break;

    case 'school_marksheets':
        $conn->query("DELETE FROM school_marksheets WHERE enrollment_no = '$enrollment_no'");
        break;

    case 'college_marksheets':
        $conn->query("DELETE FROM clg_marksheets WHERE enrollment_no = '$enrollment_no'");
        break;

    case 'fee_receipts':
        $conn->query("DELETE FROM clg_fees_receipts WHERE enrollment_no = '$enrollment_no'");
        break;

    default:
        echo "<script>alert('Unknown delete type!'); window.location.href='admin_dashboard.php';</script>";
        exit();
}

echo "<script>alert('Deleted successfully!'); window.history.back();</script>";
?>
