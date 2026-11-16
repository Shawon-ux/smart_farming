<?php
require_once __DIR__ . '/admin_layout.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_pest'])) {
        $s = $conn->prepare("INSERT INTO pesticides (name, type, expired_date, price_per_unit, availability, market_name) VALUES (?,?,?,?,?,?)");
        $s->bind_param("sssdss", $_POST['name'], $_POST['type'], $_POST['expired_date'], $_POST['price'], $_POST['availability'], $_POST['market_name']);
        $s->execute();
    } elseif (isset($_POST['delete_name'])) {
        $s = $conn->prepare("DELETE FROM pesticides WHERE name = ?");
        $s->bind_param("s", $_POST['delete_name']);
        $s->execute();
    } elseif (isset($_POST['update_pest'])) {
        $s = $conn->prepare("UPDATE pesticides SET availability = ? WHERE name = ?");
        $s->bind_param("ss", $_POST['availability'], $_POST['name']);
        $s->execute();
    }
    header("Location: pesticide_list.php"); exit();
}
$rows = $conn->query("SELECT * FROM pesticides ORDER BY name");
admin_header('Pesticide', 'pesticide');
?>
<div class="panel">
  <div class="panel-h"><h2>All pesticides (<?= $rows->num_rows ?>)</h2>
    <div class="d-flex gap-2"><input class="search" data-filter="#t" placeholder="Search..."><button class="abtn" data-bs-toggle="modal" data-bs-target="#addM"><i class="bi bi-plus-lg"></i> Add pesticide</button></div></div>
  <div class="tbl-wrap"><table class="tbl" id="t">
    <thead><tr><th>Name</th><th>Type</th><th>Expiry</th><th>Price / unit</th><th>Market</th><th>Availability</th><th></th></tr></thead>
    <tbody>
    <?php while ($r = $rows->fetch_assoc()): $exp = $r['expired_date'] && $r['expired_date'] < date('Y-m-d'); ?>
      <tr>
        <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
        <td><?= htmlspecialchars($r['type']) ?></td>
        <td><?= htmlspecialchars($r['expired_date']) ?> <?php if ($exp): ?><span class="pill p-bad">Expired</span><?php endif; ?></td>
        <td>৳<?= number_format((float)$r['price_per_unit'], 2) ?></td>
        <td><?= htmlspecialchars($r['market_name'] ?? '—') ?></td>
        <td>
          <form method="POST" class="inl"><input type="hidden" name="name" value="<?= htmlspecialchars($r['name']) ?>">
            <select name="availability"><?php foreach (['Available','Unavailable'] as $o): ?><option <?= $r['availability'] === $o ? 'selected' : '' ?>><?= $o ?></option><?php endforeach; ?></select>
            <button class="abtn sm ghost" name="update_pest">Save</button></form>
        </td>
        <td><form method="POST" class="inl" onsubmit="return confirm('Delete this pesticide?')"><input type="hidden" name="delete_name" value="<?= htmlspecialchars($r['name']) ?>"><button class="abtn sm red"><i class="bi bi-trash"></i></button></form></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table></div>
</div>
<div class="modal fade" id="addM" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="POST" class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Add pesticide</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input class="fi" name="name" placeholder="Name" required>
    <input class="fi" name="type" placeholder="Type (Fungicide / Insecticide...)" required>
    <input class="fi" type="date" name="expired_date" required>
    <input class="fi" type="number" step="0.01" min="0" name="price" placeholder="Price per unit (BDT)" required>
    <select class="fi" name="availability"><option>Available</option><option>Unavailable</option></select>
    <input class="fi" name="market_name" placeholder="Market name">
  </div>
  <div class="modal-footer"><button class="abtn" name="add_pest">Add</button></div>
</form></div></div>
<?php admin_footer(); ?>
