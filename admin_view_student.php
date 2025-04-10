<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION["username"])) {
    echo "<script>alert('Unauthorized access!'); window.location.href='loginadmin.php';</script>";
    exit();
}

if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid request!'); window.location.href='admin_dashboard.php';</script>";
    exit();
}

$student_id = intval($_GET['id']);

// Get student profile
$sql = "SELECT * FROM student_profile WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

$enrollment_no = $student['enrollment_no'] ?? '';
$email = $student['email'] ?? '';
$division = $student['division'] ?? '';
$dob = $student['dob'] ?? '';
$age = $student['age'] ?? '';

// Aadhaar Details
$aadhaar = $conn->query("SELECT * FROM student_aadhar WHERE enrollment_no = '$enrollment_no'")->fetch_assoc();

// School Marksheets
$school_marks = $conn->query("SELECT * FROM school_marksheets WHERE enrollment_no = '$enrollment_no'")->fetch_assoc();

// College Marksheets
$college_marks = $conn->query("SELECT * FROM clg_marksheets WHERE enrollment_no = '$enrollment_no'");

// College Fees Receipts
$fees_receipts = $conn->query("SELECT * FROM clg_fees_receipts WHERE enrollment_no = '$enrollment_no'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Full Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin_view_student.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="student-card">
    <h3 class="text-center mb-4">Student Full Profile</h3>
    <?php if ($student): ?>
        <div class="text-center mb-4">
            <img src="<?php echo htmlspecialchars($student['profile_photo']); ?>" class="profile-img">
            <div class="mt-3">
        <a href="edit_student.php?id=<?php echo $student_id; ?>" class="btn btn-primary btn-lg me-2">Edit</a>
        <a href="delete_student.php?id=<?php echo $student_id; ?>" class="btn btn-danger btn-lg" 
           onclick="return confirm('Are you sure you want to delete this student\'s entire data? This action cannot be undone!');">
           Delete Entire Data
        </a>
    </div>
        </div>
        <p><strong>ID:</strong> <?php echo htmlspecialchars($student['id']); ?></p>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
        <p><strong>Enrollment No:</strong> <?php echo htmlspecialchars($enrollment_no); ?></p>
        <p><strong>Division:</strong> <?php echo htmlspecialchars($division); ?></p>
        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($dob); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($age); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

        <h5 class="mt-4">Aadhaar Details 
            <?php if ($aadhaar): ?>
                <button class="btn btn-sm btn-danger float-end" onclick="deleteDoc('aadhaar', '<?php echo $enrollment_no; ?>')">Delete Aadhaar</button>
            <?php endif; ?>
        </h5>
        <?php if ($aadhaar): ?>
            <div class="doc-section">
            <img src="<?php echo $aadhaar['student_aadhar_image']; ?>" class="doc-img" onclick="showModal(this)">
                <img src="<?php echo $aadhaar['father_aadhar_image']; ?>" class="doc-img" onclick="showModal(this)">
                <img src="<?php echo $aadhaar['mother_aadhar_image']; ?>" class="doc-img" onclick="showModal(this)">
            <?php endif; ?>
        </div> 

        <h5 class="mt-4">School Marksheets 
            <?php if ($school_marks): ?>
                <button class="btn btn-sm btn-danger float-end" onclick="deleteDoc('school_marksheets', '<?php echo $enrollment_no; ?>')">Delete School Marksheets</button>
            <?php endif; ?>
        </h5>
        <div class="doc-section">
            <?php if ($school_marks): ?>
                <img src="<?php echo $school_marks['marksheet_10']; ?>" class="doc-img" onclick="showModal(this)">
                <img src="<?php echo $school_marks['marksheet_12']; ?>" class="doc-img" onclick="showModal(this)">
            <?php endif; ?>
        </div>

        <h5 class="mt-4">College Marksheets 
            <?php if ($college_marks->num_rows > 0): ?>
                <button class="btn btn-sm btn-danger float-end" onclick="deleteDoc('college_marksheets', '<?php echo $enrollment_no; ?>')">Delete Clg Marksheets</button>
            <?php endif; ?>
        </h5>
        <div class="doc-section">
            <?php while ($row = $college_marks->fetch_assoc()): ?>
                <img src="<?php echo $row['file_path']; ?>" class="doc-img" onclick="showModal(this)" title="Semester <?php echo $row['semester']; ?>">
            <?php endwhile; ?>
        </div>

        <h5 class="mt-4">College Fee Receipts 
            <?php if ($fees_receipts->num_rows > 0): ?>
                <button class="btn btn-sm btn-danger float-end" onclick="deleteDoc('fee_receipts', '<?php echo $enrollment_no; ?>')">Delete Clg Fee Receipts</button>
            <?php endif; ?>
        </h5>
        <div class="doc-section">
            <?php while ($row = $fees_receipts->fetch_assoc()): ?>
              
                <img src="<?php echo $row['file_path']; ?>" class="doc-img" onclick="showModal(this)" title="Semester <?php echo $row['semester']; ?>">
            <?php endwhile; ?>
        </div>

        <div class="text-center mt-4">
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">Student not found!</div>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
    <?php endif; ?>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid">
            </div>
        </div>
    </div>
</div>
<script>
       function showModal(img) {
        document.getElementById("modalImage").src = img.src;
        var myModal = new bootstrap.Modal(document.getElementById("imageModal"));
myModal.show();

    }
    function deleteDoc(type, enrollment_no) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the entire section.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `admin_delete_student.php?type=${type}&enrollment_no=${enrollment_no}`;
            }
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
