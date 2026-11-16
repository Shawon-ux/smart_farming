<?php
include 'db.php';
session_start();
$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $by = trim($_POST['submitted_by'] ?? ''); $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['proj_name'] ?? ''); $loc = trim($_POST['location'] ?? '');
    $desc = trim($_POST['description'] ?? ''); $num = trim($_POST['number'] ?? '');
    $sd = $_POST['start_date'] ?? ''; $ed = $_POST['end_date'] ?? '';
    if (!$by || !$name || !$loc || !$desc || !$num || !$sd || !$ed) $err = 'Please fill in all required fields.';
    elseif (!preg_match('/^[0-9+\-\s]{6,20}$/', $num)) $err = 'Please enter a valid phone number.';
    elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Please enter a valid email address.';
    elseif ($ed < $sd) $err = 'End date cannot be before the start date.';
    else {
        $chk = $conn->prepare("SELECT 1 FROM project WHERE name = ?");
        $chk->bind_param("s", $name); $chk->execute();
        if ($chk->get_result()->num_rows) $err = 'A project with this name already exists. Please choose another name.';
        else {
            $st = $conn->prepare("INSERT INTO project (name, starting_date, ending_date, location, description, number, status, submitted_by, email) VALUES (?,?,?,?,?,?,'Unapproved',?,?)");
            $st->bind_param("ssssssss", $name, $sd, $ed, $loc, $desc, $num, $by, $email);
            if ($st->execute()) { $_SESSION['flash'] = 'Your project was submitted! It will appear in Projects once an admin approves it.'; header("Location: contact.php"); exit(); }
            else $err = 'Could not submit your project. Please try again.';
        }
    }
}
if (!empty($_SESSION['flash'])) { $msg = $_SESSION['flash']; unset($_SESSION['flash']); }
$v = fn($k) => htmlspecialchars($_POST[$k] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Submit a Project - Smart Farming</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/projects.css">
  <link rel="icon" href="images/fevicon.png" type="image/gif" />
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
</head>
<body class="main-layout inner_page">
  <?php include 'navbar.html'; ?>

  <section class="page-hero">
    <span class="badge-pill">Contact</span>
    <h1>Submit Your Project</h1>
    <p>Have a farming project or work that needs helping hands? Send it to our admin team for approval.</p>
  </section>

  <section class="req-wrap">
    <div class="container">
      <?php if ($msg): ?><div class="pj-alert ok"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif; ?>
      <?php if ($err): ?><div class="pj-alert err"><i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($err) ?></div><?php endif; ?>
      <div class="req-card">
        <div class="req-side">
          <h3>How it works</h3>
          <ol>
            <li>Fill in your project details</li>
            <li>Admin reviews your request</li>
            <li>Once approved, it goes live under <strong>Projects</strong></li>
            <li>Helpers can sign up to join you</li>
          </ol>
          <div class="note"><i class="fa fa-info-circle"></i> Your phone number is shown on the project so helpers can reach you.</div>
        </div>
        <form class="req-form" method="POST" action="contact.php">
          <div class="row2">
            <div><label>Your Name *</label><input class="pj-input" name="submitted_by" value="<?= $v('submitted_by') ?>" required></div>
            <div><label>Phone Number *</label><input class="pj-input" name="number" value="<?= $v('number') ?>" required></div>
          </div>
          <label>Email</label><input class="pj-input" type="email" name="email" value="<?= $v('email') ?>">
          <label>Project / Work Title *</label><input class="pj-input" name="proj_name" value="<?= $v('proj_name') ?>" required>
          <label>Location *</label><input class="pj-input" name="location" value="<?= $v('location') ?>" required>
          <div class="row2">
            <div><label>Start Date *</label><input class="pj-input" type="date" name="start_date" value="<?= $v('start_date') ?>" required></div>
            <div><label>End Date *</label><input class="pj-input" type="date" name="end_date" value="<?= $v('end_date') ?>" required></div>
          </div>
          <label>Description *</label><textarea class="pj-input" name="description" rows="4" maxlength="500" required><?= $v('description') ?></textarea>
          <button class="pj-btn block" type="submit">Submit for Approval</button>
        </form>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
