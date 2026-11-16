<?php
include('db.php');

// Handle form submission
$signup_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup_submit'])) {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $project_name = $_POST['project_name'];

    $stmt = $conn->prepare("INSERT INTO helper (name, phone_number, address, project_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phone_number, $address, $project_name);

    if ($stmt->execute()) {
        $signup_message = "Thank you for signing up!";
    } else {
        $signup_message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch approved projects
$query = "SELECT * FROM project WHERE status = 'Approved'"; 
$result = $conn->query($query);
$projects = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
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
    
    <meta charset="utf-8">
    <title>Smart Farming - Projects</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body class="main-layout inner_page blog_page">
    <?php include('navbar.html'); ?>

    <div class="container" style="margin-top: 40px;">
        <h1>Running Projects</h1>

        <?php if (!empty($signup_message)): ?>
            <div class="alert alert-info"><?php echo $signup_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($projects)): ?>
            <?php foreach ($projects as $project): ?>
                <div class="card mb-4 p-3">
                    <h2><?php echo htmlspecialchars($project['name']); ?></h2>
                    <h4>Location: <?php echo htmlspecialchars($project['location']); ?></h4>
                    <p><?php echo htmlspecialchars($project['description']); ?></p>
                    <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['starting_date']); ?></p>
                    <p><strong>End Date:</strong> <?php echo htmlspecialchars($project['ending_date']); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($project['number']); ?></p>

                    <form method="POST" class="mt-3">
                        <input type="hidden" name="project_name" value="<?php echo htmlspecialchars($project['name']); ?>">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number:</label>
                            <input type="text" name="phone_number" class="form-control" required>
                        </div>
                        </div>
                        <div class="form-group">
                            <label>Project Name</label>
                            <input type="text" name="project_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Address:</label>
                            <textarea name="address" class="form-control" required></textarea>
                        </div>
                        <button type="submit" name="signup_submit" class="btn btn-primary">Sign Up to Help</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No active projects at the moment.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
