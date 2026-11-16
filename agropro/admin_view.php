<?php
require_once __DIR__ . '/admin_layout.php';
$stats = [
    ['user_list.php', 'c1', 'bi-people', admin_count("SELECT COUNT(*) FROM farmer"), 'Registered farmers'],
    ['project_list.php', 'c2', 'bi-kanban', admin_count("SELECT COUNT(*) FROM project WHERE status='Approved'"), 'Running projects'],
    ['project_list.php', 'c3', 'bi-hourglass-split', admin_count("SELECT COUNT(*) FROM project WHERE status='Unapproved'"), 'Projects awaiting approval'],
    ['admin_requests.php', 'c4', 'bi-person-lock', admin_count("SELECT COUNT(*) FROM profile_change_request WHERE status='Pending'"), 'Profile change requests'],
    ['equipment_list.php', 'c5', 'bi-tools', admin_count("SELECT COUNT(*) FROM equipments"), 'Equipment items'],
    ['fertilizer_list.php', 'c6', 'bi-droplet-half', admin_count("SELECT COUNT(*) FROM fertilizer"), 'Fertilizers'],
    ['pesticide_list.php', 'c4', 'bi-bug', admin_count("SELECT COUNT(*) FROM pesticides"), 'Pesticides'],
    ['project_list.php', 'c1', 'bi-person-raised-hand', admin_count("SELECT COUNT(*) FROM helper"), 'Helper sign-ups'],
];
$pending = $conn->query("SELECT name, location, submitted_by, starting_date FROM project WHERE status='Unapproved' ORDER BY starting_date LIMIT 6");
$reqs = $conn->query("SELECT r.field, r.new_value, f.name FROM profile_change_request r JOIN farmer f ON f.id=r.farmer_id WHERE r.status='Pending' ORDER BY r.req_id DESC LIMIT 6");
admin_header('Dashboard', 'home');
?>
<div class="stats">
  <?php foreach ($stats as [$href, $c, $ic, $n, $label]): ?>
    <a class="stat" href="<?= $href ?>"><div class="ic <?= $c ?>"><i class="bi <?= $ic ?>"></i></div><div><b><?= $n ?></b><span><?= $label ?></span></div></a>
  <?php endforeach; ?>
</div>
<div class="row g-4">
  <div class="col-lg-7">
    <div class="panel">
      <div class="panel-h"><h2>Projects awaiting approval</h2><a class="abtn sm ghost" href="project_list.php">Manage</a></div>
      <div class="tbl-wrap"><table class="tbl"><thead><tr><th>Project</th><th>Location</th><th>Submitted by</th><th>Starts</th></tr></thead><tbody>
        <?php if ($pending && $pending->num_rows): while ($p = $pending->fetch_assoc()): ?>
          <tr><td><strong><?= htmlspecialchars($p['name']) ?></strong></td><td><?= htmlspecialchars($p['location']) ?></td><td><?= htmlspecialchars($p['submitted_by'] ?? '—') ?></td><td><?= htmlspecialchars($p['starting_date']) ?></td></tr>
        <?php endwhile; else: ?><tr><td colspan="4" class="empty">Nothing waiting 🎉</td></tr><?php endif; ?>
      </tbody></table></div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="panel">
      <div class="panel-h"><h2>Profile change requests</h2><a class="abtn sm ghost" href="admin_requests.php">Review</a></div>
      <div class="tbl-wrap"><table class="tbl"><thead><tr><th>Farmer</th><th>Change</th></tr></thead><tbody>
        <?php if ($reqs && $reqs->num_rows): while ($r = $reqs->fetch_assoc()): ?>
          <tr><td><?= htmlspecialchars($r['name']) ?></td><td><span class="pill p-warn"><?= $r['field'] === 'email' ? 'Email' : 'Mobile' ?></span> <?= htmlspecialchars($r['new_value']) ?></td></tr>
        <?php endwhile; else: ?><tr><td colspan="2" class="empty">No pending requests</td></tr><?php endif; ?>
      </tbody></table></div>
    </div>
  </div>
</div>
<?php admin_footer(); ?>
