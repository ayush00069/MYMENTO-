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

// Upload Marksheet
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $marksheet_type = $_POST["marksheet_type"];
    $file_name = $_FILES["file"]["name"];
    $file_tmp = $_FILES["file"]["tmp_name"];
    
    $upload_dir = "uploads/marksheets/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

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

// Upload Aadhar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["aadhar_file"])) {
    $aadhar_type = $_POST["aadhar_type"];
    $aadhar_file_name = $_FILES["aadhar_file"]["name"];
    $aadhar_file_tmp = $_FILES["aadhar_file"]["tmp_name"];
    
    $upload_dir_aadhar = "uploads/aadhar/";
    if (!is_dir($upload_dir_aadhar)) {
        mkdir($upload_dir_aadhar, 0777, true);
    }

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
        .message-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 3px solid #333;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        .message-popup p {
            font-size: 16px;
            font-weight: bold;
            color: #333;
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
    </style>
</head>
<body>

    <!-- Popup Message -->
    <div id="messagePopup" class="message-popup">
        <p id="messageText"><?= $message ?></p>
        <button class="btn" onclick="closeMessagePopup()">OK</button>
    </div>

    <script>
        function showMessagePopup(message) {
            document.getElementById("messageText").innerText = message;
            document.getElementById("messagePopup").style.display = "block";
        }

        function closeMessagePopup() {
            document.getElementById("messagePopup").style.display = "none";
        }

        // Show the message if it exists
        <?php if (!empty($message)) { ?>
            showMessagePopup("<?php echo $message; ?>");
        <?php } ?>
    </script>

</body>
</html>
