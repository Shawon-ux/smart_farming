<?php
require_once __DIR__ . '/admin_layout.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_project'])) {
        $s = $conn->prepare("INSERT INTO project (name, starting_date, ending_date, location, description, number, status) VALUES (?,?,?,?,?,?,'Approved')");
        $s->bind_param("ssssss", $_POST['proj_name'], $_POST['start_date'], $_POST['end_date'], $_POST['location'], $_POST['description'], $_POST['number']);
        $s->execute();
    } elseif (isset($_POST['delete_name'])) {
        $s = $conn->prepare("DELETE FROM project WHERE name = ?");
        $s->bind_param("s", $_POST['delete_name']);
        $s->execute();
    } elseif (isset($_POST['status'], $_POST['name'])) {
        $st = $_POST['status'] === 'Approved' ? 'Approved' : 'Unapproved';
        $s = $conn->prepare("UPDATE project SET status = ? WHERE name = ?");
        $s->bind_param("ss", $st, $_POST['name']);
        $s->execute();
    }
    header("Location: project_list.php"); exit();
}
$rows = $conn->query("SELECT * FROM project ORDER BY (status='Unapproved') DESC, starting_date DESC");
admin_header('Projects', 'projects');
?>
<div class="panel">
  <div class="panel-h"><h2>All projects (<?= $rows->num_rows ?>)</h2>
    <div class="d-flex gap-2"><input class="search" data-filter="#t" placeholder="Search..."><button class="abtn" data-bs-toggle="modal" data-bs-target="#addM"><i class="bi bi-plus-lg"></i> Add project</button></div></div>
  <div class="tbl-wrap"><table class="tbl" id="t">
    <thead><tr><th>Project</th><th>Location</th><th>Dates</th><th>Contact</th><th>Submitted by</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php while ($r = $rows->fetch_assoc()): $ap = $r['status'] === 'Approved'; ?>
      <tr>
        <td><strong><?= htmlspecialchars($r['name']) ?></strong><br><small class="text-muted"><?= htmlspecialchars(mb_strimwidth($r['description'], 0, 60, '…')) ?></small></td>
        <td><?= htmlspecialchars($r['location']) ?></td>
        <td><small><?= htmlspecialchars($r['starting_date']) ?><br>→ <?= htmlspecialchars($r['ending_date']) ?></small></td>
        <td><?= htmlspecialchars($r['number']) ?></td>
        <td><?= htmlspecialchars($r['submitted_by'] ?? '—') ?><?php if (!empty($r['email'])): ?><br><small class="text-muted"><?= htmlspecialchars($r['email']) ?></small><?php endif; ?></td>
        <td><span class="pill <?= $ap ? 'p-ok' : 'p-warn' ?>"><?= $ap ? 'Approved' : 'Pending' ?></span></td>
        <td class="text-nowrap">
          <form method="POST" class="inl"><input type="hidden" name="name" value="<?= htmlspecialchars($r['name']) ?>">
            <?php if ($ap): ?><button class="abtn sm ghost" name="status" value="Unapproved">Unapprove</button>
            <?php else: ?><button class="abtn sm" name="status" value="Approved"><i class="bi bi-check-lg"></i> Approve</button><?php endif; ?></form>
          <form method="POST" class="inl" onsubmit="return confirm('Delete this project?')"><input type="hidden" name="delete_name" value="<?= htmlspecialchars($r['name']) ?>"><button class="abtn sm red"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table></div>
</div>
<div class="modal fade" id="addM" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="POST" class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Add project (published immediately)</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input class="fi" name="proj_name" placeholder="Project name" required>
    <div class="row g-2"><div class="col"><input class="fi" type="date" name="start_date" required></div><div class="col"><input class="fi" type="date" name="end_date" required></div></div>
    <input class="fi" name="location" placeholder="Location" required>
    <textarea class="fi" name="description" rows="3" maxlength="500" placeholder="Description" required></textarea>
    <input class="fi" name="number" placeholder="Phone number" required>
  </div>
  <div class="modal-footer"><button class="abtn" name="add_project">Add</button></div>
</form></div></div>
<?php admin_footer(); ?>
