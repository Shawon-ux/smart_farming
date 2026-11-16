<?php
require_once __DIR__ . '/admin_layout.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_equipment'])) {
        $s = $conn->prepare("INSERT INTO equipments (name, type, model_number, rental_price_per_day, availability, market_name) VALUES (?,?,?,?,?,?)");
        $s->bind_param("sssdss", $_POST['eq_name'], $_POST['eq_type'], $_POST['model_number'], $_POST['price'], $_POST['availability'], $_POST['market_name']);
        $s->execute();
    } elseif (isset($_POST['delete_name'])) {
        $s = $conn->prepare("DELETE FROM equipments WHERE name = ?");
        $s->bind_param("s", $_POST['delete_name']);
        $s->execute();
    } elseif (isset($_POST['update_equipment'])) {
        $s = $conn->prepare("UPDATE equipments SET availability = ? WHERE name = ?");
        $s->bind_param("ss", $_POST['availability'], $_POST['name']);
        $s->execute();
    }
    header("Location: equipment_list.php"); exit();
}
$rows = $conn->query("SELECT * FROM equipments ORDER BY name");
admin_header('Equipment', 'equipment');
?>
<div class="panel">
  <div class="panel-h"><h2>All equipment (<?= $rows->num_rows ?>)</h2>
    <div class="d-flex gap-2"><input class="search" data-filter="#t" placeholder="Search..."><button class="abtn" data-bs-toggle="modal" data-bs-target="#addM"><i class="bi bi-plus-lg"></i> Add equipment</button></div></div>
  <div class="tbl-wrap"><table class="tbl" id="t">
    <thead><tr><th>Name</th><th>Type</th><th>Model</th><th>Price / day</th><th>Market</th><th>Availability</th><th></th></tr></thead>
    <tbody>
    <?php while ($r = $rows->fetch_assoc()): ?>
      <tr>
        <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
        <td><?= htmlspecialchars($r['type']) ?></td>
        <td><?= htmlspecialchars($r['model_number']) ?></td>
        <td>৳<?= number_format((float)$r['rental_price_per_day'], 2) ?></td>
        <td><?= htmlspecialchars($r['market_name'] ?? '—') ?></td>
        <td>
          <form method="POST" class="inl"><input type="hidden" name="name" value="<?= htmlspecialchars($r['name']) ?>">
            <select name="availability"><?php foreach (['Available','Unavailable'] as $o): ?><option <?= $r['availability'] === $o ? 'selected' : '' ?>><?= $o ?></option><?php endforeach; ?></select>
            <button class="abtn sm ghost" name="update_equipment">Save</button></form>
        </td>
        <td><form method="POST" class="inl" onsubmit="return confirm('Delete this item?')"><input type="hidden" name="delete_name" value="<?= htmlspecialchars($r['name']) ?>"><button class="abtn sm red"><i class="bi bi-trash"></i></button></form></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table></div>
</div>
<div class="modal fade" id="addM" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="POST" class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Add equipment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input class="fi" name="eq_name" placeholder="Name" required>
    <input class="fi" name="eq_type" placeholder="Type" required>
    <input class="fi" name="model_number" placeholder="Model number" required>
    <input class="fi" type="number" step="0.01" min="0" name="price" placeholder="Rental price per day (BDT)" required>
    <select class="fi" name="availability"><option>Available</option><option>Unavailable</option></select>
    <input class="fi" name="market_name" placeholder="Market name">
  </div>
  <div class="modal-footer"><button class="abtn" name="add_equipment">Add</button></div>
</form></div></div>
<?php admin_footer(); ?>
