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

// Get farmers for user selection
$farmers = [];
$result = $conn->query("SELECT id, name FROM farmer");
while ($row = $result->fetch_assoc()) {
    $farmers[$row['id']] = $row['name'];
}

$crops = getItems("crops", "name", "crop_price");
$equipments = getItems("equipments", "name", "rental_price_per_day");
$fertilizer = getItems("fertilizer", "name", "price_per_kg");
$pesticides = getItems("pesticides", "name", "price_per_unit");

// Handle file generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_files'])) {
    $user_id = $_POST['user_id'];
    $land_size = $_POST['land_size'];
    $crop_id = $_POST['crop_id'];
    
    // Get farmer name
    $farmer_name = $farmers[$user_id] ?? 'Unknown Farmer';
    
    // Get crop name and price
    $crop_name = '';
    $crop_price = 0;
    foreach ($crops as $item) {
        if ((array_search($item['name'], array_column($crops, 'name')) + 1) == $crop_id) {
            $crop_name = $item['name'];
            $crop_price = $item['price'];
            break;
        }
    }
    
    // Calculate CROP cost (price per unit × land size)
    $crop_cost = $crop_price * $land_size;
    
    // Calculate other costs
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


  
    $text_content = "FARMING COST CALCULATION REPORT\n";
    $text_content .= "===============================\n";
    $text_content .= "Farmer: $farmer_name\n";
    $text_content .= "Date: " . date('Y-m-d H:i:s') . "\n\n";
    $text_content .= "CROP DETAILS\n";
    $text_content .= "------------\n";
    $text_content .= "Crop: $crop_name\n";
    $text_content .= "Land Size: $land_size acres\n";
    $text_content .= "Crop Price (per acre): $crop_price BDT\n";
    $text_content .= "Total Crop Cost: $crop_cost BDT\n\n";
    
    if ($equipment_cost > 0) {
        $text_content .= "EQUIPMENT\n";
        $text_content .= "---------\n";
        $text_content .= "Equipment: {$_POST['equipment']}\n";
        $text_content .= "Quantity: {$_POST['equipment_qty']} days\n";
        $text_content .= "Daily Rate: $equipment_price BDT\n";
        $text_content .= "Total Equipment Cost: $equipment_cost BDT\n\n";
    }
    
    if ($fertilizer_cost > 0) {
        $text_content .= "FERTILIZER\n";
        $text_content .= "----------\n";
        $text_content .= "Fertilizer: {$_POST['fertilizer']}\n";
        $text_content .= "Quantity: {$_POST['fertilizer_qty']} kg\n";
        $text_content .= "Price per kg: $fertilizer_price BDT\n";
        $text_content .= "Total Fertilizer Cost: $fertilizer_cost BDT\n\n";
    }
    
    if ($pesticides_cost > 0) {
        $text_content .= "PESTICIDES\n";
        $text_content .= "---------\n";
        $text_content .= "Pesticides: {$_POST['pesticides']}\n";
        $text_content .= "Quantity: {$_POST['pesticides_qty']} units\n";
        $text_content .= "Price per unit: $pesticides_price BDT\n";
        $text_content .= "Total Pesticides Cost: $pesticides_cost BDT\n\n";
    }
    
    $text_content .= "TOTAL COST: $total_cost BDT\n";
    

    $image = imagecreatetruecolor(800, 600);
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $green = imagecolorallocate($image, 0, 100, 0);
    $darkGreen = imagecolorallocate($image, 0, 80, 0);
    
    imagefill($image, 0, 0, $white);
    

    imagettftext($image, 24, 0, 50, 50, $darkGreen, 'arial.ttf', "FARMING COST CALCULATION");
    

    imagettftext($image, 14, 0, 50, 90, $black, 'arial.ttf', "Farmer: $farmer_name");
    imagettftext($image, 14, 0, 50, 120, $black, 'arial.ttf', "Date: " . date('Y-m-d H:i:s'));
    
  
    $y = 170;
    imagettftext($image, 16, 0, 50, $y, $darkGreen, 'arial.ttf', "CROP DETAILS");
    imagettftext($image, 14, 0, 50, $y + 30, $black, 'arial.ttf', "Crop: $crop_name");
    imagettftext($image, 14, 0, 50, $y + 60, $black, 'arial.ttf', "Land Size: $land_size acres");
    imagettftext($image, 14, 0, 50, $y + 90, $black, 'arial.ttf', "Price per acre: $crop_price BDT");
    imagettftext($image, 14, 0, 400, $y + 90, $black, 'arial.ttf', "Total: $crop_cost BDT");
    
    $y += 130;
    
   
    if ($equipment_cost > 0) {
        imagettftext($image, 16, 0, 50, $y, $darkGreen, 'arial.ttf', "EQUIPMENT");
        imagettftext($image, 14, 0, 50, $y + 30, $black, 'arial.ttf', "Equipment: {$_POST['equipment']}");
        imagettftext($image, 14, 0, 50, $y + 60, $black, 'arial.ttf', "Days: {$_POST['equipment_qty']}");
        imagettftext($image, 14, 0, 50, $y + 90, $black, 'arial.ttf', "Daily Rate: $equipment_price BDT");
        imagettftext($image, 14, 0, 400, $y + 90, $black, 'arial.ttf', "Total: $equipment_cost BDT");
        $y += 130;
    }
    
   
    if ($fertilizer_cost > 0) {
        imagettftext($image, 16, 0, 50, $y, $darkGreen, 'arial.ttf', "FERTILIZER");
        imagettftext($image, 14, 0, 50, $y + 30, $black, 'arial.ttf', "Type: {$_POST['fertilizer']}");
        imagettftext($image, 14, 0, 50, $y + 60, $black, 'arial.ttf', "Quantity: {$_POST['fertilizer_qty']} kg");
        imagettftext($image, 14, 0, 50, $y + 90, $black, 'arial.ttf', "Price per kg: $fertilizer_price BDT");
        imagettftext($image, 14, 0, 400, $y + 90, $black, 'arial.ttf', "Total: $fertilizer_cost BDT");
        $y += 130;
    }
    

    if ($pesticides_cost > 0) {
        imagettftext($image, 16, 0, 50, $y, $darkGreen, 'arial.ttf', "PESTICIDES");
        imagettftext($image, 14, 0, 50, $y + 30, $black, 'arial.ttf', "Type: {$_POST['pesticides']}");
        imagettftext($image, 14, 0, 50, $y + 60, $black, 'arial.ttf', "Quantity: {$_POST['pesticides_qty']} units");
        imagettftext($image, 14, 0, 50, $y + 90, $black, 'arial.ttf', "Price per unit: $pesticides_price BDT");
        imagettftext($image, 14, 0, 400, $y + 90, $black, 'arial.ttf', "Total: $pesticides_cost BDT");
        $y += 130;
    }
    
    
    imagettftext($image, 20, 0, 50, $y + 30, $darkGreen, 'arial.ttf', "TOTAL COST:");
    imagettftext($image, 20, 0, 400, $y + 30, $darkGreen, 'arial.ttf', "$total_cost BDT");
    
    
    $timestamp = time();
    $text_filename = "calculation_{$timestamp}.txt";
    $image_filename = "calculation_{$timestamp}.png";
    

    file_put_contents($text_filename, $text_content);
    imagepng($image, $image_filename);
    imagedestroy($image);
    
  
    $download_links = [
        'text' => $text_filename,
        'image' => $image_filename
    ];
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
<html>
<head>
    <title>Farming Calculator</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #e0f2f1);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        .container img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            color: #00796B;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 10px;
        }

        .form-group label {
            width: 100px;
            font-weight: bold;
            color: #333;
        }

        select, input[type=number] {
            flex: 1;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        span.price {
            min-width: 70px;
            text-align: right;
            font-weight: bold;
            color: #388E3C;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .btn-calculate {
            background-color: #00796B;
            color: white;
        }

        .btn-calculate:hover {
            background-color: #004d40;
        }

        .btn-save {
            background-color: #0288D1;
            color: white;
        }

        .btn-save:hover {
            background-color: #01579B;
        }

        .btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .result-section {
            margin-top: 25px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 10px;
            border-left: 5px solid #00796B;
        }

        .result-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .total-cost {
            font-size: 24px;
            font-weight: bold;
            color: #00796B;
            text-align: center;
            margin-top: 15px;
        }

        .success-message {
            background-color: #C8E6C9;
            color: #256029;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            display: <?php echo !empty($success) ? 'block' : 'none'; ?>;
        }

        @media (max-width: 600px) {
            .form-group {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-group label {
                width: auto;
            }

            span.price {
                text-align: left;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <head>
    
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
     
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
   
      <title>Smart_farming</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
    
      <link rel="stylesheet" href="css/bootstrap.min.css">
 
      <link rel="stylesheet" href="css/style.css">

      <link rel="stylesheet" href="css/responsive.css">

      <link rel="icon" href="images/fevicon.png" type="image/gif" />
   
      <link rel="stylesheet" href="css/owl.carousel.min.css">
      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
      <link rel="stylesheet" href="css/bootstrap-datepicker.min.css">

   </head>
    <div>
        <?php include('navbar.html'); ?>
    </div>
<div class="container">
    <img class="small-image" src="https://static.vecteezy.com/system/resources/previews/013/478/830/non_2x/smart-fariming-solar-cell-water-pump-and-robot-system-equipment-component-diagram-isometric-vector.jpg" alt="Farming Image">
    <h2>Farming Cost Calculator</h2>

    
    <?php if (!empty($download_links)): ?>
        <div class="success-message">
            <h3>Your calculation is ready!</h3>
            <a href="<?php echo $download_links['text']; ?>" class="download-btn" download>Download as Text File</a>
            <a href="<?php echo $download_links['image']; ?>" class="download-btn" download>Download as Image</a>
        </div>
    <?php endif; ?>
    
    <form id="calculator">
        <div class='form-group'>
            <label>Farmer:</label>
            <select id='userSelect' name='user_id' required>
                <option value=''>Select Farmer</option>
                <?php foreach ($farmers as $id => $name): ?>
                    <option value='<?php echo $id; ?>'><?php echo htmlspecialchars($name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class='form-group'>
            <label>Land Size (acres):</label>
            <input type='number' id='landSize' name='land_size' min='0' step='0.1' required>
        </div>
        
        <div class="crop-section">
            <div class='form-group'>
                <label>Crop:</label>
                <select id='cropSelect' name='crop_id' onchange='updatePrice("crop")' required>
                    <option value=''>Select Crop</option>
                    <?php foreach ($crops as $item): 
                        $crop_id = array_search($item['name'], array_column($crops, 'name')) + 1;
                        $name = htmlspecialchars($item['name']);
                        $price = htmlspecialchars($item['price']);
                    ?>
                        <option value='<?php echo $crop_id; ?>' data-price='<?php echo $price; ?>'>
                            <?php echo $name; ?> (<?php echo $price; ?> BDT/acre)
                        </option>
                    <?php endforeach; ?>
                </select>
                <span id='cropPrice' class='price'>0 BDT</span>
            </div>
        </div>
        
        <?php
        function buildSelector($label, $items, $type) {
            echo "<div class='form-group'>
                    <label>$label:</label>
                    <select id='{$type}Select' name='{$type}' onchange='updatePrice(\"$type\")'>
                        <option value=''>Select</option>";
            foreach ($items as $item) {
                $name = htmlspecialchars($item['name']);
                $price = htmlspecialchars($item['price']);
                echo "<option value='{$name}' data-price='{$price}'>{$name} ({$price} BDT)</option>";
            }
            echo "</select>
                <input type='number' id='{$type}Qty' name='{$type}_qty' placeholder='Qty' min='0'>
                <span id='{$type}Price' class='price'>0 BDT</span>
              </div>";
        }

        buildSelector("Equipment", $equipments, "equipment");
        buildSelector("Fertilizer", $fertilizer, "fertilizer");
        buildSelector("Pesticides", $pesticides, "pesticides");
        ?>
        
        <div class="button-group">
            <button type="button" class="btn btn-calculate" onclick="calculateTotal()">Calculate</button>
            <button type="button" class="btn btn-save" id="saveBtn" disabled onclick="saveCalculation()">Generate Report</button>
        </div>
        
        <div class="result-section" id="resultSection" style="display: none;">
            <div class="result-row">
                <span>Crop Cost:</span>
                <span id="displayCropCost">0 BDT</span>
            </div>
            <div class="result-row">
                <span>Equipment Cost:</span>
                <span id="displayEquipmentCost">0 BDT</span>
            </div>
            <div class="result-row">
                <span>Fertilizer Cost:</span>
                <span id="displayFertilizerCost">0 BDT</span>
            </div>
            <div class="result-row">
                <span>Pesticides Cost:</span>
                <span id="displayPesticidesCost">0 BDT</span>
            </div>
            <div class="total-cost">
                Total Cost: <span id="total">0 BDT</span>
            </div>
        </div>
    </form>
    
    <form id="saveForm" method="POST" style="display: none;">
        <input type="hidden" name="user_id" id="save_user_id">
        <input type="hidden" name="land_size" id="save_land_size">
        <input type="hidden" name="crop_id" id="save_crop_id">
        <input type="hidden" name="equipment" id="save_equipment">
        <input type="hidden" name="equipment_qty" id="save_equipment_qty">
        <input type="hidden" name="fertilizer" id="save_fertilizer">
        <input type="hidden" name="fertilizer_qty" id="save_fertilizer_qty">
        <input type="hidden" name="pesticides" id="save_pesticides">
        <input type="hidden" name="pesticides_qty" id="save_pesticides_qty">
        <input type="hidden" name="total_cost" id="save_total_cost">
        <input type="hidden" name="generate_files" value="1">
    </form>
</div>

<script>
    let calculationData = {};
    
    function updatePrice(type) {
        const select = document.getElementById(type + "Select");
        const price = select.options[select.selectedIndex]?.getAttribute('data-price') || 0;
        document.getElementById(type + "Price").innerText = price + " BDT";
    }

    function calculateTotal() {
        const landSize = parseFloat(document.getElementById('landSize').value) || 0;
        const cropPrice = parseFloat(document.getElementById('cropPrice').innerText) || 0;
        const cropCost = cropPrice * landSize;
        
        const types = ['equipment', 'fertilizer', 'pesticides'];
        let total = cropCost;
        let breakdown = {
            crop: cropCost,
            equipment: 0,
            fertilizer: 0,
            pesticides: 0
        };
        
        types.forEach(type => {
            const price = parseFloat(document.getElementById(type + 'Price').innerText) || 0;
            const qty = parseInt(document.getElementById(type + 'Qty').value) || 0;
            const cost = price * qty;
            total += cost;
            breakdown[type] = cost;
        });
        
        // Update display
        document.getElementById('displayCropCost').innerText = cropCost.toFixed(2) + " BDT";
        document.getElementById('displayEquipmentCost').innerText = breakdown.equipment.toFixed(2) + " BDT";
        document.getElementById('displayFertilizerCost').innerText = breakdown.fertilizer.toFixed(2) + " BDT";
        document.getElementById('displayPesticidesCost').innerText = breakdown.pesticides.toFixed(2) + " BDT";
        document.getElementById('total').innerText = total.toFixed(2) + " BDT";
        
      
        document.getElementById('resultSection').style.display = 'block';
        
      
        const requiredFields = ['userSelect', 'landSize', 'cropSelect'];
        const allFilled = requiredFields.every(id => document.getElementById(id).value !== '');
        
        document.getElementById('saveBtn').disabled = !allFilled;
        
       
        calculationData = {
            user_id: document.getElementById('userSelect').value,
            land_size: landSize,
            crop_id: document.getElementById('cropSelect').value,
            equipment: document.getElementById('equipmentSelect').value,
            equipment_qty: document.getElementById('equipmentQty').value || 0,
            fertilizer: document.getElementById('fertilizerSelect').value,
            fertilizer_qty: document.getElementById('fertilizerQty').value || 0,
            pesticides: document.getElementById('pesticidesSelect').value,
            pesticides_qty: document.getElementById('pesticidesQty').value || 0,
            total_cost: total
        };
    }
    
    function saveCalculation() {
     
        for (const key in calculationData) {
            document.getElementById('save_' + key).value = calculationData[key];
        }
        
       
        document.getElementById('saveForm').submit();
    }
    
   
    document.getElementById('userSelect').addEventListener('change', checkSaveButton);
    document.getElementById('landSize').addEventListener('input', checkSaveButton);
    document.getElementById('cropSelect').addEventListener('change', checkSaveButton);
    
    function checkSaveButton() {
        const requiredFields = ['userSelect', 'landSize', 'cropSelect'];
        const allFilled = requiredFields.every(id => document.getElementById(id).value !== '');
        const saveBtn = document.getElementById('saveBtn');
        
        if (allFilled && document.getElementById('resultSection').style.display === 'block') {
            saveBtn.disabled = false;
        } else {
            saveBtn.disabled = true;
        }
    }
    
</script>
</body>
</html>