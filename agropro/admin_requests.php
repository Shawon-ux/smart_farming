<?php
require_once __DIR__ . '/admin_layout.php';
$msg = ''; $err = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['req_id'], $_POST['action'])) {
    $rid = (int)$_POST['req_id'];
    $s = $conn->prepare("SELECT * FROM profile_change_request WHERE req_id=? AND status='Pending'");
    $s->bind_param("i", $rid);
    $s->execute();
    $rq = $s->get_result()->fetch_assoc();
    if ($rq) {
        if ($_POST['action'] === 'approve') {
            $col = $rq['field'] === 'email' ? 'email' : 'mb_num';  // whitelisted
            $u = $conn->prepare("UPDATE farmer SET $col = ? WHERE id = ?");
            $u->bind_param("si", $rq['new_value'], $rq['farmer_id']);
            if ($u->execute()) {
                $conn->query("UPDATE profile_change_request SET status='Approved' WHERE req_id=$rid");
                $msg = 'Approved and applied.';
            } else {
                $err = true;
                $msg = 'Could not apply: ' . ($conn->errno == 1062 ? 'that value is already used by another farmer.' : $conn->error);
            }
        } else {
            $conn->query("UPDATE profile_change_request SET status='Rejected' WHERE req_id=$rid");
            $msg = 'Request rejected.';
        }
    }
}
$res = $conn->query("SELECT r.*, f.name, f.mb_num, f.email FROM profile_change_request r JOIN farmer f ON f.id = r.farmer_id ORDER BY (r.status='Pending') DESC, r.req_id DESC");
admin_header('Profile Requests', 'requests');
?>
<?php if ($msg): ?><div class="alert-a <?= $err ? 'err' : '' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<div class="panel">
  <div class="panel-h"><h2>Phone &amp; email change requests</h2></div>
  <div class="tbl-wrap"><table class="tbl">
    <thead><tr><th>Farmer</th><th>Field</th><th>Current</th><th>Requested</th><th>Date</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php if ($res && $res->num_rows): while ($r = $res->fetch_assoc()): $cls = ['Pending'=>'p-warn','Approved'=>'p-ok','Rejected'=>'p-bad'][$r['status']]; ?>
      <tr>
        <td><strong><?= htmlspecialchars($r['name']) ?></strong> <small class="text-muted">#<?= (int)$r['farmer_id'] ?></small></td>
        <td><?= $r['field'] === 'email' ? 'Email' : 'Mobile' ?></td>
        <td><?= htmlspecialchars($r['field'] === 'email' ? ($r['email'] ?? '—') : $r['mb_num']) ?></td>
        <td><strong><?= htmlspecialchars($r['new_value']) ?></strong></td>
        <td><small><?= htmlspecialchars($r['created_at']) ?></small></td>
        <td><span class="pill <?= $cls ?>"><?= $r['status'] ?></span></td>
        <td class="text-nowrap">
          <?php if ($r['status'] === 'Pending'): ?>
          <form method="POST" class="inl"><input type="hidden" name="req_id" value="<?= (int)$r['req_id'] ?>">
            <button class="abtn sm" name="action" value="approve"><i class="bi bi-check-lg"></i> Approve</button>
            <button class="abtn sm red" name="action" value="reject">Reject</button></form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; else: ?><tr><td colspan="7" class="empty">No requests yet.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>
<?php admin_footer(); ?>
