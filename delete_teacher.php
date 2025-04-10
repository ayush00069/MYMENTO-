<?php
include "db_connect.php";

if (isset($_GET['id'])) {
    $teacher_id = intval($_GET['id']);
    
    // Delete teacher record
    $sql = "DELETE FROM teacherprofile WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Teacher deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting teacher!'); window.location.href='admin_dashboard.php';</script>";
    }
    
    $stmt->close();
}
$conn->close();
?>
