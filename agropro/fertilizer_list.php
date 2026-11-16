<?php
require_once __DIR__ . '/admin_layout.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_fert'])) {
        $s = $conn->prepare("INSERT INTO fertilizer (name, `usage`, type, nflag, pflag, poflag, price_per_kg, availability, market_name) VALUES (?,?,?,?,?,?,?,?,?)");
        $s->bind_param("ssssssdss", $_POST['name'], $_POST['usage'], $_POST['type'], $_POST['nflag'], $_POST['pflag'], $_POST['poflag'], $_POST['price'], $_POST['availability'], $_POST['market_name']);
        $s->execute();
    } elseif (isset($_POST['delete_name'])) {
        $s = $conn->prepare("DELETE FROM fertilizer WHERE name = ?");
        $s->bind_param("s", $_POST['delete_name']);
        $s->execute();
    } elseif (isset($_POST['update_fert'])) {
        $s = $conn->prepare("UPDATE fertilizer SET availability = ? WHERE name = ?");
        $s->bind_param("ss", $_POST['availability'], $_POST['name']);
        $s->execute();
    }
    header("Location: fertilizer_list.php"); exit();
}
$rows = $conn->query("SELECT * FROM fertilizer ORDER BY name");
admin_header('Fertilizer', 'fertilizer');
?>
<div class="panel">
  <div class="panel-h"><h2>All fertilizers (<?= $rows->num_rows ?>)</h2>
    <div class="d-flex gap-2"><input class="search" data-filter="#t" placeholder="Search..."><button class="abtn" data-bs-toggle="modal" data-bs-target="#addM"><i class="bi bi-plus-lg"></i> Add fertilizer</button></div></div>
  <div class="tbl-wrap"><table class="tbl" id="t">
    <thead><tr><th>Name</th><th>Type</th><th>Usage</th><th>N / P / K</th><th>Price / kg</th><th>Market</th><th>Availability</th><th></th></tr></thead>
    <tbody>
    <?php while ($r = $rows->fetch_assoc()): ?>
      <tr>
        <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
        <td><span class="pill <?= $r['type'] === 'Organic' ? 'p-ok' : 'p-warn' ?>"><?= htmlspecialchars($r['type']) ?></span></td>
        <td><?= htmlspecialchars($r['usage']) ?></td>
        <td><?= htmlspecialchars(($r['nflag'] ?: '-') . ' / ' . ($r['pflag'] ?: '-') . ' / ' . ($r['poflag'] ?: '-')) ?></td>
        <td>৳<?= number_format((float)$r['price_per_kg'], 2) ?></td>
        <td><?= htmlspecialchars($r['market_name'] ?? '—') ?></td>
        <td>
          <form method="POST" class="inl"><input type="hidden" name="name" value="<?= htmlspecialchars($r['name']) ?>">
            <select name="availability"><?php foreach (['Available','Unavailable'] as $o): ?><option <?= $r['availability'] === $o ? 'selected' : '' ?>><?= $o ?></option><?php endforeach; ?></select>
            <button class="abtn sm ghost" name="update_fert">Save</button></form>
        </td>
        <td><form method="POST" class="inl" onsubmit="return confirm('Delete this fertilizer?')"><input type="hidden" name="delete_name" value="<?= htmlspecialchars($r['name']) ?>"><button class="abtn sm red"><i class="bi bi-trash"></i></button></form></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table></div>
</div>
<div class="modal fade" id="addM" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="POST" class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Add fertilizer</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input class="fi" name="name" placeholder="Name" required>
    <input class="fi" name="usage" placeholder="Usage" required>
    <input class="fi" name="type" placeholder="Type (Organic / Chemical)" required>
    <div class="row g-2"><div class="col"><input class="fi" name="nflag" placeholder="N"></div><div class="col"><input class="fi" name="pflag" placeholder="P"></div><div class="col"><input class="fi" name="poflag" placeholder="K"></div></div>
    <input class="fi" type="number" step="0.01" min="0" name="price" placeholder="Price per kg (BDT)" required>
    <select class="fi" name="availability"><option>Available</option><option>Unavailable</option></select>
    <input class="fi" name="market_name" placeholder="Market name">
  </div>
  <div class="modal-footer"><button class="abtn" name="add_fert">Add</button></div>
</form></div></div>
<?php admin_footer(); ?>
