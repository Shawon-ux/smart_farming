<?php
require_once __DIR__ . '/admin_layout.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $s = $conn->prepare("DELETE FROM farmer WHERE id = ?");
    $s->bind_param("i", $_POST['delete_id']);
    $s->execute();
    header("Location: user_list.php"); exit();
}
$farmers = $conn->query("SELECT * FROM farmer ORDER BY serialno");
admin_header('Farmers', 'farmers');
?>
<div class="panel">
  <div class="panel-h"><h2>Registered farmers (<?= $farmers->num_rows ?>)</h2><input class="search" data-filter="#t" placeholder="Search farmers..."></div>
  <div class="tbl-wrap"><table class="tbl" id="t">
    <thead><tr><th>#</th><th>Farmer</th><th>ID</th><th>Mobile</th><th>Email</th><th>Address</th><th>Training</th><th></th></tr></thead>
    <tbody>
    <?php while ($u = $farmers->fetch_assoc()): ?>
      <tr>
        <td><?= (int)$u['serialno'] ?></td>
        <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
        <td><?= (int)$u['id'] ?></td>
        <td><?= htmlspecialchars($u['mb_num']) ?></td>
        <td><?= htmlspecialchars($u['email'] ?? '—') ?></td>
        <td><?= htmlspecialchars($u['address']) ?></td>
        <td><span class="pill p-ok"><?= htmlspecialchars($u['training_session']) ?></span></td>
        <td><form method="POST" class="inl" onsubmit="return confirm('Delete this farmer?')"><input type="hidden" name="delete_id" value="<?= (int)$u['id'] ?>"><button class="abtn sm red"><i class="bi bi-trash"></i></button></form></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table></div>
</div>
<?php admin_footer(); ?>
