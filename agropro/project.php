<?php
include('db.php');

// Fetch approved projects
$query = "SELECT * FROM project WHERE status = 'Approved'"; 
$result = $conn->query($query);

// Check if any projects were returned
if ($result->num_rows > 0) {
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
} else {
    $projects = [];
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
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="icon" href="images/fevicon.png" type="image/gif" />
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.min.css">
</head>

<body class="main-layout inner_page blog_page">
    <div class="loader_bg">
        <div class="loader"><img src="images/loading.gif" alt="#"/></div>
    </div>
    
    <div class="full_bg">
        <?php include('navbar.html'); ?>
    </div>
    
    <div class="container" style="margin-top: 40px;">
        <h1>Running Projects</h1>

        <?php 
        // If there are projects, display them
        if (!empty($projects)) {
            foreach ($projects as $project) {
        ?>
            <div class="login-container" style="margin: 20px auto;">
                <h2><?php echo htmlspecialchars($project['name']); ?></h2>
                <h4>Location: <?php echo htmlspecialchars($project['location']); ?></h4>
                <p><?php echo htmlspecialchars($project['description']); ?></p>
                <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['starting_date']); ?></p>
                <p><strong>End Date:</strong> <?php echo htmlspecialchars($project['ending_date']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($project['number']); ?></p>
            </div>
        <?php 
            }
        } else {
            echo "<p>No active projects at the moment.</p>";
        }
        ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-3.0.0.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>
