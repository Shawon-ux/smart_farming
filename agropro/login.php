<?php
session_start();  // Start session

// Only run if form was submitted
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

    // Get values from form
    $mb_num = $_POST['mb_num'];
    $id = $_POST['id'];

    // Check if user exists
    $sql = "SELECT * FROM farmer WHERE mb_num = '$mb_num' AND id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Login success
        $_SESSION['mb_num'] = $mb_num;
        echo "<script>alert('Login Successful!'); window.location.href='index.php';</script>";
    } else {
        // Login fail
        echo "<script>alert('Invalid Mobile Number or ID!'); window.location.href='login.php';</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Smart Farming</title>
  <link rel="stylesheet" href="loginstyle.css">
</head>
<body>

<div class="container">
  <h1>Welcome to Smart Farming</h1> <!-- Main Heading added -->
  
  <div class="login-container">
    <h2>Login</h2>
    <!-- Form that submits data to the same page (login.php) -->
    <form action="login.php" method="POST">
      <input type="text" placeholder="Mobile Number" name="mb_num" required>
      <input type="password" placeholder="Enter your Password" name="id" required>
      <button type="submit">Login</button>
    </form>
    <p class="register-link">
      Don't have an account? <a href="registration.php">Register here</a>
    </p>
  </div>
</div>

</body>
</html>
