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

// Ensure teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    echo "<script>alert('Please log in first!'); window.location.href='loginteacher.php';</script>";
    exit();
}

// Fetch pending Aadhaar requests
$sql = "SELECT * FROM student_aadhar WHERE status = 'pending'";
$result = $conn->query($sql);

// Fetch pending Marksheet requests
$sql_marksheet = "SELECT * FROM school_marksheets WHERE status = 'pending'";
$result_marksheet = $conn->query($sql_marksheet);

// Fetch pending Semester Marksheet requests
$sql_semester_marksheet = "SELECT * FROM clg_marksheets WHERE status = 'pending'";
$result_semester_marksheet = $conn->query($sql_semester_marksheet);

// Fetch pending Fee Receipt requests
$sql_fees_receipt = "SELECT * FROM clg_fees_receipts WHERE status = 'pending'";
$result_fees_receipt = $conn->query($sql_fees_receipt);

// Handle approval/rejection for requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $enrollment_no = $_POST['enrollment_no'];
    $action = $_POST['action'];
    $semester = isset($_POST['semester']) ? $_POST['semester'] : null;

    $table = "";
    switch ($action) {
        case 'approve_aadhar':
        case 'reject_aadhar':
            $table = "student_aadhar";
            break;
        case 'approve_marksheet':
        case 'reject_marksheet':
            $table = "school_marksheets";
            break;
        case 'approve_semester_marksheet':
        case 'reject_semester_marksheet':
            $table = "clg_marksheets";
            break;
        case 'approve_fees_receipt':
        case 'reject_fees_receipt':
            $table = "clg_fees_receipts";
            break;
    }

    if ($table) {
        $status = strpos($action, "approve") !== false ? "approved" : "rejected";

        // Use semester in condition if the table is college-related
        if (in_array($table, ['clg_marksheets', 'clg_fees_receipts']) && $semester !== null) {
            $update_stmt = $conn->prepare("UPDATE $table SET status = ? WHERE enrollment_no = ? AND semester = ?");
            $update_stmt->bind_param("ssi", $status, $enrollment_no, $semester);
        } else {
            $update_stmt = $conn->prepare("UPDATE $table SET status = ? WHERE enrollment_no = ?");
            $update_stmt->bind_param("ss", $status, $enrollment_no);
        }

        $update_stmt->execute();
        $update_stmt->close();
        echo "<script>alert('Request updated successfully!'); window.location.href='notification.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Aadhaar & Marksheet Requests</title>
    <link rel="stylesheet" href="notification.css">
</head>
<body>

<div class="header">My Mento - Requests</div>
<div class="menu-container">
    <a href="notification.php">Notifications</a>
</div>

<div style="margin: 20px;">
    <a href="final.php" style="
        text-decoration: none;
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        display: inline-block;
    ">
        ⬅️ Back to Dashboard
    </a>
</div>


<div class="main-container">
    <h2>Pending Aadhaar Requests</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Enrollment No</th>
                <th>Student Aadhaar</th>
                <th>Father Aadhaar</th>
                <th>Mother Aadhaar</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['enrollment_no']; ?></td>
                    <td><img src="<?php echo $row['student_aadhar_image']; ?>" width="100"></td>
                    <td><img src="<?php echo $row['father_aadhar_image']; ?>" width="100"></td>
                    <td><img src="<?php echo $row['mother_aadhar_image']; ?>" width="100"></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="enrollment_no" value="<?php echo $row['enrollment_no']; ?>">
                            <button type="submit" name="action" value="approve_aadhar">Approve</button>
                            <button type="submit" name="action" value="reject_aadhar">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending Aadhaar requests.</p>
    <?php endif; ?>
</div>

<div class="main-container">
    <h2>Pending Marksheet Requests</h2>

    <?php if ($result_marksheet->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Enrollment No</th>
                <th>10th Marksheet</th>
                <th>12th Marksheet</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result_marksheet->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['enrollment_no']; ?></td>
                    <td><a href="<?php echo $row['marksheet_10']; ?>" target="_blank">View 10th Marksheet</a></td>
                    <td><a href="<?php echo $row['marksheet_12']; ?>" target="_blank">View 12th Marksheet</a></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="enrollment_no" value="<?php echo $row['enrollment_no']; ?>">
                            <button type="submit" name="action" value="approve_marksheet">Approve</button>
                            <button type="submit" name="action" value="reject_marksheet">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending Marksheet requests.</p>
    <?php endif; ?>
</div>

<div class="main-container">
    <h2>Pending Semester Marksheet Requests</h2>
    <?php if ($result_semester_marksheet->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Enrollment No</th>
                <th>Semester</th>
                <th>Marksheet</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result_semester_marksheet->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['enrollment_no']; ?></td>
                    <td><?php echo $row['semester']; ?></td>
                    <td><a href="<?php echo $row['file_path']; ?>" target="_blank">View Marksheet</a></td>
                    <td>
                    <form method="POST">
    <input type="hidden" name="enrollment_no" value="<?php echo $row['enrollment_no']; ?>">
    <input type="hidden" name="semester" value="<?php echo $row['semester']; ?>">
    <input type="hidden" name="table" value="clg_marksheets">
    <button type="submit" name="action" value="approve_semester_marksheet">Approve</button>
    <button type="submit" name="action" value="reject_semester_marksheet">Reject</button>
</form>

                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending Semester Marksheet requests.</p>
    <?php endif; ?>
</div>
<!-- Pending Fee Receipt Requests -->
<div class="main-container">
    <h2>Pending Fee Receipt Requests</h2>
    <?php if ($result_fees_receipt->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Enrollment No</th>
                <th>Semester</th>
                <th>Fee Receipt</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result_fees_receipt->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['enrollment_no']; ?></td>
                    <td><?php echo $row['semester']; ?></td>
                    <td><a href="<?php echo $row['file_path']; ?>" target="_blank">View Fee Receipt</a></td>
                    <td>
                    <form method="POST">
    <input type="hidden" name="enrollment_no" value="<?php echo $row['enrollment_no']; ?>">
    <input type="hidden" name="semester" value="<?php echo $row['semester']; ?>">
    <input type="hidden" name="table" value="clg_fees_receipts">
    <button type="submit" name="action" value="approve_fees_receipt">Approve</button>
    <button type="submit" name="action" value="reject_fees_receipt">Reject</button>
</form>

                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending Fee Receipt requests.</p>
    <?php endif; ?>
</div>


</body>
</html>
