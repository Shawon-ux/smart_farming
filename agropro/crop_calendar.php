<?php
include 'db.php';
mysqli_report(MYSQLI_REPORT_OFF);

// Live data from the database
$prices = [];
if ($r = $conn->query("SELECT name, crop_price FROM crops")) while ($x = $r->fetch_assoc()) $prices[strtolower($x['name'])] = $x['crop_price'];
$weather = [];
if ($r = $conn->query("SELECT season, humidity, temperature FROM weather")) while ($x = $r->fetch_assoc()) $weather[$x['season']] = $x;

// Crops managed by the admin (admin_crops.php)
$conn->set_charset('utf8mb4');
$crops = [];
if ($r = $conn->query("SELECT * FROM crop_calendar ORDER BY name")) {
    while ($x = $r->fetch_assoc()) {
        $crops[] = [$x['name'], $x['season'], $x['type'], $x['soil'],
            array_map('intval', array_filter(explode(',', $x['sow_months']), 'strlen')),
            array_map('intval', array_filter(explode(',', $x['harvest_months']), 'strlen')), $x['tip']];
    }
}
$mn = ['J','F','M','A','M','J','J','A','S','O','N','D'];
$full = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
$now = (int)date('n');
// season of the current month
$curSeason = in_array($now, [11,12,1,2]) ? 'Rabi' : (in_array($now, [3,4,5,6]) ? 'Kharif-1' : 'Kharif-2');
$seasonInfo = ['Rabi' => 'Nov – Feb', 'Kharif-1' => 'Mar – Jun', 'Kharif-2' => 'Jul – Oct'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crop Calendar - Smart Farming</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/projects.css">
  <link rel="icon" href="images/fevicon.png" type="image/gif" />
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
  <style>
    .cc-wrap{background:#f4f7ea;padding:40px 0 70px}
    .cc-now{background:#fff;border-left:6px solid #6b7908;border-radius:14px;padding:18px 24px;box-shadow:0 8px 22px rgba(0,0,0,.07);margin-bottom:26px;display:flex;gap:16px;align-items:center;flex-wrap:wrap}
    .cc-now i{font-size:30px;color:#6b7908}.cc-now strong{color:#1f2a1f;font-size:18px}.cc-now span{color:#555}
    .cc-chips{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:24px}
    .cc-chips button{border:2px solid #6b7908;background:#fff;color:#6b7908;padding:8px 20px;border-radius:30px;font-weight:600;cursor:pointer;transition:.2s}
    .cc-chips button.on,.cc-chips button:hover{background:#6b7908;color:#fff}
    .cc-season{display:none;margin-bottom:10px}.cc-season.on{display:block}
    .cc-sh{display:flex;gap:14px;flex-wrap:wrap;align-items:center;margin-bottom:18px;color:#1f2a1f}
    .cc-sh h3{margin:0;font-size:24px;color:#1f2a1f}
    .cc-sh .tag{background:#e6f6e0;color:#2e7d32;padding:4px 14px;border-radius:20px;font-size:13px;font-weight:700}
    .cc-sh .tag.w{background:#fff6dc;color:#8a6d00}
    .cc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(380px,1fr));gap:22px}
    .cc-card{background:#fff;border-radius:16px;box-shadow:0 8px 22px rgba(0,0,0,.07);padding:22px;transition:.3s}
    .cc-card:hover{transform:translateY(-5px);box-shadow:0 14px 30px rgba(0,0,0,.12)}
    .cc-card.hot{outline:3px solid #b7cf2a}
    .cc-head{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:6px}
    .cc-head h4{margin:0;font-size:20px;color:#1f2a1f}
    .cc-type{background:#f4f7ea;color:#6b7908;padding:3px 12px;border-radius:14px;font-size:12px;font-weight:700}
    .cc-meta{color:#555;font-size:14px;margin:4px 0}
    .cc-meta i{color:#6b7908;width:18px}
    .cc-price{display:inline-block;margin:8px 0 12px;background:#6b7908;color:#fff;padding:4px 14px;border-radius:20px;font-size:13px;font-weight:600}
    .cc-bar{display:grid;grid-template-columns:repeat(12,1fr);gap:3px;margin:10px 0 6px}
    .cc-bar span{height:30px;border-radius:6px;background:#eef1e2;color:#9aa27f;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center}
    .cc-bar .s{background:#8aa10b;color:#fff}.cc-bar .h{background:#f59e0b;color:#fff}.cc-bar .sh{background:linear-gradient(135deg,#8aa10b 50%,#f59e0b 50%);color:#fff}
    .cc-bar .now{box-shadow:0 0 0 2px #1f2a1f}
    .cc-leg{display:flex;gap:16px;font-size:12px;color:#555;margin-bottom:10px}
    .cc-leg b{display:inline-block;width:12px;height:12px;border-radius:3px;margin-right:5px;vertical-align:-1px}
    .cc-tip{background:#f9fbf2;border-radius:10px;padding:10px 14px;font-size:14px;color:#444}
    .cc-act{margin-top:12px;font-size:13px;font-weight:700;color:#2e7d32}
    @media(max-width:480px){.cc-grid{grid-template-columns:1fr}}
  </style>
</head>
<body class="main-layout inner_page">
  <?php include 'navbar.html'; ?>
  <section class="page-hero">
    <span class="badge-pill">Crop Calendar</span>
    <h1>What to Grow &amp; When</h1>
    <p>Sowing and harvest times for popular crops in Bangladesh, by season.</p>
  </section>

  <section class="cc-wrap">
    <div class="container">
      <div class="cc-now">
        <i class="fa fa-calendar-check-o"></i>
        <div><strong>It's <?= $full[$now] ?> — <?= $curSeason ?> season (<?= $seasonInfo[$curSeason] ?>)</strong><br>
        <span>Crops you can sow or harvest this month are highlighted below.</span></div>
      </div>

      <div class="cc-chips" id="chips">
        <?php foreach (['Rabi','Kharif-1','Kharif-2'] as $s): ?>
          <button type="button" data-s="<?= $s ?>" class="<?= $s === $curSeason ? 'on' : '' ?>"><?= $s ?> · <?= $seasonInfo[$s] ?></button>
        <?php endforeach; ?>
        <button type="button" data-s="all">All crops</button>
      </div>

      <?php foreach (['Rabi','Kharif-1','Kharif-2'] as $s): ?>
      <div class="cc-season <?= $s === $curSeason ? 'on' : '' ?>" data-season="<?= $s ?>">
        <div class="cc-sh">
          <h3><?= $s ?> season</h3>
          <span class="tag"><?= $seasonInfo[$s] ?></span>
          <?php if (isset($weather[$s])): ?>
            <span class="tag w"><i class="fa fa-thermometer-half"></i> <?= htmlspecialchars($weather[$s]['temperature']) ?>°C</span>
            <span class="tag w"><i class="fa fa-tint"></i> <?= htmlspecialchars($weather[$s]['humidity']) ?> humidity</span>
          <?php endif; ?>
        </div>
        <div class="cc-grid">
          <?php foreach ($crops as [$name, $cs, $type, $soil, $sow, $harv, $tip]): if ($cs !== $s) continue;
              $price = $prices[strtolower($name)] ?? null;
              $doing = in_array($now, $sow) ? 'Good time to sow now' : (in_array($now, $harv) ? 'Harvest time now' : ''); ?>
          <div class="cc-card <?= $doing ? 'hot' : '' ?>">
            <div class="cc-head"><h4><?= htmlspecialchars($name) ?></h4><span class="cc-type"><?= $type ?></span></div>
            <div class="cc-meta"><i class="fa fa-leaf"></i> <?= htmlspecialchars($soil) ?></div>
            <?php if ($price !== null): ?><span class="cc-price">৳<?= number_format((float)$price, 2) ?> / acre</span><?php endif; ?>
            <div class="cc-bar">
              <?php for ($m = 1; $m <= 12; $m++):
                  $a = in_array($m, $sow); $b = in_array($m, $harv);
                  $c = $a && $b ? 'sh' : ($a ? 's' : ($b ? 'h' : '')); ?>
                <span class="<?= $c ?> <?= $m === $now ? 'now' : '' ?>" title="<?= $full[$m] ?>"><?= $mn[$m-1] ?></span>
              <?php endfor; ?>
            </div>
            <div class="cc-leg"><span><b style="background:#8aa10b"></b>Sowing</span><span><b style="background:#f59e0b"></b>Harvest</span></div>
            <div class="cc-tip"><i class="fa fa-lightbulb-o"></i> <?= htmlspecialchars($tip) ?></div>
            <?php if ($doing): ?><div class="cc-act">● <?= $doing ?></div><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
      <div style="text-align:center;margin-top:30px">
        <a class="pj-btn" href="calculator.php"><i class="fa fa-calculator"></i> Estimate your costs</a>
        <a class="pj-btn" style="background:#fff;color:#6b7908;border:2px solid #6b7908;margin-left:8px" href="blog.php"><i class="fa fa-comments"></i> Ask AgroBot</a>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('#chips button').forEach(function (b) {
      b.onclick = function () {
        document.querySelectorAll('#chips button').forEach(function (x) { x.classList.remove('on'); });
        b.classList.add('on');
        document.querySelectorAll('.cc-season').forEach(function (s) {
          s.classList.toggle('on', b.dataset.s === 'all' || s.dataset.season === b.dataset.s);
        });
      };
    });
  </script>
</body>
</html>
