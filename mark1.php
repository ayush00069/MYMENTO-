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
$marksheet_preview = "";
$aadhar_preview = "";
$marksheet_images = [];
$aadhar_images = [];

// Fetch all uploaded marksheet images
$result_marksheets = $conn->query("SELECT * FROM marksheets");
if ($result_marksheets->num_rows > 0) {
    while ($row = $result_marksheets->fetch_assoc()) {
        $marksheet_images[] = $row;
    }
}

// Fetch all uploaded aadhar images
$result_aadhar = $conn->query("SELECT * FROM aadhar_cards");
if ($result_aadhar->num_rows > 0) {
    while ($row = $result_aadhar->fetch_assoc()) {
        $aadhar_images[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $marksheet_type = $_POST["marksheet_type"];
    $file_name = $_FILES["file"]["name"];
    $file_tmp = $_FILES["file"]["tmp_name"];
    
    $upload_dir = "uploads/marksheets/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
    }

    // Check if the file already exists in the marksheet folder
    if (file_exists($upload_dir . $file_name)) {
        $message = "This marksheet image already exists!";
    } else {
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
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["aadhar_file"])) {
    $aadhar_type = $_POST["aadhar_type"];
    $aadhar_file_name = $_FILES["aadhar_file"]["name"];
    $aadhar_file_tmp = $_FILES["aadhar_file"]["tmp_name"];
    
    $upload_dir_aadhar = "uploads/aadhar/";
    if (!is_dir($upload_dir_aadhar)) {
        mkdir($upload_dir_aadhar, 0777, true); // Create the directory if it doesn't exist
    }
    
    // Check if the Aadhar file already exists in the aadhar folder
    if (file_exists($upload_dir_aadhar . $aadhar_file_name)) {
        $message = "This Aadhar card image already exists!";
    } else {
        $aadhar_file_destination = $upload_dir_aadhar . basename($aadhar_file_name);
    
        if (move_uploaded_file($aadhar_file_tmp, $aadhar_file_destination)) {
            $stmt = $conn->prepare("INSERT INTO aadhar_cards (aadhar_type, file_name, file_path, uploaded_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $aadhar_type, $aadhar_file_name, $aadhar_file_destination);
            
            if ($stmt->execute()) {
                $message = "Aadhar card uploaded successfully.";
            } else {
                $message = "Error: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $message = "Failed to upload Aadhar card.";
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
    <title>Student Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(106, 163, 171);
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
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
            border: 3px solid #333;
        }
        .btn {
            background-color: rgb(29, 126, 236);
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin: 10px;
        }
        .popup, .message-popup, .aadhar-popup {
            display: none;
            position: fixed;
            top: 70%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 3px solid #333;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        .popup select, .popup input {
            margin: 10px 0;
            display: block;
        }
        .message {
            color: green;
            margin-top: 10px;
            border: 3px solid #333;
        }
        .aadhar-image, .marksheet-image {
            width: 100px;
            height: 150px;
            object-fit: cover;
            margin: 10px;
            border: 3px solid #333;
        }
        .maximized-img {
            width: 10%;
            height: 30%;
            object-fit: contain;
            display: none;
            border: 3px solid #333;
            margin-top: 20px;
        }
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .image-item {
            margin: 10px;
            text-align: center;
        }
        .gallery-section {
            margin-top: 30px;
        }
        .gallery-section h2 {
            text-align: center;
            color: #2a2a2a;
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
        <button class="btn" onclick="openAadharPopup()">Aadhar Card</button>
    </div>

    <!-- Marksheet Upload -->
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

    <!-- Aadhar Upload -->
    <div id="aadharPopup" class="aadhar-popup">
        <h3>Upload Aadhar Card</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <select name="aadhar_type" required>
                <option value="your">Your Aadhar Card</option>
                <option value="mother">Mother's Aadhar Card</option>
                <option value="father">Father's Aadhar Card</option>
            </select>
            <input type="file" name="aadhar_file" required>
            <button type="submit" class="btn">Upload</button>
        </form>
        <button class="btn" onclick="closeAadharPopup()">Close</button>
    </div>

    <div id="messagePopup" class="message-popup">
        <p id="messageText"><?= $message ?></p>
        <button class="btn" onclick="closeMessagePopup()">OK</button>
    </div>

    <!-- Display Uploaded Images -->
    <div class="gallery-section">
        <!-- Marksheet Section -->
        <h2>Uploaded Marksheet Images</h2>
        <div class="image-gallery">
            <?php foreach ($marksheet_images as $marksheet) { ?>
                <div class="image-item">
                    <h4>Title: <?= $marksheet['marksheet_type'] ?></h4>
                    <img class="marksheet-image" src="<?= $marksheet['file_path'] ?>" alt="Marksheet Image" onclick="maximizeImage('<?= $marksheet['file_path'] ?>')" />
                </div>
            <?php } ?>
        </div>

        <!-- Aadhar Card Section -->
        <h2>Uploaded Aadhar Card Images</h2>
        <div class="image-gallery">
            <?php foreach ($aadhar_images as $aadhar_cards) { ?>
                <div class="image-item">
                    <h4>Title: <?= $aadhar_cards['aadhar_type'] ?></h4>
                    <img class="aadhar_cards-image" src="<?= $aadhar_cards['file_path'] ?>" alt="Aadhar Image" onclick="maximizeImage('<?= $aadhar_cards['file_path'] ?>')" />
                </div>
            <?php } ?>
        </div>
    </div>

    <img id="maximizedImage" class="maximized-img" src="" alt="Maximized Image" onclick="closeMaximizedImage()" />

    <script>
        <?php if (!empty($message)) { ?>
            showMessagePopup("<?php echo $message; ?>");
        <?php } ?>
        function openPopup() {
            document.getElementById("popup").style.display = "block";
            document.getElementById("aadharPopup").style.display = "none"; // Close Aadhar
        }

        function openAadharPopup() {
            document.getElementById("aadharPopup").style.display = "block";
            document.getElementById("popup").style.display = "none"; // Close Marksheet
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }

        function closeAadharPopup() {
            document.getElementById("aadharPopup").style.display = "none";
        }

        function maximizeImage(src) {
            document.getElementById("maximizedImage").src = src;
            document.getElementById("maximizedImage").style.display = "block";
        }

        function closeMaximizedImage() {
            document.getElementById("maximizedImage").style.display = "none";
        }

        function closeMessagePopup() {
            document.getElementById("messagePopup").style.display = "none";
        }
    </script>
</body>
</html>