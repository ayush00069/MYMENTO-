<?php 
session_start();
include "db_connect.php"; // Ensure database connection is included

if (!isset($_SESSION["username"])) {  
    echo "<script>alert('Unauthorized access!'); window.location.href='loginadmin.php';</script>";
    exit();
}

// Fetch teacher data
$sql_teachers = "SELECT * FROM teacherprofile";
$result_teachers = $conn->query($sql_teachers);

// Fetch student data
$sql_students = "SELECT * FROM student_profile";
$result_students = $conn->query($sql_students);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
       body {
        background: url('adminphoto.png') no-repeat center center fixed;
        background-size: cover;
        font-family: Arial, sans-serif;
    }
    
    .container {
        margin-top: 50px;
        background: rgba(255, 255, 255, 0.8); /* Adding a slight white overlay for readability */
        padding: 20px;
        border-radius: 10px;
    }
        .card {
            border-radius: 10px;
            background: linear-gradient(to right, #007bff, #00c6ff);
            color: white;
            padding: 20px;
            text-align: center;
        }
    
        

        .btn-custom {
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }
        .dropdown-menu {
            min-width: 120px;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #a71d2a;
        }
        .table-container {
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: none;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow">
        <h2>Welcome, Admin <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>!</h2>
        <div class="mt-3">
            <a href="loginadmin.php" class="btn btn-danger btn-custom">Logout</a>
        </div>
    </div>

    <div class="text-center mt-4">
        <button id="toggleTeachers" class="btn btn-primary btn-custom">Show Teachers</button>
       <!-- Add Users Button (Triggers Modal) -->
       <button id="openAddUserModal" class="btn btn-info btn-custom" data-bs-toggle="modal" data-bs-target="#addUserModal">
       Add Users
</button>


<!-- Modal Structure -->
<div id="addUserModal" class="modal fade" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="addUserModalLabel">Add a New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>Select the type of user you want to add:</p>
                <div class="d-flex justify-content-around">
                    <a href="add_teacher.php" class="btn btn-primary btn-lg">Add Teacher</a>
                    <a href="add_student.php" class="btn btn-success btn-lg">Add Student</a>
                </div>
            </div>
        </div>
    </div>
</div>

        <button id="toggleStudents" class="btn btn-success btn-custom">Show Students</button>
    </div>

    <div id="welcomeMessage" class="text-center mt-4">
        <h3>My Mento welcomes you, Boss</h3>
    </div>

    <!-- Teacher Table -->
    <div id="teacherTable" class="table-container">
        <h3 class="mt-4">Teacher Data</h3>
        <table id="teacherData" class="table table-bordered table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Mentoring Class</th>
                    <th>Qualification</th>
                    <th>Profile Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result_teachers->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['teacher_id']}</td>
                            <td>{$row['full_name']}</td>
                            <td>{$row['mentoringclass']}</td>
                            <td>{$row['qualification']}</td>
                            <td><img src='{$row['profile_photo_path']}' alt='Profile Photo' width='50'></td>
                            <td>
                                        <button class='btn btn-warning btn-sm edit-teacher' data-id='{$row['teacher_id']}'>Edit</button>
                                <button class='btn btn-danger btn-sm delete-teacher' data-id='{$row['teacher_id']}'>Delete</button>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Student Table -->
    <div id="studentTable" class="table-container">
        <h3 class="mt-4">Student Data</h3>
        <table id="studentData" class="table table-bordered table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Enrollment Number</th>
                    <th>Division</th>
                    <th>Date of Birth</th>
                    <th>Age</th>
                    <th>Profile Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result_students->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['full_name']}</td>
                            <td>{$row['enrollment_no']}</td>
                            <td>{$row['division']}</td>
                            <td>{$row['dob']}</td>
                            <td>{$row['age']}</td>
                            <td><img src='{$row['profile_photo']}' alt='Profile Photo' width='50'></td>
                            <td>
                              <a href='admin_view_student.php?id={$row['id']}' class='btn btn-info btn-sm'>Show More</a>
                   
                            </td>
                          </tr>";
                }
                ?>

            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#teacherData, #studentData').DataTable();

        $("#toggleTeachers").click(function() {
            $("#teacherTable").toggle();
            $("#welcomeMessage").toggle();
        });

        $("#toggleStudents").click(function() {
            $("#studentTable").toggle();
            $("#welcomeMessage").toggle();
        });


        // Delete Teacher
        $(".delete-teacher").click(function() {
            let teacherId = $(this).data("id");
            Swal.fire({
                title: "Are you sure?",
                text: "This record will be deleted permanently!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "delete_teacher.php?id=" + teacherId;
                }
            });
        });

        // Edit Teacher
        $(".edit-teacher").click(function() {
            let teacherId = $(this).data("id");
            window.location.href = "edit_teacher.php?id=" + teacherId;
        });

        // Delete Student (Fixed variable name)
        $(".delete-student").click(function() {
            let studentId = $(this).data("id");  // FIXED: Corrected variable name
            Swal.fire({
                title: "Are you sure?",
                text: "This record will be deleted permanently!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "delete_student.php?id=" + studentId; // FIXED: Corrected variable reference
                }
            });
        });

        // Edit Student (Fixed variable name)
        $(".edit-student").click(function() {
            let studentId = $(this).data("id");  // FIXED: Corrected variable name
            window.location.href = "edit_student.php?id=" + studentId; // FIXED: Corrected variable reference
        });

    });
</script>


</body>
</html>
<?php
$conn->close();
?>
