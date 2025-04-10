<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mymento";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure student is logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in first!'); window.location.href='loginstudent.php';</script>";
    exit();
}

$email = $_SESSION['email'];
$message = "";

// Get Enrollment Number
$getEnrollment = $conn->prepare("SELECT enrollment_no FROM student_profile WHERE email = ?");
$getEnrollment->bind_param("s", $email);
$getEnrollment->execute();
$result = $getEnrollment->get_result();
$getEnrollment->close();

$enrollment_no = "";
if ($row = $result->fetch_assoc()) {
    $enrollment_no = $row['enrollment_no'];
} else {
    echo "<script>alert('Enrollment number not found!'); window.location.href='stufinal.php';</script>";
    exit();
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["marksheet"])) {
    $semester = $_POST["semester"];
    $targetDir = "uploads/marksheets/";
    $fileName = basename($_FILES["marksheet"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($_FILES["marksheet"]["tmp_name"], $targetFilePath)) {
        $sql = "INSERT INTO clg_marksheets (enrollment_no, email, semester, file_path, status, upload_date) 
                VALUES (?, ?, ?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $enrollment_no, $email, $semester, $targetFilePath);
        if ($stmt->execute()) {
            $update_notif_sql = "UPDATE clg_marksheets SET status = 'pending' WHERE enrollment_no = ? AND semester = ?";
            $notif_stmt = $conn->prepare($update_notif_sql);
            $notif_stmt->bind_param("si", $enrollment_no, $semester);
            $notif_stmt->execute();
            $notif_stmt->close();
            

            $message = "<p style='color: green;'>Marksheet uploaded successfully! Waiting for approval.</p>";
        } else {
            $message = "<p style='color: red;'>Database error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color: red;'>File upload failed.</p>";
    }
}

// Retrieve marksheets
$sql = "SELECT semester, file_path, status FROM clg_marksheets WHERE enrollment_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $enrollment_no);
$stmt->execute();
$result = $stmt->get_result();
$marksheets = [];
while ($row = $result->fetch_assoc()) {
    $marksheets[$row["semester"]] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Semester Marksheet</title>
    <link rel="stylesheet" href="semmarksheet.css">
</head>
<body>
<div class="header">Upload Semester Marksheet</div>
<div class="menu-container">
    <a href="stufinal.php">🏠 Back to Profile</a>
</div>
<div class="main-container">
    <h2>Upload Your Semester Marksheet</h2>
    <?php echo $message; ?>
    <form action="marksheet.php" method="POST" enctype="multipart/form-data">
        <label for="semester">Select Semester:</label>
        <select id="semester" name="semester" required>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
            <option value="3">Semester 3</option>
            <option value="4">Semester 4</option>
            <option value="5">Semester 5</option>
            <option value="6">Semester 6</option>
        </select>
        <label for="marksheet">Upload Marksheet (PDF, JPG, PNG):</label>
        <input type="file" id="marksheet" name="marksheet" accept=".pdf,.jpg,.png" required>
        <button type="submit">Upload</button>
    </form>
    <h2>Uploaded Marksheets</h2>
    <?php if (!empty($marksheets)): ?>
        <ul>
            <?php foreach ($marksheets as $sem => $data): ?>
                <li>
                    <strong>Semester <?php echo $sem; ?>:</strong>
                    <a href="<?php echo htmlspecialchars($data['file_path']); ?>" target="_blank">View Marksheet</a>
                    <span style="color: <?php echo ($data['status'] == 'approved') ? 'green' : (($data['status'] == 'rejected') ? 'red' : 'orange'); ?>;">
                        (<?php echo ucfirst($data['status']); ?>)
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No marksheets uploaded yet.</p>
    <?php endif; ?>
</div>
</body>
</html>