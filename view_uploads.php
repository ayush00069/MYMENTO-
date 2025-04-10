<?php
$conn = new mysqli("localhost", "root", "", "your_database_name");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM uploaded_files");

echo "<h2>Uploaded Files</h2>";
while ($row = $result->fetch_assoc()) {
    echo "<p>{$row['file_name']} - <a href='{$row['file_path']}' target='_blank'>View</a></p>";
}

$conn->close();
?>
