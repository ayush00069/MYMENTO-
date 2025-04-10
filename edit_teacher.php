<?php
include "db_connect.php";

if (isset($_GET['id'])) {
    $teacher_id = intval($_GET['id']);
    
    // Fetch teacher details
    $sql = "SELECT * FROM teacherprofile WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = intval($_POST['teacher_id']);
    $full_name = $_POST['full_name'];
    $mentoringclass = $_POST['mentoringclass'];
    $qualification = $_POST['qualification'];
    
    $sql = "UPDATE teacherprofile SET full_name = ?, mentoringclass = ?, qualification = ? WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $full_name, $mentoringclass, $qualification, $teacher_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Teacher details updated successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating teacher details!'); window.location.href='admin_dashboard.php';</script>";
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
    <title>Edit Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Teacher Details</h2>
    <form method="POST">
        <input type="hidden" name="teacher_id" value="<?php echo $teacher['teacher_id']; ?>">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="full_name" value="<?php echo $teacher['full_name']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mentoring Class</label>
            <input type="text" class="form-control" name="mentoringclass" value="<?php echo $teacher['mentoringclass']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Qualification</label>
            <input type="text" class="form-control" name="qualification" value="<?php echo $teacher['qualification']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
