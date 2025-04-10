<?php
$uploadDir = "uploads/";

// Check if directory exists, if not create it
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!empty($_FILES['marksheet']['name'][0])) {
    foreach ($_FILES['marksheet']['name'] as $key => $fileName) {
        if (!empty($fileName)) {
            $fileTmpName = $_FILES['marksheet']['tmp_name'][$key];
            $destination = $uploadDir . basename($fileName);

            if (move_uploaded_file($fileTmpName, $destination)) {
                echo "File $fileName uploaded successfully!<br>";
            } else {
                echo "Error uploading $fileName.<br>";
            }
        }
    }
} else {
    echo "No file selected!";
}
?>
