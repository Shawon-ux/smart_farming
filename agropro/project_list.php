<?php
include 'db.php';

// Add new project
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_project'])) {
    $stmt = $conn->prepare("INSERT INTO project (name, starting_date, ending_date, location, description, status) VALUES (?, ?, ?, ?, ?, 'Unapproved')");
    $stmt->bind_param("sssss", $_POST['proj_name'], $_POST['start_date'], $_POST['end_date'], $_POST['location'], $_POST['description']);
    $stmt->execute();
    $stmt->close();
    header("Location: project_list.php");
    exit();
}

// Delete project
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM project WHERE name = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: project_list.php");
    exit();
}

// Update approval status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status']) && isset($_POST['name'])) {
    $stmt = $conn->prepare("UPDATE project SET status = ? WHERE name = ?");
    $stmt->bind_param("ss", $_POST['status'], $_POST['name']);
    $stmt->execute();
    $stmt->close();
    header("Location: project_list.php");
    exit();
}

// Fetch all projects
$projects = $conn->query("SELECT * FROM project");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
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

        form.inline-form {
            display: inline;
        }

        /* Modal styles */
        #addModal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
        }

        #addModal .modal-content {
            background: white;
            padding: 20px;
            margin: 100px auto;
            width: 90%;
            max-width: 500px;
            border-radius: 10px;
            position: relative;
        }

        #addModal .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        input, textarea, select {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 5px;
        }

        input[type="submit"] {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background: #0056b3;
        }

        /* Status styling */
        .status {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }

        .status-approved {
            background-color: #28a745;
            color: white;
        }

        .status-unapproved {
            background-color: #ffc107;
            color: black;
        }
    </style>
</head>
<body>

<div class="nav">
    <a href="admin_view.php">Home</a>
    <a href="#" onclick="document.getElementById('addModal').style.display='block'">Add Project</a>
</div>

<h2 style="text-align:center;">Project List</h2>

<?php while ($proj = $projects->fetch_assoc()): ?>
    <div class="card">
        <a class="delete-btn" href="?delete=<?= urlencode($proj['name']) ?>" onclick="return confirm('Delete this project?');">×</a>
        <strong><?= htmlspecialchars($proj['name']) ?></strong><br>
        Start Date: <?= htmlspecialchars($proj['starting_date']) ?><br>
        End Date: <?= htmlspecialchars($proj['ending_date']) ?><br>
        Location: <?= htmlspecialchars($proj['location']) ?><br>
        Description: <?= nl2br(htmlspecialchars($proj['description'])) ?><br>
        
        <!-- Status indicator -->
        <span class="status <?= $proj['status'] == 'Approved' ? 'status-approved' : 'status-unapproved' ?>">
            <?= htmlspecialchars($proj['status']) ?>
        </span><br>

        <!-- Update form for status -->
        <form method="POST" class="inline-form">
            <input type="hidden" name="name" value="<?= htmlspecialchars($proj['name']) ?>">
            <select name="status">
                <option value="Approved" <?= $proj['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                <option value="Unapproved" <?= $proj['status'] == 'Unapproved' ? 'selected' : '' ?>>Unapproved</option>
            </select>
            <input type="submit" value="Update">
        </form>
    </div>
<?php endwhile; ?>

<!-- Add Project Modal -->
<div id="addModal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        <h3>Add Project</h3>
        <form method="POST">
            <input type="text" name="proj_name" placeholder="Project Name" required>
            <input type="date" name="start_date" required>
            <input type="date" name="end_date" required>
            <input type="text" name="location" placeholder="Location" required>
            <textarea name="description" placeholder="Description" rows="4" required></textarea>
            <input type="submit" name="add_project" value="Add">
        </form>
    </div>
</div>

<script>
    window.onclick = function(e) {
        const modal = document.getElementById('addModal');
        if (e.target === modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
