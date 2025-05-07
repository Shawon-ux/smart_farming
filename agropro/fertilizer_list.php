<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_fertilizer'])) {
    $stmt = $conn->prepare("INSERT INTO fertilizer (name, `usage`, type, nflag, pflag, poflag, availability, market_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $_POST['fert_name'], $_POST['usage'], $_POST['fert_type'], $_POST['nflag'], $_POST['pflag'], $_POST['poflag'], $_POST['fert_availability'], $_POST['fert_market_name']);
    $stmt->execute();
    $stmt->close();
    header("Location: fertilizer_list.php");
    exit();
}

if (isset($_GET['delete'])) {
    $name = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM fertilizer WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();
    header("Location: fertilizer_list.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_fertilizer'])) {
    $stmt = $conn->prepare("UPDATE fertilizer SET availability = ? WHERE name = ?");
    $stmt->bind_param("ss", $_POST['availability'], $_POST['name']);
    $stmt->execute();
    $stmt->close();
    header("Location: fertilizer_list.php");
    exit();
}

$fertilizers = $conn->query("SELECT * FROM fertilizer");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Fertilizers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }

        .nav {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
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

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
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

        input[type="text"], select {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="nav">
    <a href="admin_view.php">Home</a>
    <a href="#" id="openModalBtn">Add Fertilizer</a>
</div>

<h2>All Fertilizers</h2>
<?php while ($f = $fertilizers->fetch_assoc()): ?>
    <div class="card">
        <a class="delete-btn" href="?delete=<?= urlencode($f['name']) ?>" onclick="return confirm('Delete this fertilizer?');">×</a>
        <strong><?= htmlspecialchars($f['name']) ?></strong><br>
        Type: <?= htmlspecialchars($f['type']) ?><br>
        Usage: <?= htmlspecialchars($f['usage']) ?><br>
        N: <?= htmlspecialchars($f['nflag']) ?> |
        P: <?= htmlspecialchars($f['pflag']) ?> |
        PO: <?= htmlspecialchars($f['poflag']) ?><br>
        Market: <?= htmlspecialchars($f['market_name']) ?><br>
        <form method="POST" class="inline-form">
            <input type="hidden" name="name" value="<?= htmlspecialchars($f['name']) ?>">
            <select name="availability">
                <option <?= $f['availability'] == 'Available' ? 'selected' : '' ?>>Available</option>
                <option <?= $f['availability'] == 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
            </select>
            <input type="submit" name="update_fertilizer" value="Update">
        </form>
    </div>
<?php endwhile; ?>

<!-- Modal -->
<div id="addFertilizerModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModalBtn">&times;</span>
        <h2>Add Fertilizer</h2>
        <form method="POST">
            <input type="text" name="fert_name" placeholder="Name" required>
            <input type="text" name="usage" placeholder="Usage" required>
            <input type="text" name="fert_type" placeholder="Type" required>
            <input type="text" name="nflag" placeholder="N-Flag" required>
            <input type="text" name="pflag" placeholder="P-Flag" required>
            <input type="text" name="poflag" placeholder="PO-Flag" required>
            <select name="fert_availability">
                <option>Available</option>
                <option>Unavailable</option>
            </select>
            <input type="text" name="fert_market_name" placeholder="Market Name" required>
            <input type="submit" name="add_fertilizer" value="Add">
        </form>
    </div>
</div>

<script>
    var modal = document.getElementById("addFertilizerModal");
    var openBtn = document.getElementById("openModalBtn");
    var closeBtn = document.getElementById("closeModalBtn");

    openBtn.onclick = function () {
        modal.style.display = "block";
    }

    closeBtn.onclick = function () {
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
