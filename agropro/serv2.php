<?php

include 'db.php';


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Equipments, Fertilizers and Pesticides</title>
    <link rel="stylesheet" href="servstyle.css">
</head>
<body>
    <h1>Equipments</h1>
    <?php
    $equipments = $conn->query("SELECT name, type,model_number FROM equipments");
    if ($equipments->num_rows > 0) {
        while($row = $equipments->fetch_assoc()) {
            echo "<p>" . $row["name"] . " - " . $row["type"] . " - " . $row["model_number"] . "</p>";
        }
    } else {
        echo "<p>No equipments found.</p>";
    }
    ?>

    <h1>Fertilizers</h1>
    <?php
    $fertilizer = $conn->query("SELECT name,`usage`, type, nflag,pflag,poflag FROM fertilizer");
    if ($fertilizer->num_rows > 0) {
        while($row = $fertilizer->fetch_assoc()) {
            echo "<p>" . $row["name"] . " - " . $row["usage"] . $row["type"] . " - " . $row["nflag"] . $row["pflag"] . " - " . $row["poflag"] . "</p>";
        }
    } else {
        echo "<p>No fertilizers found.</p>";
    }
    ?>

    <h1>Pesticides</h1>
    <?php
    $pesticides = $conn->query("SELECT name, type, expired_date FROM pesticides");
    if ($pesticides->num_rows > 0) {
        while($row = $pesticides->fetch_assoc()) {
            echo "<p>" . $row["name"] . " - " . $row["type"] . " - " . $row["expired_date"] . "</p>";
        }
    } else {
        echo "<p>No pesticides found.</p>";
    }

    $conn->close();
    ?>
</body>
</html>

