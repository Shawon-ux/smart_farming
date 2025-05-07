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

// Handle market info submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_market"])) {
    $market_name = $_POST["market_name"];
    $address = $_POST["address"];

    $stmt = $conn->prepare("INSERT INTO market (name, address) VALUES (?, ?)");
    $stmt->bind_param("ss", $market_name, $address);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Crops and Market</title>
    <link rel="stylesheet" href="servstyle.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            padding: 40px;
            color: #333;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 28px;
        }

        form {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            max-width: 400px;
            margin-bottom: 30px;
        }

        form h3 {
            color: #34495e;
            margin-bottom: 15px;
        }

        form input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        form input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            border-collapse: collapse;
            width: 60%;
            background-color: #fff;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }

        th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

    <h1>Crops Information</h1>
    <?php
    $crops = $conn->query("SELECT name, c_id, type FROM crops");
    if ($crops->num_rows > 0) {
        while($row = $crops->fetch_assoc()) {
            echo "<p><strong>" . $row["name"] . "</strong>: " . $row["c_id"] . ", Type: " . $row["type"] . "</p>";
        }
    } else {
        echo "<p>No crop data found.</p>";
    }
    ?>

    <h1>Your Market Name</h1>

    <form method="POST">
        <h3>Add Market Info</h3>
        Market Name: <input type="text" name="market_name" required><br>
        Location: <input type="text" name="address" required><br>
        <input type="submit" name="add_market" value="Add Market Info">
    </form>

    <h1>Market Prices</h1>
    <?php
    $market = $conn->query("SELECT name, address FROM market");
    if ($market->num_rows > 0) {
        echo "<table><tr><th>Market Name</th><th>Location</th></tr>";
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
