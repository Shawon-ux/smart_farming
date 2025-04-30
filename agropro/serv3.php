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
    <title>Crops and Market</title>
    <link rel="stylesheet" href="servstyle.css">
</head>
<body>
    <h1>Crops Information</h1>
    <?php
    $crops = $conn->query("SELECT name, c_id,type FROM crops");
    if ($crops->num_rows > 0) {
        while($row = $crops->fetch_assoc()) {
            echo "<p><strong>" . $row["name"] . "</strong>: " . $row["c_id"] . ", Type: " . $row["type"] . "</p>";
        }
    } else {
        echo "<p>No crop data found.</p>";
    }
    ?>

    <h1>Market Prices</h1>
    <?php
    $market = $conn->query("SELECT name, address FROM market");
    if ($market->num_rows > 0) {
        echo "<table border='1'><tr><th>Crop</th><th>Price</th><th>Location</th></tr>";
        while($row = $market->fetch_assoc()) {
            echo "<tr><td>" . $row["name"] . "</td><td>" . $row["address"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No market data found.</p>";
    }

    $conn->close();
    ?>
</body>
</html>

