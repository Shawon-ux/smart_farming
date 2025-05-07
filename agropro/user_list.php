<?php
include 'db.php';

$farmers = $conn->query("SELECT * FROM farmer");

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM farmer WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: user_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
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

        .delete-btn {
            position: absolute;
            right: 15px;
            top: 15px;
            color: red;
            font-weight: bold;
            text-decoration: none;
        }

        .delete-btn:hover {
            color: darkred;
        }
    </style>
</head>
<body>

<div class="nav">
    <a href="admin_view.php">Home</a>
</div>

<h2 style="text-align:center;">Registered Farmers</h2>

<?php while ($u = $farmers->fetch_assoc()): ?>
    <div class="card">
        <a class="delete-btn" href="?delete=<?= urlencode($u['id']) ?>" onclick="return confirm('Delete this user?');">×</a>
        <strong><?= htmlspecialchars($u['name']) ?></strong><br>
        Serial No: <?= htmlspecialchars($u['serialno']) ?><br>
        ID: <?= htmlspecialchars($u['id']) ?><br>
        Address: <?= htmlspecialchars($u['address']) ?><br>
        Mobile: <?= htmlspecialchars($u['mb_num']) ?><br>
        Training: <?= htmlspecialchars($u['training_session']) ?><br>
    </div>
<?php endwhile; ?>

</body>
</html>
