<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smart_farming";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Trainers and Farming Helpers</title>
    <link rel="stylesheet" href="servstyle.css">
</head>
<body>
    <h1>Trainers</h1>
    <?php
    $trainer = $conn->query("SELECT name, address,contact,trainer_id,id,trainning_session FROM trainer");
    if ($trainer->num_rows > 0) {
        while($row = $trainer->fetch_assoc()) {
            echo "<p><strong>" . $row["name"] . "</strong>: " . $row["address"] . ", Contact: " . $row["contact"] . ", Trainer ID: " . $row["trainer_id"] . ", ID: " . $row["id"] . ", Training Session: " . $row["trainning_session"] . "</p>";
        }
    } else {
        echo "<p>No trainers found.</p>";
    }
    ?>

    <h1>Farming Helpers</h1>
    <?php
    $helper = $conn->query("SELECT name,phone_number,address,h_id  FROM helper");
    if ($helper->num_rows > 0) {
        while($row = $helper->fetch_assoc()) {
            echo "<p><strong>" . $row["name"] . "</strong>: " . $row["phone_number"] . ", Address: " . $row["address"] . ", ID: " . $row["h_id"] . "</p>";
        }
    } else {
        echo "<p>No helpers found.</p>";
    }

    $conn->close();
    ?>
</body>
</html>
