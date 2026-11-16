<?php
ob_start();
$conn = new mysqli("localhost", "root", "", "smart_farming");

function getItems($table, $nameCol, $priceCol) {
    global $conn;
    $result = $conn->query("SELECT $nameCol, $priceCol FROM $table");
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'name' => $row[$nameCol],
            'price' => $row[$priceCol]
        ];
    }
    return $items;
}


$farmers = [];
$result = $conn->query("SELECT id, name FROM farmer");
while ($row = $result->fetch_assoc()) {
    $farmers[$row['id']] = $row['name'];
}

$crops = getItems("crops", "name", "crop_price");
$equipments = getItems("equipments", "name", "rental_price_per_day");
$fertilizer = getItems("fertilizer", "name", "price_per_kg");
$pesticides = getItems("pesticides", "name", "price_per_unit");


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_files'])) {
    $user_id = $_POST['user_id'];
    $land_size = $_POST['land_size'];
    $crop_id = $_POST['crop_id'];
    
    
    $farmer_name = $farmers[$user_id] ?? 'Unknown Farmer';
    
    
    $crop_name = '';
    $crop_price = 0;
    foreach ($crops as $item) {
        if ((array_search($item['name'], array_column($crops, 'name')) + 1) == $crop_id) {
            $crop_name = $item['name'];
            $crop_price = $item['price'];
            break;
        }
    }
    
    
    $crop_cost = $crop_price * $land_size;
    
    
    $equipment_cost = 0;
    $fertilizer_cost = 0;
    $pesticides_cost = 0;
    
    if (!empty($_POST['equipment'])) {
        $equipment_price = getPrice('equipments', $_POST['equipment'], 'rental_price_per_day');
        $equipment_cost = $equipment_price * ($_POST['equipment_qty'] ?? 0);
    }
    
    if (!empty($_POST['fertilizer'])) {
        $fertilizer_price = getPrice('fertilizer', $_POST['fertilizer'], 'price_per_kg');
        $fertilizer_cost = $fertilizer_price * ($_POST['fertilizer_qty'] ?? 0);
    }
    
    if (!empty($_POST['pesticides'])) {
        $pesticides_price = getPrice('pesticides', $_POST['pesticides'], 'price_per_unit');
        $pesticides_cost = $pesticides_price * ($_POST['pesticides_qty'] ?? 0);
    }
    
    $total_cost = $crop_cost + $equipment_cost + $fertilizer_cost + $pesticides_cost;
    

    require('fpdf.php');


    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);

    $pdf->Cell(0, 10, "Farm Cost Report", 0, 1, 'C');
    $pdf->Ln(10);
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(60, 10, "Farmer Name: $farmer_name", 0, 1);
    $pdf->Cell(60, 10, "Land Size: $land_size acres", 0, 1);
    $pdf->Cell(60, 10, "Crop: $crop_name", 0, 1);
    $pdf->Cell(60, 10, "Crop Cost: $crop_cost", 0, 1);
    $pdf->Cell(60, 10, "Equipment: " . ($_POST['equipment'] ?? 'None'), 0, 1);
    $pdf->Cell(60, 10, "Equipment Cost: $equipment_cost", 0, 1);
    $pdf->Cell(60, 10, "Fertilizer: " . ($_POST['fertilizer'] ?? 'None'), 0, 1);
    $pdf->Cell(60, 10, "Fertilizer Cost: $fertilizer_cost", 0, 1);
    $pdf->Cell(60, 10, "Pesticides: " . ($_POST['pesticides'] ?? 'None'), 0, 1);
    $pdf->Cell(60, 10, "Pesticides Cost: $pesticides_cost", 0, 1);
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(60, 10, "Total Cost: $total_cost", 0, 1);
    
    $pdf->Output('D', 'farm_report.pdf');
    exit;
    ob_end_flush();

}

function getPrice($table, $name, $priceColumn) {
    global $conn;
    $stmt = $conn->prepare("SELECT $priceColumn FROM $table WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()[$priceColumn];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cost Calculator - Smart Farming</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/projects.css">
  <link rel="icon" href="images/fevicon.png" type="image/gif" />
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
  <style>
    .calc-wrap{background:#f4f7ea;padding:50px 0 70px}
    .calc-grid{display:grid;grid-template-columns:1.5fr 1fr;gap:28px;align-items:start}
    .calc-card{background:#fff;border-radius:18px;box-shadow:0 10px 30px rgba(0,0,0,.08);padding:32px;color:#1f2a1f}
    .calc-card h3{color:#1f2a1f;font-size:22px;margin:0 0 22px;padding-bottom:12px;border-bottom:2px solid #eef2dc}
    .calc-card h3 i{color:#6b7908;margin-right:8px}
    .calc-field{margin-bottom:18px}
    .calc-field>label{display:block;font-weight:600;color:#1f2a1f;margin-bottom:6px;font-size:14px}
    .calc-row{display:grid;grid-template-columns:1fr 110px;gap:10px}
    .calc-input{width:100%;padding:12px 14px;border:1.5px solid #dfe5cf;border-radius:10px;background:#fff;color:#1f2a1f;font-size:15px;transition:.2s}
    .calc-input:focus{outline:0;border-color:#6b7908;box-shadow:0 0 0 3px rgba(107,121,8,.15)}
    .calc-hint{display:block;font-size:13px;color:#6b7908;font-weight:600;margin-top:5px;min-height:18px}
    .calc-sec{font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:#6b7908;font-weight:700;margin:26px 0 14px}
    .calc-actions{display:flex;gap:12px;margin-top:26px}
    .calc-actions .pj-btn{flex:1;text-align:center;font-size:16px;padding:14px}
    .calc-actions .pj-btn:disabled{background:#c9cfb8;cursor:not-allowed;transform:none}
    .calc-actions .alt{background:#fff;color:#6b7908;border:2px solid #6b7908}
    .calc-actions .alt:hover:not(:disabled){background:#6b7908;color:#fff}
    .calc-actions .alt:disabled{background:#fff;color:#aab08f;border-color:#d5dac3}
    .calc-sum{position:sticky;top:20px;background:linear-gradient(160deg,#6b7908,#3d4a05);color:#fff;border-radius:18px;padding:32px;box-shadow:0 10px 30px rgba(0,0,0,.18)}
    .calc-sum h3{color:#fff;font-size:22px;margin:0 0 20px}
    .calc-line{display:flex;justify-content:space-between;padding:11px 0;border-bottom:1px solid rgba(255,255,255,.18);color:#fff;font-size:15px}
    .calc-line span:last-child{font-weight:600}
    .calc-total{margin-top:22px;background:rgba(255,255,255,.15);border-radius:14px;padding:18px;text-align:center}
    .calc-total small{display:block;color:#e9f0c0;letter-spacing:1px;text-transform:uppercase;font-size:12px}
    .calc-total strong{display:block;color:#fff;font-size:34px;margin-top:4px}
    .calc-empty{color:#e9f0c0;font-size:13px;margin-top:14px;text-align:center}
    @media(max-width:900px){.calc-grid{grid-template-columns:1fr}.calc-sum{position:static}.calc-card{padding:22px}.calc-actions{flex-direction:column}}
  </style>
</head>
<body class="main-layout inner_page">
  <?php include('navbar.html'); ?>

  <section class="page-hero">
    <span class="badge-pill">Cost Calculator</span>
    <h1>Farming Cost Calculator</h1>
    <p>Estimate crop, equipment, fertilizer and pesticide costs for your land, then download a PDF report.</p>
  </section>

  <section class="calc-wrap">
    <div class="container">
      <div class="calc-grid">
        <form id="calculator" class="calc-card" onsubmit="return false">
          <h3><i class="fa fa-calculator"></i>Your Details</h3>
          <div class="calc-field">
            <label>Farmer</label>
            <select id="userSelect" class="calc-input" required>
              <option value="">Select farmer</option>
              <?php foreach ($farmers as $id => $name): ?>
                <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="calc-field">
            <label>Land size (acres)</label>
            <input type="number" id="landSize" class="calc-input" min="0" step="0.1" placeholder="e.g. 2.5" required>
          </div>
          <div class="calc-field">
            <label>Crop</label>
            <select id="cropSelect" class="calc-input" required>
              <option value="">Select crop</option>
              <?php foreach ($crops as $i => $item): ?>
                <option value="<?= $i + 1 ?>" data-price="<?= htmlspecialchars($item['price']) ?>"><?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['price']) ?> BDT/acre)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="calc-sec">Optional inputs</div>
          <?php
          function calcSelector($label, $items, $type, $unit) {
              echo "<div class='calc-field'><label>$label</label><div class='calc-row'>
                    <select id='{$type}Select' class='calc-input'><option value=''>None</option>";
              foreach ($items as $it) {
                  $n = htmlspecialchars($it['name']); $p = htmlspecialchars($it['price']);
                  echo "<option value='$n' data-price='$p'>$n ($p BDT/$unit)</option>";
              }
              echo "</select><input type='number' id='{$type}Qty' class='calc-input' placeholder='Qty' min='0'></div></div>";
          }
          calcSelector("Equipment", $equipments, "equipment", "day");
          calcSelector("Fertilizer", $fertilizer, "fertilizer", "kg");
          calcSelector("Pesticides", $pesticides, "pesticides", "unit");
          ?>
          <div class="calc-actions">
            <button type="button" class="pj-btn" onclick="calculateTotal()">Calculate</button>
            <button type="button" class="pj-btn alt" id="saveBtn" disabled onclick="saveCalculation()"><i class="fa fa-download"></i> Download PDF</button>
          </div>
        </form>

        <aside class="calc-sum">
          <h3><i class="fa fa-leaf"></i> Cost Summary</h3>
          <div class="calc-line"><span>Crop</span><span id="displayCropCost">0.00 BDT</span></div>
          <div class="calc-line"><span>Equipment</span><span id="displayEquipmentCost">0.00 BDT</span></div>
          <div class="calc-line"><span>Fertilizer</span><span id="displayFertilizerCost">0.00 BDT</span></div>
          <div class="calc-line"><span>Pesticides</span><span id="displayPesticidesCost">0.00 BDT</span></div>
          <div class="calc-total"><small>Total cost</small><strong id="total">0.00 BDT</strong></div>
          <div class="calc-empty" id="calcHint">Fill in the form and press Calculate.</div>
        </aside>
      </div>
    </div>
  </section>

  <form id="saveForm" method="POST" style="display:none">
    <?php foreach (['user_id','land_size','crop_id','equipment','equipment_qty','fertilizer','fertilizer_qty','pesticides','pesticides_qty','total_cost'] as $f): ?>
      <input type="hidden" name="<?= $f ?>" id="save_<?= $f ?>">
    <?php endforeach; ?>
    <input type="hidden" name="generate_files" value="1">
  </form>

  <?php include 'footer.php'; ?>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script>
    const $ = id => document.getElementById(id);
    const priceOf = id => parseFloat($(id).selectedOptions[0]?.dataset.price) || 0;
    const fmt = n => n.toFixed(2) + ' BDT';
    let calculationData = null;

    function calculateTotal() {
      const land = parseFloat($('landSize').value) || 0;
      const cost = { crop: priceOf('cropSelect') * land };
      ['equipment','fertilizer','pesticides'].forEach(t => {
        cost[t] = priceOf(t + 'Select') * (parseFloat($(t + 'Qty').value) || 0);
      });
      const total = cost.crop + cost.equipment + cost.fertilizer + cost.pesticides;
      $('displayCropCost').innerText = fmt(cost.crop);
      $('displayEquipmentCost').innerText = fmt(cost.equipment);
      $('displayFertilizerCost').innerText = fmt(cost.fertilizer);
      $('displayPesticidesCost').innerText = fmt(cost.pesticides);
      $('total').innerText = fmt(total);

      const ready = $('userSelect').value && land > 0 && $('cropSelect').value;
      calculationData = ready ? {
        user_id: $('userSelect').value, land_size: land, crop_id: $('cropSelect').value,
        equipment: $('equipmentSelect').value, equipment_qty: $('equipmentQty').value,
        fertilizer: $('fertilizerSelect').value, fertilizer_qty: $('fertilizerQty').value,
        pesticides: $('pesticidesSelect').value, pesticides_qty: $('pesticidesQty').value,
        total_cost: total
      } : null;
      $('saveBtn').disabled = !ready;
      $('calcHint').innerText = ready ? 'Looks good — you can download the PDF report.' : 'Select a farmer, land size and crop to enable the report.';
    }

    function saveCalculation() {
      if (!calculationData) return;
      for (const k in calculationData) $('save_' + k).value = calculationData[k];
      $('saveForm').submit();
    }

    // live update + invalidate stale report when inputs change
    document.querySelectorAll('#calculator select, #calculator input').forEach(el => {
      el.addEventListener('input', calculateTotal);
      el.addEventListener('change', calculateTotal);
    });
  </script>
</body>
</html>
