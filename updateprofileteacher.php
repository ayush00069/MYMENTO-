<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'mymento';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if not logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: loginteacher.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$error_message = '';
$success_message = '';
$teacherData = null;

// Fetch existing data
$query = $conn->prepare("SELECT * FROM teacherprofile WHERE teacher_id = ?");
$query->bind_param("s", $teacher_id);
$query->execute();
$result = $query->get_result();
$teacherData = $result->fetch_assoc();
$query->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $mentoringclass = $_POST['mentoringclass'];
    $qualification = trim($_POST['qualification']);

    // Handle profile photo upload
    $profile_photo_path = $teacherData['profile_photo_path'] ?? null;
    if (!empty($_FILES['profile_photo']['name'])) {
        $profile_photo = $_FILES['profile_photo']['name'];
        $profile_photo_tmp = $_FILES['profile_photo']['tmp_name'];
        $profile_photo_path = 'uploads/' . uniqid() . '_' . $profile_photo;

        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['profile_photo']['type'], $allowed_types)) {
            $error_message = 'Only JPG, PNG, and GIF files are allowed.';
        } elseif (!move_uploaded_file($profile_photo_tmp, $profile_photo_path)) {
            $error_message = 'File upload failed.';
        }
    }

    if (!$error_message) {
        $sql = "INSERT INTO teacherprofile (teacher_id, full_name, mentoringclass, qualification, profile_photo_path) 
                VALUES (?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                full_name = VALUES(full_name), 
                mentoringclass = VALUES(mentoringclass), 
                qualification = VALUES(qualification), 
                profile_photo_path = VALUES(profile_photo_path)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('sssss', $teacher_id, $full_name, $mentoringclass, $qualification, $profile_photo_path);
            if ($stmt->execute()) {
                $success_message = "Profile updated successfully!";
                header("Refresh:2; url=final.php"); // Redirect after update
            } else {
                $error_message = "Error updating profile: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "SQL error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background: url('updateprofileteacher.png') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            animation: fadeInBackground 2s ease-in-out;
        }

        .header {
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-align: center;
            margin-bottom: 10px;
        }

        .menu-container {
            margin-bottom: 20px;
        }

        .back-btn {
            text-decoration: none;
            background: #ff4757;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        input, select, button {
            width: 90%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: block;
        }

        button {
            background: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #218838;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        .success-message {
            color: green;
            font-weight: bold;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector("form");
            form.addEventListener("submit", function () {
                document.querySelector(".loading").style.display = "block";
            });
        });
    </script>
</head>
<body>
    <div class="header">My Mento</div>

    <div class="menu-container">
        <a href="final.php" class="back-btn">⬅ Back</a>
    </div>

    <div class="form-container">
        <h2>Update Profile</h2>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php elseif ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="profile_photo">Profile Photo:</label>
            <input type="file" name="profile_photo" id="profile_photo" accept="image/*">

            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($teacherData['full_name'] ?? ''); ?>" required>

            <label for="mentoringclass">Mentoring Class:</label>
            <select name="mentoringclass" id="mentoringclass" required>
                <option value="6A">6A</option>
                <option value="6B">6B</option>
                <option value="6C">6C</option>
            </select>

            <label for="qualification">Qualification:</label>
            <input type="text" name="qualification" id="qualification" value="<?php echo htmlspecialchars($teacherData['qualification'] ?? ''); ?>" required>

            <button type="submit" name="update_profile">Save Changes</button>
        </form>
    </div>
</body>
</html>
