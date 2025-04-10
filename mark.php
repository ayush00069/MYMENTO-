<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mymento";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $marksheet_type = $_POST["marksheet_type"];
    $file_name = $_FILES["file"]["name"];
    $file_tmp = $_FILES["file"]["tmp_name"];
    
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
    }
    
    $file_destination = $upload_dir . basename($file_name);
    
    if (move_uploaded_file($file_tmp, $file_destination)) {
        $stmt = $conn->prepare("INSERT INTO marksheets (marksheet_type, file_name, file_path, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $marksheet_type, $file_name, $file_destination);
        
        if ($stmt->execute()) {
            $message = "File uploaded successfully.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $message = "Failed to upload file.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Marksheet Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(106, 163, 171);
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .profile {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: url('mohit.png') center/cover;
            border: 3px solid #333;
            cursor: pointer;
        }
        .container {
            margin-top: 300px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }
        .btn {
            background-color:rgb(85, 93, 102);
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin: 10px;
        }
        .popup, .message-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        .popup select, .popup input {
            margin: 10px 0;
            display: block;
        }
        .message {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="profile" onclick="showProfileInfo()"></div>
    <div id="profileInfo" class="popup" style="display: none;">
        <h3>Student Profile</h3>
        <p>Name: John Doe</p>
        <p>Email: johndoe@example.com</p>
        <p>Course: Computer Science</p>
        <button class="btn" onclick="closeProfile()">Close</button>
    </div>

    <div class="container">
        <button class="btn" onclick="openPopup()">Marksheet</button>
    </div>
    <div class="container">
        <button class="btn" onclick="openPopupp()">Aadharcard</button>
    </div>

    <div id="popup" class="popup">
        <h3>Upload Your Marksheet</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <select name="marksheet_type" required>
                <option value="12th">12th Marksheet</option>
                <option value="1st">1st Semester Marksheet</option>
                <option value="2nd">2nd Semester Marksheet</option>
                <option value="3rd">3rd Semester Marksheet</option>
                <option value="4th">4th Semester Marksheet</option>
                <option value="5th">5th Semester Marksheet</option>
                <option value="6th">6th Semester Marksheet</option>
            </select>
            <input type="file" name="file" required>
            <button type="submit" class="btn">Upload</button>
        </form>
        <button class="btn" onclick="closePopup()">Close</button>
    </div>

    <div id="messagePopup" class="message-popup">
        <p id="messageText"></p>
        <button class="btn" onclick="closeMessagePopup()">OK</button>
    </div>

    <script>
        function openPopup() {
            document.getElementById("popup").style.display = "block";
        }
        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }
        function showProfileInfo() {
            document.getElementById("profileInfo").style.display = "block";
        }
        function closeProfile() {
            document.getElementById("profileInfo").style.display = "none";
        }
        function showMessagePopup(message) {
            document.getElementById("messageText").innerText = message;
            document.getElementById("messagePopup").style.display = "block";
        }
        function closeMessagePopup() {
            document.getElementById("messagePopup").style.display = "none";
        }

        <?php if (!empty($message)) { ?>
            showMessagePopup("<?php echo $message; ?>");
        <?php } ?>
    </script>
     <script>
        function openPopupp() {
            document.getElementById("popup").style.display = "block";
        }
        function closePopupp() {
            document.getElementById("popup").style.display = "none";
        }
        function showProfileInfo() {
            document.getElementById("profileInfo").style.display = "block";
        }
        function closeProfile() {
            document.getElementById("profileInfo").style.display = "none";
        }
        function showMessagePopupp(message) {
            document.getElementById("messageText").innerText = message;
            document.getElementById("messagePopup").style.display = "block";
        }
        function closeMessagePopup() {
            document.getElementById("messagePopup").style.display = "none";
        }

        <?php if (!empty($message)) { ?>
            showMessagePopup("<?php echo $message; ?>");
        <?php } ?>
    </script>
</body>
</html>
