<?php
include 'db.php'; 

// Handle Add Equipment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_equipment'])) {
    $stmt = $conn->prepare("INSERT INTO equipments (name, type, model_number, availability, market_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $_POST['eq_name'], $_POST['eq_type'], $_POST['model_number'], $_POST['availability'], $_POST['market_name']);
    $stmt->execute();
    $stmt->close();
    header("Location: equipment_list.php");
    exit();
}

// Handle Delete Equipment
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM equipments WHERE name = $name");
    header("Location: equipment_list.php");
    exit();
}

// Handle Update Equipment Availability
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_equipment'])) {
    $stmt = $conn->prepare("UPDATE equipments SET availability = ? WHERE name = ?");
    $stmt->bind_param("si", $_POST['availability'], $_POST['name']);
    $stmt->execute();
    $stmt->close();
    header("Location: equipment_list.php");
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
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }

        /* Navigation bar styling */
        .nav {
            margin-bottom: 20px;
            display: flex;
            justify-content: center; /* Center the buttons */
            gap: 15px; /* Space between buttons */
        }
        
        .nav a {
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            transition: background 0.3s ease;
        }

        .nav a:hover {
            background: #0056b3;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4); /* Black with opacity */
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%; /* Adjust the width as per requirement */
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Equipment Card Styling */
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card strong {
            font-size: 18px;
            color: #333;
        }

        .card a.delete-btn {
            position: absolute;
            right: 15px;
            top: 15px;
            color: red;
            font-weight: bold;
            text-decoration: none;
        }

        .card a.delete-btn:hover {
            color: darkred;
        }

        /* Inline form to update availability */
        form.inline-form {
            display: inline;
        }

        form.inline-form select {
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form.inline-form input[type="submit"] {
            padding: 6px 12px;
            background-color: #28a745;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        form.inline-form input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="nav">
    <a href="admin_view.php">Home</a>
    <a href="#" id="openModalBtn">Add Equipment</a>
</div>

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

<!-- Modal Structure -->
<div id="addEquipmentModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModalBtn">&times;</span>
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
    </div>
</div>

<!-- JavaScript to manage the modal -->
<script>
    // Get modal elements
    var modal = document.getElementById("addEquipmentModal");
    var openModalBtn = document.getElementById("openModalBtn");
    var closeModalBtn = document.getElementById("closeModalBtn");

    // Open the modal
    openModalBtn.onclick = function() {
        modal.style.display = "block";
    }

    // Close the modal when the user clicks on <span> (x)
    closeModalBtn.onclick = function() {
        modal.style.display = "none";
    }

    // Close the modal if the user clicks anywhere outside of the modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
