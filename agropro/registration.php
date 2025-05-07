<?php

session_start();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    
    $servername = "localhost";
    $username = "root";
    $password = "";  
    $dbname = "smart_farming";

    
    $conn = new mysqli($servername, $username, $password, $dbname);

    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    $id = isset($_POST['id']) ? $_POST['id'] : '';  
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $mb_num = isset($_POST['mb_num']) ? $_POST['mb_num'] : '';
    $training_session = isset($_POST['training_session']) ? $_POST['training_session'] : '';

    
    if (empty($id) || empty($name) || empty($address) || empty($mb_num) || empty($training_session)) {
        echo "<script>alert('All fields are required!'); window.location.href='registration.php';</script>";
    } else {
        
        $sql = "INSERT INTO farmer (id, name, address, mb_num, training_session)
                VALUES ('$id', '$name', '$address', '$mb_num', '$training_session')";

        
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Smart Farming</title>
  <link rel="stylesheet" href="regisstyle.css">
</head>
<body>

<div class="container">
  <h1>Welcome to Smart Farming</h1> 

  <div class="register-container">
    <h2>Create Account</h2>
    <form action="registration.php" method="POST">
        <input type="text" placeholder="ID (Will be password)" name="id" required>
        <input type="text" placeholder="Name" name="name" required>
        <input type="text" placeholder="Address" name="address" required>
        <input type="text" placeholder="Mobile Number" name="mb_num" required>
        <input type="text" placeholder="Training Session" name="training_session" required>
        <button type="submit">Register</button>
    </form>
    <p class="login-link">
      Already have an account? <a href="login.php">Login here</a>
    </p>
  </div>
</div>

</body>
</html>
