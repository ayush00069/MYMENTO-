<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["username"])) {  
    echo "<script>alert('Unauthorized access!'); window.location.href='loginadmin.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $mentoringclass = $_POST["mentoringclass"];
    $qualification = $_POST["qualification"];
    
    $profile_photo_path = "uploads/default.png";
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
        move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file);
        $profile_photo_path = $target_file;
    }
    
    $sql = "INSERT INTO teacherprofile (full_name, mentoringclass, qualification, profile_photo_path) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $full_name, $mentoringclass, $qualification, $profile_photo_path);
    
    if ($stmt->execute()) {
        echo "<script>alert('Teacher added successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "Error: " . $conn->error;
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
    <title>Add Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add New Teacher</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mentoring Class</label>
            <input type="text" name="mentoringclass" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Qualification</label>
            <input type="text" name="qualification" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Profile Photo</label>
            <input type="file" name="profile_photo" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Teacher</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
