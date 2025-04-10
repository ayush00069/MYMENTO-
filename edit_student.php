<?php
session_start();
include "db_connect.php"; // Ensure database connection is included

if (!isset($_SESSION["username"])) {
    echo "<script>alert('Unauthorized access!'); window.location.href='loginadmin.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']); // Sanitize input

    // Fetch student details
    $sql = "SELECT * FROM student_profile WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $enrollment_number = $_POST['enrollment_number'];
    $division = $_POST['division'];
    $dob = $_POST['dob'];
    $age = $_POST['age'];

    // Update query
    $sql = "UPDATE student_profile SET full_name=?, enrollment_no=?, division=?, dob=?, age=? WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $full_name, $enrollment_number, $division, $dob, $age, $student_id);

    if ($stmt->execute()) {
        echo "<script>alert('Student updated successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating student!'); window.location.href='admin_dashboard.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Student</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Enrollment Number</label>
            <input type="text" name="enrollment_number" class="form-control" value="<?php echo htmlspecialchars($student['enrollment_no']); ?>" required>

        </div>
        <div class="mb-3">
            <label class="form-label">Division</label>
            <input type="text" name="division" class="form-control" value="<?php echo htmlspecialchars($student['division']); ?>" required>
        </div>
        <div class="mb-3">
    <label class="form-label">Date of Birth</label>
    <input type="date" name="dob" id="dob" class="form-control" value="<?php echo htmlspecialchars($student['dob']); ?>" required onchange="calculateAge()">
</div>

        <div class="mb-3">
            <label class="form-label">Age</label>
            <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($student['age']); ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update Student</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
<script>
function calculateAge() {
    const dobInput = document.getElementById('dob');
    const ageInput = document.querySelector('input[name="age"]');
    
    if (!dobInput.value) return;

    const today = new Date();
    const dob = new Date(dobInput.value);
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();

    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
        age--;
    }

    ageInput.value = age;
}
</script>

</html>
