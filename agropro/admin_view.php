<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Smart Farming</title>
  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 60px;
            background-color: #f1f4f9;
        }
        .admin-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .admin-panel {
            max-width: 600px;
            margin: auto;
        }
        .btn-block {
            margin-bottom: 15px;
        }
        .logout-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="admin-header">
            <h1 class="display-5">Smart Farming Admin Panel</h1>
            <p class="text-muted">Manage system entities</p>
        </div>

        <div class="admin-panel">
            <a href="user_list.php" class="btn btn-primary btn-lg btn-block w-100">User/Farmer List</a>
            <a href="equipment_list.php" class="btn btn-success btn-lg btn-block w-100">Equipment List</a>
            <a href="fertilizer_list.php" class="btn btn-warning btn-lg btn-block w-100">Fertilizer List</a>
            <a href="pesticide_list.php" class="btn btn-danger btn-lg btn-block w-100">Pesticide List</a>
            <a href="project_list.php" class="btn btn-danger btn-lg btn-block w-100">Projects</a>
        </div>
    </div>

    <a href="logout.php" class="btn btn-outline-secondary logout-btn">Logout</a>

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
