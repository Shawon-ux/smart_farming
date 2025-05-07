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
<style>
  * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: 'Poppins', sans-serif;
  background: 
  linear-gradient(rgba(227, 227, 227, 0.171), rgba(5, 96, 5, 0.275)),
      url('https://www.symmetryelectronics.com/getmedia/5ddf849b-de2f-42f5-99c6-24ee59a98b22/iStock-1429073633.jpg') center/cover no-repeat;
  position: relative;
  overflow: hidden;
}

/* Nature-inspired overlay */
body::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(240, 248, 242, 0.4); /* Soft greenish-white tint */
  z-index: 0;
}

.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  width: 100%;
  padding: 20px;
  position: relative;
  z-index: 1;
}

.container h1 {
  border-radius: 18px;
  
  font-size: 2.5rem;
  color: #0a4a1b; /* Deep natural green */
  margin-bottom: 1.5rem;
  text-shadow: 1px 1px 2px rgba(255,255,255,0.5);
  font-weight: 600;
  letter-spacing: 0.5px;
}

.login-container {
  background: rgba(255, 255, 255, 0.514); /* More transparent */
  backdrop-filter: blur(2px); /* Slightly stronger blur */
  border: 1px solid rgba(255, 255, 255, 0.192); /* Softer border */
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  padding: 30px;
  border-radius: 15px;
}


.login-container h2 {
  margin-bottom: 1.8rem;
  font-size: 1.8rem;
  color: #3a5a40; /* Earthy green */
  font-weight: 600;
}

.login-container form input {
  width: 100%;
  padding: 15px 18px;
  margin: 0.8rem 0;
  border: 1px solid #d1e3d4; /* Light green border */
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s;
  background: rgba(255,255,255,0.9);
}

.login-container form input:focus {
  outline: none;
  border-color: #4a8c6d; /* Medium green */
  box-shadow: 0 0 0 4px rgba(74, 140, 109, 0.15);
  background: white;
}

.login-container form button {
  width: 100%;
  padding: 16px;
  background: linear-gradient(135deg, #4a8c6d, #3a6b53); /* Natural green gradient */
  border: none;
  border-radius: 10px;
  color: white;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  margin-top: 1.2rem;
  transition: all 0.3s;
  box-shadow: 0 5px 15px rgba(58, 107, 83, 0.3);
}

.login-container form button:hover {
  background: linear-gradient(135deg, #3a6b53, #2a4f3c); /* Darker green */
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(58, 107, 83, 0.4);
}

.register-link {
  margin-top: 1.8rem;
  font-size: 0.95rem;
  color: #5a6d5a; /* Muted green-gray */
}

.register-link a {
  color: #4a8c6d; /* Matching button color */
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s;
  position: relative;
}

.register-link a:hover {
  color: #3a6b53; /* Darker shade */
}

.register-link a::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 2px;
  background: #3a6b53;
  transition: width 0.3s;
}

.register-link a:hover::after {
  width: 100%;
}

/* Nature-themed decorative elements */
.decorative-circle {
  position: absolute;
  border-radius: 50%;
  z-index: 0;
  opacity: 0.6;
}

.circle-1 {
  width: 250px;
  height: 250px;
  top: -50px;
  left: -50px;
  background: radial-gradient(circle, rgba(164, 209, 178, 0.4) 0%, rgba(164, 209, 178, 0) 70%);
}

.circle-2 {
  width: 350px;
  height: 350px;
  bottom: -100px;
  right: -100px;
  background: radial-gradient(circle, rgba(116, 166, 131, 0.3) 0%, rgba(116, 166, 131, 0) 70%);
}

/* Leaf-like animation */
@keyframes float-leaf {
  0%, 100% { transform: translateY(0) rotate(-2deg); }
  50% { transform: translateY(-15px) rotate(2deg); }
}

</style>


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
