<?php
require_once __DIR__ . '/admin_layout.php';
$conn->set_charset('utf8mb4');
$mn = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$msg = ''; $err = false;

function months($arr) {
    $m = array_unique(array_filter(array_map('intval', (array)$arr), fn($x) => $x >= 1 && $x <= 12));
    sort($m);
    return implode(',', $m);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_crop'])) {
        $id = (int)($_POST['cc_id'] ?? 0);
        $name = trim($_POST['name'] ?? ''); $season = $_POST['season'] ?? '';
        $type = trim($_POST['type'] ?? ''); $soil = trim($_POST['soil'] ?? '');
        $sow = months($_POST['sow'] ?? []); $har = months($_POST['harvest'] ?? []);
        $tip = trim($_POST['tip'] ?? '');
        if (!$name || !$type || !$soil || !in_array($season, ['Rabi', 'Kharif-1', 'Kharif-2'], true)) {
            $msg = 'Please fill in name, season, type and soil.'; $err = true;
        } elseif ($sow === '' && $har === '') {
            $msg = 'Select at least one sowing or harvest month.'; $err = true;
        } else {
            if ($id) {
                $s = $conn->prepare("UPDATE crop_calendar SET name=?, season=?, type=?, soil=?, sow_months=?, harvest_months=?, tip=? WHERE cc_id=?");
                $s->bind_param("sssssssi", $name, $season, $type, $soil, $sow, $har, $tip, $id);
            } else {
                $s = $conn->prepare("INSERT INTO crop_calendar (name, season, type, soil, sow_months, harvest_months, tip) VALUES (?,?,?,?,?,?,?)");
                $s->bind_param("sssssss", $name, $season, $type, $soil, $sow, $har, $tip);
            }
            if ($s->execute()) { $msg = $id ? 'Crop updated.' : 'Crop added to the calendar.'; }
            else { $err = true; $msg = $conn->errno == 1062 ? 'A crop with this name already exists.' : 'Could not save: ' . $conn->error; }
        }
    } elseif (isset($_POST['delete_id'])) {
        $s = $conn->prepare("DELETE FROM crop_calendar WHERE cc_id = ?");
        $s->bind_param("i", $_POST['delete_id']);
        $s->execute();
        $msg = 'Crop removed.';
    }
}
$rows = $conn->query("SELECT * FROM crop_calendar ORDER BY FIELD(season,'Rabi','Kharif-1','Kharif-2'), name");
$list = $rows ? $rows->fetch_all(MYSQLI_ASSOC) : [];
$fmt = function ($csv) use ($mn) {
    $o = [];
    foreach (array_filter(explode(',', $csv), 'strlen') as $m) $o[] = $mn[(int)$m];
    return $o ? implode(', ', $o) : '—';
};
admin_header('Crop Calendar', 'crops');
?>
<?php if ($msg): ?><div class="alert-a <?= $err ? 'err' : '' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<div class="panel">
  <div class="panel-h"><h2>Crops shown to farmers (<?= count($list) ?>)</h2>
    <div class="d-flex gap-2"><input class="search" data-filter="#t" placeholder="Search..."><button class="abtn" onclick="openCrop()"><i class="bi bi-plus-lg"></i> Add crop</button></div></div>
  <div class="tbl-wrap"><table class="tbl" id="t">
    <thead><tr><th>Crop</th><th>Season</th><th>Type</th><th>Soil</th><th>Sowing</th><th>Harvest</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($list as $r): ?>
      <tr>
        <td><strong><?= htmlspecialchars($r['name']) ?></strong><br><small class="text-muted"><?= htmlspecialchars(mb_strimwidth($r['tip'], 0, 50, '…')) ?></small></td>
        <td><span class="pill p-ok"><?= $r['season'] ?></span></td>
        <td><?= htmlspecialchars($r['type']) ?></td>
        <td><?= htmlspecialchars($r['soil']) ?></td>
        <td><small><?= $fmt($r['sow_months']) ?></small></td>
        <td><small><?= $fmt($r['harvest_months']) ?></small></td>
        <td class="text-nowrap">
          <button class="abtn sm ghost" onclick='openCrop(<?= json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) ?>)'><i class="bi bi-pencil"></i> Edit</button>
          <form method="POST" class="inl" onsubmit="return confirm('Remove this crop from the calendar?')"><input type="hidden" name="delete_id" value="<?= (int)$r['cc_id'] ?>"><button class="abtn sm red"><i class="bi bi-trash"></i></button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$list): ?><tr><td colspan="7" class="empty">No crops yet. Add the first one.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>

<div class="modal fade" id="cropM" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><form method="POST" class="modal-content">
  <div class="modal-header"><h5 class="modal-title" id="cropTitle">Add crop</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <input type="hidden" name="cc_id" id="f_id" value="0">
    <div class="row g-2">
      <div class="col-md-6"><input class="fi" name="name" id="f_name" placeholder="Crop name (e.g. Brinjal)" required></div>
      <div class="col-md-3"><select class="fi" name="season" id="f_season"><option>Rabi</option><option>Kharif-1</option><option>Kharif-2</option></select></div>
      <div class="col-md-3"><input class="fi" name="type" id="f_type" placeholder="Type (Cereal…)" required></div>
    </div>
    <input class="fi" name="soil" id="f_soil" placeholder="Suitable soil (e.g. Loamy, well-drained)" required>
    <div class="mb-1"><strong style="color:#6b7908">Sowing months</strong></div>
    <div class="mrow" id="sowRow"><?php for ($m = 1; $m <= 12; $m++): ?><label><input type="checkbox" name="sow[]" value="<?= $m ?>"><span><?= $mn[$m] ?></span></label><?php endfor; ?></div>
    <div class="mb-1 mt-3"><strong style="color:#d97706">Harvest months</strong></div>
    <div class="mrow harv" id="harRow"><?php for ($m = 1; $m <= 12; $m++): ?><label><input type="checkbox" name="harvest[]" value="<?= $m ?>"><span><?= $mn[$m] ?></span></label><?php endfor; ?></div>
    <textarea class="fi mt-3" name="tip" id="f_tip" rows="2" maxlength="300" placeholder="Farming tip shown to farmers"></textarea>
  </div>
  <div class="modal-footer"><button class="abtn" name="save_crop">Save crop</button></div>
</form></div></div>
<style>
  .mrow{display:grid;grid-template-columns:repeat(6,1fr);gap:6px}
  .mrow label{margin:0;cursor:pointer}.mrow input{display:none}
  .mrow span{display:block;text-align:center;padding:7px 0;border-radius:8px;background:#eef1e2;color:#6a7552;font-size:13px;font-weight:600;transition:.15s}
  .mrow input:checked+span{background:#8aa10b;color:#fff}.mrow.harv input:checked+span{background:#f59e0b}
</style>
<script>
function openCrop(c) {
  document.getElementById('cropTitle').textContent = c ? 'Edit crop' : 'Add crop';
  document.getElementById('f_id').value = c ? c.cc_id : 0;
  document.getElementById('f_name').value = c ? c.name : '';
  document.getElementById('f_season').value = c ? c.season : 'Rabi';
  document.getElementById('f_type').value = c ? c.type : '';
  document.getElementById('f_soil').value = c ? c.soil : '';
  document.getElementById('f_tip').value = c ? c.tip : '';
  var sow = c ? c.sow_months.split(',') : [], har = c ? c.harvest_months.split(',') : [];
  document.querySelectorAll('#sowRow input').forEach(function (i) { i.checked = sow.indexOf(i.value) > -1; });
  document.querySelectorAll('#harRow input').forEach(function (i) { i.checked = har.indexOf(i.value) > -1; });
  new bootstrap.Modal(document.getElementById('cropM')).show();
}
</script>
<?php admin_footer(); ?>
