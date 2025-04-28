<?php
// Start the session
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Database connection settings
    $servername = "localhost";
    $username = "root";
    $password = "";  // Default password for XAMPP
    $dbname = "smart_farming";

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data, check if set to avoid undefined index warnings
    $id = isset($_POST['id']) ? $_POST['id'] : '';  // User's ID entered manually
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $mb_num = isset($_POST['mb_num']) ? $_POST['mb_num'] : '';
    $training_session = isset($_POST['training_session']) ? $_POST['training_session'] : '';

    // Check if any required field is empty
    if (empty($id) || empty($name) || empty($address) || empty($mb_num) || empty($training_session)) {
        echo "<script>alert('All fields are required!'); window.location.href='registration.php';</script>";
    } else {
        // Insert the data into the farmer table
        $sql = "INSERT INTO farmer (id, name, address, mb_num, training_session)
                VALUES ('$id', '$name', '$address', '$mb_num', '$training_session')";

        // Execute the query and check for success
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Close the database connection
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
  <h1>Welcome to Smart Farming</h1> <!-- ✨ Main Heading added -->

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
