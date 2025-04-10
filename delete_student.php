<?php
session_start();
include "db_connect.php"; // Ensure database connection is included

if (!isset($_SESSION["username"])) {
    echo "<script>alert('Unauthorized access!'); window.location.href='loginadmin.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']); // Sanitize input

    // Get the student's enrollment number to delete related records
    $sql = "SELECT enrollment_no FROM student_profile WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if (!$student) {
        echo "<script>alert('Student not found!'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }

    $enrollment_no = $student['enrollment_no'];

    // Function to delete files from server
    function deleteFiles($conn, $table, $column, $enrollment_no) {
        $query = "SELECT $column FROM $table WHERE enrollment_no = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $enrollment_no);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            if (file_exists($row[$column])) {
                unlink($row[$column]); // Delete file
            }
        }
        $stmt->close();
    }

    // Delete associated files first
    deleteFiles($conn, "student_aadhar", "student_aadhar_image", $enrollment_no);
    deleteFiles($conn, "student_aadhar", "father_aadhar_image", $enrollment_no);
    deleteFiles($conn, "student_aadhar", "mother_aadhar_image", $enrollment_no);
    deleteFiles($conn, "school_marksheets", "marksheet_10", $enrollment_no);
    deleteFiles($conn, "school_marksheets", "marksheet_12", $enrollment_no);
    deleteFiles($conn, "clg_marksheets", "file_path", $enrollment_no);
    deleteFiles($conn, "clg_fees_receipts", "file_path", $enrollment_no);

    // Delete records from related tables
    $tables = ["student_aadhar", "school_marksheets", "clg_marksheets", "clg_fees_receipts"];
    foreach ($tables as $table) {
        $sql = "DELETE FROM $table WHERE enrollment_no = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $enrollment_no);
        $stmt->execute();
        $stmt->close();
    }

    // Finally, delete student profile
    $sql = "DELETE FROM student_profile WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        echo "<script>alert('Student and all related data deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting student data!'); window.location.href='admin_dashboard.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request!'); window.location.href='admin_dashboard.php';</script>";
}

$conn->close();
?>
