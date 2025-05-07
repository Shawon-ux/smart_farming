<?php
include 'db.php'; // same folder

// Handle Add Equipment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_equipment'])) {
    $stmt = $conn->prepare("INSERT INTO equipments (name, type, model_number, availability, market_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $_POST['eq_name'], $_POST['eq_type'], $_POST['model_number'], $_POST['availability'], $_POST['market_name']);
    $stmt->execute();
    $stmt->close();
    header("Location: equipments.php");
    exit();
}

// Handle Delete Equipment
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM equipments WHERE name = $name");
    header("Location: equipments.php");
    exit();
}

// Handle Update Equipment Availability
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_equipment'])) {
    $stmt = $conn->prepare("UPDATE equipments SET availability = ? WHERE name = ?");
    $stmt->bind_param("si", $_POST['availability'], $_POST['name']);
    $stmt->execute();
    $stmt->close();
    header("Location: equipments.php");
    exit();
}

// Fetch all equipments
$equipments = $conn->query("SELECT * FROM equipments");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Equipments</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; padding: 20px; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 10px; text-decoration: none; background: #007bff; color: white; padding: 8px 16px; border-radius: 4px; }
        .card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: relative;
        }
        .delete-btn {
            position: absolute;
            right: 15px;
            top: 15px;
            color: red;
            font-weight: bold;
            text-decoration: none;
        }
        form.inline-form { display: inline; }
    </style>
</head>
<body>

<div class="nav">
    <a href="admin_view.php">Home</a>
    <a href="#addForm">Add Equipment</a>
</div>

<h2>Add Equipment</h2>
<form method="POST" id="addForm">
    <input type="text" name="eq_name" placeholder="Name" required>
    <input type="text" name="eq_type" placeholder="Type" required>
    <input type="text" name="model_number" placeholder="Model Number" required>
    <select name="availability">
        <option>Available</option>
        <option>Unavailable</option>
    </select>
    <input type="text" name="market_name" placeholder="Market Name" required>
    <input type="submit" name="add_equipment" value="Add">
</form>

<h2>All Equipments</h2>
<?php while ($eq = $equipments->fetch_assoc()): ?>
    <div class="card">
        <a class="delete-btn" href="?delete=<?= $eq['name'] ?>" onclick="return confirm('Delete this item?');">×</a>
        <strong><?= htmlspecialchars($eq['name']) ?></strong><br>
        Type: <?= htmlspecialchars($eq['type']) ?><br>
        Model: <?= htmlspecialchars($eq['model_number']) ?><br>
        Market: <?= htmlspecialchars($eq['market_name']) ?><br>

        <form method="POST" class="inline-form">
            <input type="hidden" name="name" value="<?= $eq['name'] ?>">
            <select name="availability">
                <option <?= $eq['availability']=='Available'?'selected':'' ?>>Available</option>
                <option <?= $eq['availability']=='Unavailable'?'selected':'' ?>>Unavailable</option>
            </select>
            <input type="submit" name="update_equipment" value="Update">
        </form>
    </div>
<?php endwhile; ?>

</body>
</html>
