<?php
include('db.php');
$msg = ''; $err = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup_submit'])) {
    $stmt = $conn->prepare("INSERT INTO helper (name, phone_number, address, project_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $_POST['name'], $_POST['phone_number'], $_POST['address'], $_POST['project_name']);
    if ($stmt->execute()) $msg = "Thank you for signing up to help!";
    else $err = "Could not sign you up: " . $stmt->error;
    $stmt->close();
}
$result = $conn->query("SELECT * FROM project WHERE status = 'Approved' ORDER BY starting_date DESC");
$projects = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Running Projects - Smart Farming</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/projects.css">
  <link rel="icon" href="images/fevicon.png" type="image/gif" />
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
</head>
<body class="main-layout inner_page">
  <?php include('navbar.html'); ?>

  <section class="page-hero">
    <span class="badge-pill">Projects</span>
    <h1>Running Projects</h1>
    <p>Farming projects approved by our team. Join one as a helper, or <a href="contact.php" style="color:#fff;text-decoration:underline">submit your own</a>.</p>
  </section>

  <section class="pj-wrap">
    <div class="container">
      <?php if ($msg): ?><div class="pj-alert ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
      <?php if ($err): ?><div class="pj-alert err"><?= htmlspecialchars($err) ?></div><?php endif; ?>

      <?php if ($projects): ?>
      <div class="pj-grid">
        <?php foreach ($projects as $i => $p): ?>
        <div class="pj-card">
          <div class="pj-head">
            <span class="pj-live">● Running</span>
            <h3><?= htmlspecialchars($p['name']) ?></h3>
            <div class="loc"><i class="fa fa-map-marker"></i> <?= htmlspecialchars($p['location']) ?></div>
          </div>
          <div class="pj-body">
            <p class="desc"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
            <div class="pj-meta">
              <span><i class="fa fa-calendar"></i> <?= htmlspecialchars($p['starting_date']) ?> → <?= htmlspecialchars($p['ending_date']) ?></span>
              <span><i class="fa fa-phone"></i> <?= htmlspecialchars($p['number']) ?></span>
              <?php if (!empty($p['submitted_by'])): ?><span><i class="fa fa-user"></i> <?= htmlspecialchars($p['submitted_by']) ?></span><?php endif; ?>
            </div>
          </div>
          <div class="pj-foot">
            <button type="button" class="pj-toggle" onclick="document.getElementById('h<?= $i ?>').classList.toggle('open')">Sign Up to Help</button>
            <form method="POST" class="pj-help" id="h<?= $i ?>">
              <input type="hidden" name="project_name" value="<?= htmlspecialchars($p['name']) ?>">
              <input class="pj-input" type="text" name="name" placeholder="Your name" required>
              <input class="pj-input" type="tel" name="phone_number" placeholder="Phone number" required>
              <textarea class="pj-input" name="address" placeholder="Address" rows="2" required></textarea>
              <button type="submit" name="signup_submit" class="pj-btn block">Confirm Sign Up</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
        <div class="pj-empty"><h3>No active projects right now</h3><p>Be the first — submit a project for approval.</p><a class="pj-btn" href="contact.php">Submit a Project</a></div>
      <?php endif; ?>
    </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
