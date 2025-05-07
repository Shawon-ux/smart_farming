<?php
$servername = "localhost"; 
$username = "root";        
$password = "";            
$database = "smart_farming"; 


$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "✅ Connection successful!<br><br>";


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
