<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["username"])) {  
    echo "<script>alert('Unauthorized access!'); window.location.href='loginadmin.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $enrollment_number = $_POST["enrollment_no"];
    $email = $_POST["email"];
    $division = $_POST["division"];
    $dob = $_POST["dob"];
    $age = $_POST["age"];

    // Handle profile photo upload
    $profile_photo_path = "uploads/default.png";
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
        move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file);
        $profile_photo_path = $target_file;
    }

    // Insert student data directly into student_profile table
    $sql = "INSERT INTO student_profile (full_name, enrollment_no, email, division, dob, age, profile_photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $full_name, $enrollment_number, $email, $division, $dob, $age, $profile_photo_path);

    if ($stmt->execute()) {
        echo "<script>alert('Student added successfully!'); window.location.href='admin_dashboard.php';</script>";
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
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function calculateAge() {
            var dob = document.getElementById("dob").value;
            if (dob) {
                var dobDate = new Date(dob);
                var today = new Date();
                var age = today.getFullYear() - dobDate.getFullYear();
                var monthDiff = today.getMonth() - dobDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                    age--;
                }
                document.getElementById("age").value = age;
            }
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h2>Add New Student</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Enrollment Number</label>
            <input type="text" name="enrollment_no" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Division</label>
            <input type="text" name="division" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" id="dob" class="form-control" required onchange="calculateAge()">
        </div>
        <div class="mb-3">
            <label class="form-label">Age</label>
            <input type="number" name="age" id="age" class="form-control" readonly required>
        </div>
        <div class="mb-3">
            <label class="form-label">Profile Photo</label>
            <input type="file" name="profile_photo" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Student</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
