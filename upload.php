<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "mymento"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uploadDir = "uploads/"; // Folder to store uploaded files
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_FILES as $key => $file) {
        if ($file["error"] == 0) {
            $fileName = basename($file["name"]);
            $filePath = $uploadDir . $fileName;
            $fileType = explode('_', $key)[0]; // Extract type from input name (e.g., 'aadhar' from 'aadhar_student')

            if (move_uploaded_file($file["tmp_name"], $filePath)) {
                $stmt = $conn->prepare("INSERT INTO uploaded_files (file_type, file_name, file_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $fileType, $fileName, $filePath);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    echo "Files uploaded successfully!";
}

$conn->close();
?>
