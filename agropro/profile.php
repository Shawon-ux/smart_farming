<?php
session_start(); 


if (!isset($_SESSION['mb_num'])) {
    
    header('Location: login.php');
    exit();
}


include 'db.php';


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mb_num = $_SESSION['mb_num']; 

$sql = "SELECT * FROM farmer WHERE mb_num = '$mb_num'";
$result = $conn->query($sql);

$userData = [];

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userData = [
        "name" => $row['name'],
        "serialno" => $row['serialno'],
        "mb_num" => $row['mb_num'],
        "training_session" => $row['training_session'],
        "address" => $row['address']
    ];
} else {
    $userData = ["error" => "User not found"];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">Smart Farming</div>
        <ul class="navbar-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="#">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="profile-container">
        <h1>My Profile</h1>
        <div class="profile-card">
            <p><strong>Name:</strong> <span id="name"></span></p>
            <p><strong>Farmer_ID:</strong> <span id="serialno"></span></p>
            <p><strong>Mobile Number:</strong> <span id="mb_num"></span></p>
            <p><strong>Training Session:</strong> <span id="training_session"></span></p>
            <p><strong>Address:</strong> <span id="address"></span></p>
        </div>
    </div>

    
    <script>
        const userData = <?php echo json_encode($userData); ?>;
        
        if (userData.error) {
            console.error('Error from server:', userData.error);
            alert('Error: ' + userData.error);
        } else {
            document.getElementById('name').innerText = userData.name;
            document.getElementById('serialno').innerText = userData.serialno;
            document.getElementById('mb_num').innerText = userData.mb_num;
            document.getElementById('training_session').innerText = userData.training_session;
            document.getElementById('address').innerText = userData.address;
        }
    </script>

</body>
</html>
