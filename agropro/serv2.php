<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smart_farming";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Equipment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_equipment'])) {
    $name = $_POST['eq_name'];
    $type = $_POST['eq_type'];
    $model = $_POST['model_number'];
    $availability = $_POST['availability'];
    $market_name = $_POST['market_name'];
    $stmt = $conn->prepare("INSERT INTO equipments (name, type, model_number, availability, market_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $type, $model, $availability, $market_name);
    $stmt->execute();
    $stmt->close();
}

// Handle Update Equipment Availability
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_equipment'])) {
    $product_id = $_POST['product_id'];
    $availability = $_POST['availability'];
    $stmt = $conn->prepare("UPDATE equipments SET availability = ? WHERE product_id = ?");
    $stmt->bind_param("si", $availability, $product_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Add Fertilizer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_fertilizer'])) {
    $name = $_POST['fert_name'];
    $usage = $_POST['usage'];
    $type = $_POST['fert_type'];
    $nflag = $_POST['nflag'];
    $pflag = $_POST['pflag'];
    $poflag = $_POST['poflag'];
    $availability = $_POST['fert_availability'];
    $market_name = $_POST['fert_market_name'];
    $stmt = $conn->prepare("INSERT INTO fertilizer (name, `usage`, type, nflag, pflag, poflag, availability, market_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $usage, $type, $nflag, $pflag, $poflag, $availability, $market_name);
    $stmt->execute();
    $stmt->close();
}

// Handle Update Fertilizer Availability
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_fertilizer'])) {
    $product_id = $_POST['product_id'];
    $availability = $_POST['availability'];
    $stmt = $conn->prepare("UPDATE fertilizer SET availability = ? WHERE product_id = ?");
    $stmt->bind_param("si", $availability, $product_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Add Pesticide
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_pesticide'])) {
    $name = $_POST['pest_name'];
    $type = $_POST['pest_type'];
    $expired_date = $_POST['expired_date'];
    $availability = $_POST['pest_availability'];
    $market_name = $_POST['pest_market_name'];
    $stmt = $conn->prepare("INSERT INTO pesticides (name, type, expired_date, availability, market_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $type, $expired_date, $availability, $market_name);
    $stmt->execute();
    $stmt->close();
}

// Handle Update Pesticide Availability
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pesticide'])) {
    $product_id = $_POST['product_id'];
    $availability = $_POST['availability'];
    $stmt = $conn->prepare("UPDATE pesticides SET availability = ? WHERE product_id = ?");
    $stmt->bind_param("si", $availability, $product_id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="serv2style.css">
    <meta charset="UTF-8">
    <title>Smart Farming - Edit Availability</title>
    <style>
        body {
            font-family: Arial;
            background: #f8f9fa;
            margin: 20px;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #ccc;
        }
        form, table {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .available {
            background: #e6f4ea;
            color: #155724;
        }
        .unavailable {
            background: #f8d7da;
            color: #721c24;
        }
        input[type="submit"] {
            padding: 5px 10px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        select {
            padding: 4px;
        }
    </style>
</head>
<body>

    <h1>Add Equipment</h1>
    <form method="POST">
        Name: <input type="text" name="eq_name" required>
        Type: <input type="text" name="eq_type" required>
        Model Number: <input type="text" name="model_number" required>
        Availability:
        <select name="availability" required>
            <option>Available</option>
            <option>Unavailable</option>
        </select>
        Market Name: <input type="text" name="market_name" required>
        <input type="submit" name="add_equipment" value="Add Equipment">
    </form>

    <h2>Equipment List</h2>
    <table>
        <tr><th>Name</th><th>Type</th><th>Model</th><th>Availability</th><th>Market</th><th>Update</th></tr>
        <?php
        $result = $conn->query("SELECT * FROM equipments");
        while($row = $result->fetch_assoc()) {
            $class = strtolower($row["availability"]) === 'available' ? 'available' : 'unavailable';
            echo "<tr class='$class'>
                    <td>{$row['name']}</td>
                    <td>{$row['type']}</td>
                    <td>{$row['model_number']}</td>
                    <td>
                        <form method='POST' style='display:inline;'>
                            <select name='availability'>
                                <option value='Available' ".($row['availability']=='Available'?'selected':'').">Available</option>
                                <option value='Unavailable' ".($row['availability']=='Unavailable'?'selected':'').">Unavailable</option>
                            </select>
                    </td>
                    <td>{$row['market_name']}</td>
                    <td><input type='submit' name='update_equipment' value='Update'></form></td>
                  </tr>";
        }
        ?>
    </table>

    <h1>Add Fertilizer</h1>
    <form method="POST">
        Name: <input type="text" name="fert_name" required>
        Usage: <input type="text" name="usage" required>
        Type: <input type="text" name="fert_type" required>
        N-Flag: <input type="text" name="nflag" required>
        P-Flag: <input type="text" name="pflag" required>
        PO-Flag: <input type="text" name="poflag" required>
        Availability:
        <select name="fert_availability" required>
            <option>Available</option>
            <option>Unavailable</option>
        </select>
        Market Name: <input type="text" name="fert_market_name" required>
        <input type="submit" name="add_fertilizer" value="Add Fertilizer">
    </form>

    <h2>Fertilizer List</h2>
    <table>
        <tr><th>Name</th><th>Usage</th><th>Type</th><th>N</th><th>P</th><th>PO</th><th>Availability</th><th>Market</th><th>Update</th></tr>
        <?php
        $fert = $conn->query("SELECT * FROM fertilizer");
        while($row = $fert->fetch_assoc()) {
            $class = strtolower($row["availability"]) === 'available' ? 'available' : 'unavailable';
            echo "<tr class='$class'>
                    <td>{$row['name']}</td>
                    <td>{$row['usage']}</td>
                    <td>{$row['type']}</td>
                    <td>{$row['nflag']}</td>
                    <td>{$row['pflag']}</td>
                    <td>{$row['poflag']}</td>
                    <td>
                        <form method='POST' style='display:inline;'>
                    
                            <select name='availability'>
                                <option value='Available' ".($row['availability']=='Available'?'selected':'').">Available</option>
                                <option value='Unavailable' ".($row['availability']=='Unavailable'?'selected':'').">Unavailable</option>
                            </select>
                    </td>
                    <td>{$row['market_name']}</td>
                    <td><input type='submit' name='update_fertilizer' value='Update'></form></td>
                  </tr>";
        }
        ?>
    </table>

    <h1>Add Pesticide</h1>
    <form method="POST">
        Name: <input type="text" name="pest_name" required>
        Type: <input type="text" name="pest_type" required>
        Expired Date: <input type="date" name="expired_date" required>
        Availability:
        <select name="pest_availability" required>
            <option>Available</option>
            <option>Unavailable</option>
        </select>
        Market Name: <input type="text" name="pest_market_name" required>
        <input type="submit" name="add_pesticide" value="Add Pesticide">
    </form>

    <h2>Pesticide List</h2>
    <table>
        <tr><th>Name</th><th>Type</th><th>Expires</th><th>Availability</th><th>Market</th><th>Update</th></tr>
        <?php
        $pest = $conn->query("SELECT * FROM pesticides");
        while($row = $pest->fetch_assoc()) {
            $class = strtolower($row["availability"]) === 'available' ? 'available' : 'unavailable';
            echo "<tr class='$class'>
                    <td>{$row['name']}</td>
                    <td>{$row['type']}</td>
                    <td>{$row['expired_date']}</td>
                    <td>
                        <form method='POST' style='display:inline;'>
                
                            <select name='availability'>
                                <option value='Available' ".($row['availability']=='Available'?'selected':'').">Available</option>
                                <option value='Unavailable' ".($row['availability']=='Unavailable'?'selected':'').">Unavailable</option>
                            </select>
                    </td>
                    <td>{$row['market_name']}</td>
                    <td><input type='submit' name='update_pesticide' value='Update'></form></td>
                  </tr>";
        }
        ?>
    </table>

</body>
</html>
