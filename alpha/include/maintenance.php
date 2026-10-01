<?php
$maintenance_file = $_SERVER['DOCUMENT_ROOT'] . '/alpha/.maintenance';
$maintenance_bypass = false;

if (!file_exists($maintenance_file)) {
    return;
}

$bypass_key = trim(file_get_contents($maintenance_file));
if ($bypass_key === '') {
    return;
}

$bypass_hash = hash('sha256', $bypass_key);

if (isset($_COOKIE['maintenance_bypass']) && hash_equals($bypass_hash, $_COOKIE['maintenance_bypass'])) {
    $maintenance_bypass = true;
    return;
}

if (isset($_GET['bypass']) && hash_equals($bypass_key, $_GET['bypass'])) {
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('maintenance_bypass', $bypass_hash, [
        'expires' => time() + 86400,
        'path' => '/',
        'secure' => $is_https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $maintenance_bypass = true;
    return;
}

while (ob_get_level()) {
    ob_end_clean();
}

http_response_code(503);
header('Retry-After: 3600');

$is_api = (strpos($_SERVER['REQUEST_URI'], '/api/') === 0);
if ($is_api) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'code' => '503',
        'status' => 'Maintenance',
        'description' => 'Service temporarily unavailable for scheduled maintenance',
    ]);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
include_once __DIR__ . '/maintenance-page.php';
exit;
