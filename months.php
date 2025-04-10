<?php
// Google Sheets link
$googleSheetLink = "https://docs.google.com/spreadsheets/d/1QjRWjg-96S1b-hq6YG63D48yPgguVPerpjL0OoWvTEI/edit?gid=747656545#gid=747656545";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Months Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 50px;
        }
        .month-block {
            width: 150px;
            height: 100px;
            background-color: #4CAF50;
            color: white;
            margin: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
        }
        .month-block:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h2>Select a Month</h2>
<div class="container">
    <?php
    $months = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    
    foreach ($months as $month) {
        echo "<a href='$googleSheetLink' target='_blank' class='month-block'>$month</a>";
    }
    ?>
</div>

</body>
</html>
