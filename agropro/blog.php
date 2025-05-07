<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $userMsg = strtolower(trim($_POST['message']));
    $botResponse = "Sorry, I couldn't find any info for that.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "smart_farming";

   $conn = new mysqli($servername, $username, $password, $dbname);
   if ($conn->connect_error) {
      echo "Database connection failed!";
      exit;
   }
  
   if (strpos($userMsg, 'market') !== false) {
        $sql = "SELECT name, address FROM market LIMIT 3";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $botResponse = "Here are some markets:\n";
            while ($row = $result->fetch_assoc()) {
                $botResponse .= "- {$row['name']} at {$row['address']}\n";
            }
        }
    }


    elseif (strpos($userMsg, 'helper') !== false || strpos($userMsg, 'labour') !== false) {
        $sql = "SELECT name, phone_number FROM helper LIMIT 3";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $botResponse = "Available helpers:\n";
            while ($row = $result->fetch_assoc()) {
                $botResponse .= "- {$row['name']} (Phone: {$row['phone_number']})\n";
            }
        }
    }


    elseif (strpos($userMsg, 'weather') !== false || strpos($userMsg, 'season') !== false) {
        $sql = "SELECT season, temperature FROM weather LIMIT 3";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $botResponse = "Current season: {$row['season']}. Temperature: {$row['temperature']}°C";
        }
    }


    elseif (strpos($userMsg, 'land') !== false || strpos($userMsg, 'soil') !== false) {
        $sql = "SELECT soil_type, location FROM lands LIMIT 3";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $botResponse = "Land details:\n";
            while ($row = $result->fetch_assoc()) {
                $botResponse .= "- {$row['soil_type']} soil at {$row['location']}\n";
            }
        }
    }

   
    elseif (strpos($userMsg, 'crop') !== false || strpos($userMsg, 'plant') !== false) {
        $sql = "SELECT name, type, about FROM crops LIMIT 3";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $botResponse = "Available crops:\n";
            while ($row = $result->fetch_assoc()) {
                $botResponse .= "- {$row['name']} ({$row['type']}): {$row['about']}\n";
            }
        }
    }

  
    elseif (strpos($userMsg, 'equipment') !== false || strpos($userMsg, 'tool') !== false) {
        $sql = "SELECT name, type FROM equipments LIMIT 3";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $botResponse = "Equipments:\n";
            while ($row = $result->fetch_assoc()) {
                $botResponse .= "- {$row['name']} ({$row['type']})\n";
            }
        }
    }

  
    elseif (strpos($userMsg, 'pesticide') !== false) {
        $sql = "SELECT name, type FROM pestides LIMIT 3";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $botResponse = "Pesticides:\n";
            while ($row = $result->fetch_assoc()) {
                $botResponse .= "- {$row['name']} ({$row['type']})\n";
            }
        }
    }

  
    elseif (strpos($userMsg, 'fertilizer') !== false) {
        $sql = "SELECT name, type, nflag, pflag, poflag FROM fertilizer LIMIT 3";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $botResponse = "Fertilizers:\n";
            while ($row = $result->fetch_assoc()) {
                $botResponse .= "- {$row['name']} ({$row['type']}), N: {$row['nflag']}, P: {$row['pflag']}, K: {$row['poflag']}\n";
            }
        }
    }

    echo nl2br($botResponse);
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
     
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">

      <title>Smart_farming</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
 
      <link rel="stylesheet" href="css/bootstrap.min.css">
     
      <link rel="stylesheet" href="css/style.css">
     
      <link rel="stylesheet" href="css/responsive.css">
     
      <link rel="icon" href="images/fevicon.png" type="image/gif" />
    
      <link rel="stylesheet" href="css/owl.carousel.min.css">
      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
      <link rel="stylesheet" href="css/bootstrap-datepicker.min.css">
  
  <meta charset="UTF-8">
  <title>SmartFarming - Chatbot</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('mp.png') no-repeat center center fixed;
      background-size: cover;
    }

    .chat-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 10px;
      width: 350px;
      height: 500px;
      box-shadow: 0 0 15px rgba(0,0,0,0.3);
      display: flex;
      flex-direction: column;
      margin: 100px auto;
    }

    .chat-box {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      border-bottom: 1px solid #ccc;
    }

    .chat-message {
      margin-bottom: 10px;
    }

    .chat-message.user {
      text-align: right;
    }

    .chat-message.bot {
      text-align: left;
      color: #2ecc71;
    }

    .chat-input {
      display: flex;
      border-top: 1px solid #ccc;
    }

    .chat-input input {
      flex: 1;
      padding: 10px;
      border: none;
      outline: none;
    }

    .chat-input button {
      padding: 10px 15px;
      background-color: #2ecc71;
      color: white;
      border: none;
      cursor: pointer;
    }

    h1.title {
      text-align: center;
      color: white;
      margin-top: 50px;
      text-shadow: 1px 1px 5px #000;
    }
  </style>
</head>
<body>

  <?php include('navbar.html'); ?>

  <div class="chat-container">
    <div class="chat-box" id="chatBox">
      <div class="chat-message bot">Hello! Ask me anything about Smart Farming.</div>
    </div>
    <div class="chat-input">
      <input type="text" id="userInput" placeholder="Type your message..." />
      <button onclick="sendMessage()">Send</button>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script>
    function sendMessage() {
      const userInput = document.getElementById("userInput");
      const message = userInput.value.trim();
      if (message === "") return;

      addMessage("user", message);
      userInput.value = "";

      fetch("blog.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "message=" + encodeURIComponent(message)
      })
      .then(response => response.text())
      .then(reply => addMessage("bot", reply));
    }

    function addMessage(sender, text) {
      const chatBox = document.getElementById("chatBox");
      const msgDiv = document.createElement("div");
      msgDiv.className = "chat-message " + sender;
      msgDiv.textContent = text;
      chatBox.appendChild(msgDiv);
      chatBox.scrollTop = chatBox.scrollHeight;
    }
  </script>

</body>
</html>
