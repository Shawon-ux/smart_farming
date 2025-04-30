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
    <title>Lands and Weather</title>
    <link rel="stylesheet" href="servstyle.css">
</head>
<body>
    <h1>Available Lands</h1>
    <?php
    $lands = $conn->query("SELECT l_id,location,soil_type,size FROM lands");
    if ($lands->num_rows > 0) {
        while($row = $lands->fetch_assoc()) {
            echo "<p>" . $row["l_id"] . ": " . $row["location"] . ", Soil Type: " . $row["soil_type"] . " - " . $row["size"] . " acres</p>";
        }
    } else {
        echo "<p>No land data found.</p>";
    }
    ?>

    <h1>Weather Forecast</h1>
    <?php
    $weather = $conn->query("SELECT season,humidity,temperature FROM weather");
    if ($weather->num_rows > 0) {
        while($row = $weather->fetch_assoc()) {
            echo "<p>" . $row["season"] . ", Humidity: " . $row["humidity"] .  " (" . $row["temperature"] . "°C)</p>";
        }
    } else {
        echo "<p>No weather data found.</p>";
    }

    $conn->close();
    ?>
</body>
</html>

