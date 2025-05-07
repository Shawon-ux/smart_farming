<?php
// Form processing logic
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "smart_farming"; // <-- Change this to your actual DB name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $error = "Connection failed: " . $conn->connect_error;
    } else {
        // Get form values
        $name = $_POST['name'];
        $phone_number = $_POST['phone_number'];
        $address = $_POST['address'];
        $h_id = $_POST['h_id'];
        $project_name = $_POST['project_name'];

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO helper (name, phone_number, address, h_id, project_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $phone_number, $address, $h_id, $project_name);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Helper Signup</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f6fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: white;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 300px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    h2 {
      margin-bottom: 15px;
      text-align: center;
    }

    label {
      margin-top: 10px;
      font-weight: bold;
    }

    input {
      padding: 8px;
      margin-top: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button {
      margin-top: 20px;
      padding: 10px;
      background-color: #2ecc71;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .message {
      margin-top: 15px;
      text-align: center;
      font-weight: bold;
    }

    .success {
      color: green;
    }

    .error {
      color: red;
    }
  </style>
</head>
<body>
  <div class="container">
    <form method="POST" action="">
      <h2>Helper Sign-Up</h2>

      <label for="name">Name</label>
      <input type="text" name="name" id="name" required />

      <label for="phone">Phone Number</label>
      <input type="text" name="phone_number" id="phone" required />

      <label for="address">Address</label>
      <input type="text" name="address" id="address" required />

      <label for="h_id">H_ID</label>
      <input type="text" name="h_id" id="h_id" required />

      <label for="project_name">Project Name</label>
      <input type="text" name="project_name" id="project_name" required />

      <button type="submit">Sign Up</button>

      <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
      <?php elseif ($error): ?>
        <div class="message error"><?= $error ?></div>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
