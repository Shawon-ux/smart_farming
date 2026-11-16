<?php
// JSON endpoint for the chatbot. POST {messages:[{role,content},...]} -> {reply}
header('Content-Type: application/json; charset=utf-8');
$in = json_decode(file_get_contents('php://input'), true);
$history = [];
foreach (($in['messages'] ?? []) as $m) {
    if (in_array($m['role'] ?? '', ['user', 'assistant'], true) && is_string($m['content'] ?? null) && trim($m['content']) !== '') {
        $history[] = ['role' => $m['role'], 'content' => mb_substr($m['content'], 0, 2000)];
    }
}
$history = array_slice($history, -12);
while ($history && $history[0]['role'] !== 'user') array_shift($history);
if (!$history || end($history)['role'] !== 'user') { echo json_encode(['reply' => 'Please type a question.']); exit; }
$question = strtolower(end($history)['content']);

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli("localhost", "root", "", "smart_farming");

function rows($conn, $sql) {
    $out = [];
    $r = $conn ? @$conn->query($sql) : false;
    if ($r) while ($row = $r->fetch_assoc()) $out[] = $row;
    return $out;
}
function lines($rows) {
    return implode("\n", array_map(fn($r) => '- ' . implode(', ', array_map(fn($k, $v) => "$k: " . ($v ?? '-'), array_keys($r), $r)), $rows));
}
$tables = [
    'Crops (price per acre in BDT)' => "SELECT name, type, crop_price FROM crops LIMIT 40",
    'Fertilizers (price per kg in BDT)' => "SELECT name, `usage`, type, price_per_kg, availability FROM fertilizer LIMIT 40",
    'Pesticides (price per unit in BDT)' => "SELECT name, type, price_per_unit, availability FROM pesticides LIMIT 40",
    'Equipment for rent (BDT per day)' => "SELECT name, type, rental_price_per_day, availability FROM equipments LIMIT 40",
    'Markets' => "SELECT name, address FROM market LIMIT 40",
    'Seasons / weather' => "SELECT season, temperature FROM weather LIMIT 20",
    'Land / soil' => "SELECT soil_type, location FROM lands LIMIT 20",
    'Running projects' => "SELECT name, location, starting_date, ending_date FROM project WHERE status='Approved' LIMIT 20",
];
$context = '';
foreach ($tables as $title => $sql) {
    $r = rows($conn, $sql);
    if ($r) $context .= "## $title\n" . lines($r) . "\n\n";
}

$cfg = @include __DIR__ . '/ai_config.php';
$key = is_array($cfg) ? trim($cfg['api_key'] ?? '') : '';

if ($key !== '') {
    $system = "You are AgroBot, the friendly assistant of the Smart Farming website in Bangladesh. "
        . "Answer any question a farmer might ask (crops, soil, fertilizer, pests, diseases, irrigation, weather, seasons, markets, equipment, costs, farming practice). "
        . "Be practical, accurate and concise (under 180 words), use simple language, short bullet points when useful, and reply in the language the user writes in (English or Bangla). "
        . "When relevant, prefer the website data below (prices in BDT). If you are unsure or it is a serious chemical/safety matter, advise consulting a local agriculture officer. "
        . "If the question is unrelated to farming, politely steer back to farming.\n\nWebsite data:\n" . $context;
    $payload = json_encode([
        'model' => $cfg['model'] ?? 'claude-haiku-5-5',
        'max_tokens' => 700,
        'system' => $system,
        'messages' => $history,
    ]);
    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 40,
        CURLOPT_HTTPHEADER => ['content-type: application/json', 'x-api-key: ' . $key, 'anthropic-version: 2023-06-01'],
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data = json_decode((string)$res, true);
    $text = '';
    foreach (($data['content'] ?? []) as $b) if (($b['type'] ?? '') === 'text') $text .= $b['text'];
    if ($code === 200 && $text !== '') { echo json_encode(['reply' => $text]); exit; }
    $apiErr = $data['error']['message'] ?? ('HTTP ' . $code);
}

// Fallback: simple database answers
$map = [
    'market' => ['Markets', "SELECT name, address FROM market LIMIT 5"],
    'crop' => ['Crops', "SELECT name, type, crop_price FROM crops LIMIT 8"],
    'fertilizer' => ['Fertilizers', "SELECT name, type, price_per_kg FROM fertilizer LIMIT 8"],
    'pesticide' => ['Pesticides', "SELECT name, type, price_per_unit FROM pesticides LIMIT 8"],
    'equipment' => ['Equipment', "SELECT name, type, rental_price_per_day FROM equipments LIMIT 8"],
    'weather' => ['Seasons', "SELECT season, temperature FROM weather LIMIT 5"],
    'season' => ['Seasons', "SELECT season, temperature FROM weather LIMIT 5"],
    'soil' => ['Land', "SELECT soil_type, location FROM lands LIMIT 5"],
    'land' => ['Land', "SELECT soil_type, location FROM lands LIMIT 5"],
    'helper' => ['Helpers', "SELECT name, phone_number FROM helper LIMIT 5"],
];
$reply = "I can answer from our database for now. Ask about markets, crops, fertilizer, pesticides, equipment, weather, soil or helpers.";
foreach ($map as $kw => [$title, $sql]) {
    if (strpos($question, $kw) !== false) {
        $r = rows($conn, $sql);
        if ($r) { $reply = "**$title**\n" . lines($r); break; }
    }
}
if ($key !== '') $reply .= "\n\n(AI service unavailable: " . ($apiErr ?? 'error') . ")";
elseif (!$key) $reply .= "\n\n_AI mode is off — add an API key in ai_config.php to answer any question._";
echo json_encode(['reply' => $reply]);
