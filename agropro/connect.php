<?php
$servername = "localhost";  // or 127.0.0.1
$username = "root";         // default XAMPP user
$password = "";             // default XAMPP password is empty
$database = "smart_farming"; // replace with your actual DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "✅ Connection successful!<br><br>";

// Show tables
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "📋 Tables in the database:<br>";
    while($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
} else {
    echo "No tables found in the database.";
}

$conn->close();
?>
