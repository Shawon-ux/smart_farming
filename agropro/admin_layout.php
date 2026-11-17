<?php
// Shared layout for all admin pages. Usage: require 'admin_layout.php'; admin_header('Title','key'); ... admin_footer();
require_once __DIR__ . '/admin_guard.php';
include_once __DIR__ . '/db.php';
mysqli_report(MYSQLI_REPORT_OFF);

function admin_count($sql) {
    global $conn;
    $r = @$conn->query($sql);
    $row = $r ? $r->fetch_row() : null;
    return $row ? (int)$row[0] : 0;
}
function admin_header($title, $active) {
    $pendReq = admin_count("SELECT COUNT(*) FROM profile_change_request WHERE status='Pending'");
    $pendPrj = admin_count("SELECT COUNT(*) FROM project WHERE status='Unapproved'");
    $items = [
        ['home', 'admin_view.php', 'bi-speedometer2', 'Dashboard', 0],
        ['farmers', 'user_list.php', 'bi-people', 'Farmers', 0],
        ['equipment', 'equipment_list.php', 'bi-tools', 'Equipment', 0],
        ['fertilizer', 'fertilizer_list.php', 'bi-droplet-half', 'Fertilizer', 0],
        ['pesticide', 'pesticide_list.php', 'bi-bug', 'Pesticide', 0],
        ['projects', 'project_list.php', 'bi-kanban', 'Projects', $pendPrj],
        ['requests', 'admin_requests.php', 'bi-person-lock', 'Profile Requests', $pendReq],
        ['crops', 'admin_crops.php', 'bi-calendar3', 'Crop Calendar', 0],
        ['admins', 'admin_admins.php', 'bi-shield-lock', 'Admins', 0],
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?> - Admin | Smart Farming</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="css/admin.css?v=<?= filemtime(__DIR__ . '/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="adm">
<aside class="adm-side" id="side">
  <div class="adm-brand">Smart<span>Farming</span><small>Admin Panel</small></div>
  <nav class="adm-nav">
    <?php foreach ($items as [$k, $href, $ic, $label, $n]): ?>
      <a href="<?= $href ?>" class="<?= $k === $active ? 'on' : '' ?>"><i class="bi <?= $ic ?>"></i><?= $label ?><?php if ($n): ?><span class="badge-n"><?= $n ?></span><?php endif; ?></a>
    <?php endforeach; ?>
    <a href="index.php" target="_blank"><i class="bi bi-box-arrow-up-right"></i>View Website</a>
  </nav>
  <div class="adm-out"><a href="logout.php"><i class="bi bi-box-arrow-left"></i>Logout</a></div>
</aside>
<div class="adm-main">
  <header class="adm-top">
    <button class="adm-burger" onclick="document.getElementById('side').classList.toggle('open')"><i class="bi bi-list"></i></button>
    <h1><?= htmlspecialchars($title) ?></h1>
    <div class="who"><span><?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin') ?></span><div class="av"><?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)) ?></div></div>
  </header>
  <main class="adm-body">
<?php }
function admin_footer() { ?>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('[data-filter]').forEach(function (inp) {
  inp.addEventListener('input', function () {
    var q = inp.value.toLowerCase();
    document.querySelectorAll(inp.dataset.filter + ' tbody tr').forEach(function (tr) {
      tr.style.display = tr.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
    });
  });
});
</script>
</body>
</html>
<?php }
