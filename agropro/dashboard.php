<?php
session_start();
if (!isset($_SESSION["helper_id"])) {
    header("Location: loginn.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = ""; // no password in xampp default
    $dbname = "smart_farming";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}
$conn = new mysqli("localhost", "root", "", "smart_farming");
$project = $_SESSION["project"];

$stmt = $conn->prepare("SELECT about FROM crops WHERE about = ?");
$stmt->bind_param("s", $project);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>
    <div class="form-container">
        <h2>Welcome, <?php echo $_SESSION["name"]; ?></h2>
        <h3>Your Project: <?php echo $project; ?></h3>
        <p><strong>Current Progress:</strong></p>
        <p><?php echo $row ? $row['progress'] : "No updates yet."; ?></p>
        <a href="logoutt.php"><button>Logout</button></a>
    </div>
</body>
</html>
