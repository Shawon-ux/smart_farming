<?php
session_start();
if (!isset($_SESSION['mb_num']) && !isset($_SESSION['farmer_id'])) {
    header('Location: login.php');
    exit();
}
include 'db.php';

// Locate the farmer by id (stable even after an approved phone change)
if (isset($_SESSION['farmer_id'])) {
    $stmt = $conn->prepare("SELECT * FROM farmer WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['farmer_id']);
} else {
    $stmt = $conn->prepare("SELECT * FROM farmer WHERE mb_num = ?");
    $stmt->bind_param("s", $_SESSION['mb_num']);
}
$stmt->execute();
$f = $stmt->get_result()->fetch_assoc();
if (!$f) { session_destroy(); header('Location: login.php'); exit(); }
$_SESSION['farmer_id'] = $f['id'];
$fid = (int)$f['id'];
$flash = $_SESSION['pf_flash'] ?? null;
unset($_SESSION['pf_flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = false; $msg = '';
    if (isset($_POST['save_profile'])) {
        $name = mb_substr(trim($_POST['name'] ?? ''), 0, 30);
        $addr = mb_substr(trim($_POST['address'] ?? ''), 0, 50);
        $tr   = mb_substr(trim($_POST['training_session'] ?? ''), 0, 100);
        if ($name === '' || $addr === '') {
            $msg = 'Name and address cannot be empty.';
        } else {
            $u = $conn->prepare("UPDATE farmer SET name=?, address=?, training_session=? WHERE id=?");
            $u->bind_param("sssi", $name, $addr, $tr, $fid);
            $ok = $u->execute();
            $msg = $ok ? 'Profile updated.' : 'Could not update profile.';
        }
    } elseif (isset($_POST['request_change'])) {
        $field = $_POST['field'] ?? '';
        $val = trim($_POST['new_value'] ?? '');
        if (!in_array($field, ['mb_num', 'email'], true)) {
            $msg = 'Invalid request.';
        } elseif ($field === 'mb_num' && !preg_match('/^[0-9]{7,12}$/', $val)) {
            $msg = 'Enter a valid mobile number (digits only).';
        } elseif ($field === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Enter a valid email address.';
        } else {
            $c = $conn->prepare("SELECT 1 FROM profile_change_request WHERE farmer_id=? AND field=? AND status='Pending'");
            $c->bind_param("is", $fid, $field);
            $c->execute();
            if ($c->get_result()->num_rows) {
                $msg = 'You already have a pending request for this. Please wait for the admin.';
            } else {
                $i = $conn->prepare("INSERT INTO profile_change_request (farmer_id, field, new_value) VALUES (?,?,?)");
                $i->bind_param("iss", $fid, $field, $val);
                $ok = $i->execute();
                $msg = $ok ? 'Request sent. Your change will apply once an admin approves it.' : 'Could not send request.';
            }
        }
    }
    $_SESSION['pf_flash'] = ['ok' => $ok, 'msg' => $msg];
    header('Location: profile.php');
    exit();
}

$r = $conn->prepare("SELECT field, new_value, status, created_at FROM profile_change_request WHERE farmer_id=? ORDER BY req_id DESC LIMIT 6");
$r->bind_param("i", $fid);
$r->execute();
$reqs = $r->get_result()->fetch_all(MYSQLI_ASSOC);
$pending = [];
foreach ($reqs as $q) if ($q['status'] === 'Pending') $pending[$q['field']] = $q['new_value'];
$conn->close();
$e = fn($v) => htmlspecialchars((string)$v);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Profile - Smart Farming</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/projects.css">
  <link rel="icon" href="images/fevicon.png" type="image/gif" />
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
  <style>
    .pf-wrap{background:#f4f7ea;padding:0 0 70px}
    .pf-card{max-width:900px;margin:-70px auto 0;background:#fff;border-radius:20px;box-shadow:0 14px 40px rgba(0,0,0,.14);overflow:hidden;position:relative}
    .pf-top{display:flex;align-items:center;gap:22px;padding:30px 34px;border-bottom:1px solid #eef2dc;flex-wrap:wrap}
    .pf-av{width:92px;height:92px;border-radius:50%;background:linear-gradient(135deg,#6b7908,#8aa10b);color:#fff;display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:700;box-shadow:0 6px 16px rgba(107,121,8,.4)}
    .pf-top h2{margin:0;color:#1f2a1f;font-size:28px}
    .pf-top .tag{display:inline-block;margin-top:6px;background:#e6f6e0;color:#2e7d32;padding:4px 14px;border-radius:20px;font-size:13px;font-weight:700}
    .pf-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px;padding:30px 34px}
    .pf-item{display:flex;gap:14px;align-items:center;background:#f9fbf2;border:1px solid #e8eed3;border-radius:14px;padding:16px 18px}
    .pf-item i{width:42px;height:42px;border-radius:12px;background:#6b7908;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
    .pf-item small{display:block;color:#7a8560;font-size:12px;text-transform:uppercase;letter-spacing:1px;font-weight:600}
    .pf-item strong{color:#1f2a1f;font-size:17px}
    .pf-actions{display:flex;gap:12px;flex-wrap:wrap;padding:0 34px 34px}
    .pf-actions .pj-btn{padding:11px 22px}
    .pf-actions .alt{background:#fff;color:#6b7908;border:2px solid #6b7908}
    .pf-actions .alt:hover{background:#6b7908;color:#fff}
    .pf-actions .out{background:#fff;color:#c62828;border:2px solid #c62828;margin-left:auto}
    .pf-actions .out:hover{background:#c62828;color:#fff}
    .pf-h{margin:26px 34px 0;color:#1f2a1f;font-size:18px;font-weight:700}
    .pf-h small{color:#8a6d00;font-weight:600;font-size:13px;margin-left:8px}
    .pf-form{display:grid;grid-template-columns:repeat(2,1fr);gap:0 18px;padding:14px 34px 0}
    .pf-form label{font-weight:600;color:#1f2a1f;font-size:14px;margin-bottom:4px;display:block}
    .pf-form .full{grid-column:1/-1}
    .pf-form input:disabled{background:#f1f3e8;color:#777}
    .pf-grid{padding-top:16px}
    .pf-req{display:flex;gap:8px;width:100%;margin-top:6px}
    .pf-req .pj-input{margin:0;flex:1;padding:9px 12px}
    .alt-sm{padding:9px 14px!important;font-size:13px;white-space:nowrap}
    .pf-pend{width:100%;margin-top:8px;background:#fff6dc;color:#8a6d00;padding:8px 12px;border-radius:10px;font-size:13px;font-weight:600}
    .pf-hist{margin:12px 34px 0;border:1px solid #e8eed3;border-radius:12px;overflow:hidden}
    .pf-hist div{display:flex;justify-content:space-between;padding:10px 16px;border-bottom:1px solid #eef2dc;color:#1f2a1f;font-size:14px}
    .pf-hist div:last-child{border:0}
    .pf-hist em{font-style:normal;font-weight:700;font-size:12px;padding:2px 10px;border-radius:12px}
    .st-pending{background:#fff6dc;color:#8a6d00}.st-approved{background:#e6f6e0;color:#2e7d32}.st-rejected{background:#fdeaea;color:#c62828}
    .pf-actions{padding-top:26px}
    @media(max-width:700px){.pf-form{grid-template-columns:1fr;padding:14px 22px 0}.pf-h,.pf-hist{margin-left:22px;margin-right:22px}.pf-req{flex-direction:column}.pf-grid{grid-template-columns:1fr;padding:22px}.pf-top,.pf-actions{padding-left:22px;padding-right:22px}.pf-actions .out{margin-left:0}}
  </style>
</head>
<body class="main-layout inner_page">
  <?php include 'navbar.html'; ?>

  <section class="page-hero" style="padding-bottom:110px">
    <span class="badge-pill">My Profile</span>
    <h1>Welcome back, <?= $e(explode(' ', $f['name'])[0]) ?></h1>
    <p>Your farmer account details at a glance.</p>
  </section>

  <section class="pf-wrap">
    <div class="pf-card">
      <div class="pf-top">
        <div class="pf-av"><?= $e(mb_strtoupper(mb_substr($f['name'], 0, 1))) ?></div>
        <div>
          <h2><?= $e($f['name']) ?></h2>
          <span class="tag"><i class="fa fa-check-circle"></i> Registered Farmer</span>
        </div>
      </div>
      <?php if ($flash): ?>
        <div class="pj-alert <?= $flash['ok'] ? 'ok' : 'err' ?>" style="margin:20px 34px 0"><?= $e($flash['msg']) ?></div>
      <?php endif; ?>

      <h4 class="pf-h">Edit your details</h4>
      <form method="POST" class="pf-form">
        <div><label>Name</label><input class="pj-input" name="name" maxlength="30" value="<?= $e($f['name']) ?>" required></div>
        <div><label>Address</label><input class="pj-input" name="address" maxlength="50" value="<?= $e($f['address']) ?>" required></div>
        <div><label>Training Session</label><input class="pj-input" name="training_session" maxlength="100" value="<?= $e($f['training_session']) ?>"></div>
        <div><label>Farmer ID</label><input class="pj-input" value="<?= $e($f['serialno']) ?>" disabled></div>
        <div class="full"><button class="pj-btn" name="save_profile" type="submit"><i class="fa fa-save"></i> Save changes</button></div>
      </form>

      <h4 class="pf-h">Phone &amp; email <small><i class="fa fa-lock"></i> changes need admin approval</small></h4>
      <div class="pf-grid">
        <?php foreach ([['mb_num','fa-phone','Mobile Number',$f['mb_num'],'tel','New mobile number'],['email','fa-envelope','Email',$f['email'] ?? '','email','New email address']] as [$k,$ic,$lb,$cur,$ty,$ph]): ?>
        <div class="pf-item" style="flex-wrap:wrap">
          <i class="fa <?= $ic ?>"></i>
          <div><small><?= $lb ?></small><strong><?= $cur !== '' ? $e($cur) : 'Not added' ?></strong></div>
          <?php if (isset($pending[$k])): ?>
            <div class="pf-pend"><i class="fa fa-clock-o"></i> Pending approval: <?= $e($pending[$k]) ?></div>
          <?php else: ?>
            <form method="POST" class="pf-req">
              <input type="hidden" name="field" value="<?= $k ?>">
              <input class="pj-input" type="<?= $ty ?>" name="new_value" placeholder="<?= $ph ?>" required>
              <button class="pj-btn alt-sm" name="request_change" type="submit">Request change</button>
            </form>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <?php if ($reqs): ?>
      <h4 class="pf-h">Your recent requests</h4>
      <div class="pf-hist">
        <?php foreach ($reqs as $q): ?>
          <div><span><?= $q['field'] === 'mb_num' ? 'Mobile' : 'Email' ?> → <strong><?= $e($q['new_value']) ?></strong></span><em class="st-<?= strtolower($q['status']) ?>"><?= $e($q['status']) ?></em></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="pf-actions">
        <a class="pj-btn" href="contact.php"><i class="fa fa-plus"></i> Submit a Project</a>
        <a class="pj-btn alt" href="calculator.php"><i class="fa fa-calculator"></i> Cost Calculator</a>
        <a class="pj-btn alt" href="blog.php"><i class="fa fa-comments"></i> Ask AgroBot</a>
        <a class="pj-btn out" href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
