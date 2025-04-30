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
    <title>Smart Farming Search</title>
    <link rel="stylesheet" href="searchstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="container">
    <h1>Smart Farming Search</h1>

    <form action="search.php" method="GET">
        <input type="text" name="query" placeholder="Search crops, markets, equipment..." required>
        <button type="submit"><i class="fa fa-search"></i> Search</button>
    </form>

    <?php
    if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
        $query = trim($_GET['query']);
        $query = $conn->real_escape_string($query);

        echo "<h2>Results for: <em>$query</em></h2>";

        function printResults($result, $title) {
            if ($result->num_rows > 0) {
                echo "<h3>$title</h3>";
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='service-card'>";
                    foreach ($row as $key => $value) {
                        echo "<strong>$key:</strong> $value<br>";
                    }
                    echo "</div>";
                }
            }
        }

        $trainer = $conn->query("SELECT name, trainer_id FROM trainer WHERE name LIKE '%$query%' OR trainer_id LIKE '%$query%'");
        printResults($trainer, "trainer");
        
        
        $helper = $conn->query("SELECT name, address FROM helper WHERE name LIKE '%$query%' OR address LIKE '%$query%'");
        printResults($helper, "helper");
        
        
        $lands = $conn->query("SELECT location, soil_type FROM lands WHERE location LIKE '%$query%' OR soil_type LIKE '%$query%'");
        printResults($lands, "lands");
        
        
        $weather = $conn->query("SELECT season, temperature FROM weather WHERE season LIKE '%$query%' OR temperature LIKE '%$query%'");
        printResults($weather, "weather");
        
        
        $equipments = $conn->query("SELECT name FROM equipments WHERE name LIKE '%$query%'");
        printResults($equipments, "equipments");
        
        
        $fertilizer = $conn->query("SELECT name, type FROM fertilizer WHERE name LIKE '%$query%' OR type LIKE '%$query%'");
        printResults($fertilizer, "fertilizer");
        
        
        $pesticides = $conn->query("SELECT name, type FROM pesticides WHERE name LIKE '%$query%' OR type LIKE '%$query%'");
        printResults($pesticides, "pesticides");
        
        
        $crops = $conn->query("SELECT name FROM crops WHERE name LIKE '%$query%'");
        printResults($crops, "crops");
        
        
        $market = $conn->query("SELECT address FROM market WHERE address LIKE '%$query%'");
        printResults($market, "market");

    } elseif (isset($_GET['query'])) {
        echo "<p>Please enter a search term.</p>";
    }
    ?>

</div>

</body>
</html>
