<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_method_not_allowed("GET");
}

$hmac_key = get_altcha_key();
$salt = bin2hex(random_bytes(12));
$number = random_int(0, 50000);
$challenge = hash('sha256', $salt . $number);
$signature = hash_hmac('sha256', $challenge, $hmac_key);

echo json_encode([
    'algorithm' => 'SHA-256',
    'challenge' => $challenge,
    'maxnumber' => 50000,
    'salt' => $salt,
    'signature' => $signature,
]);
exit;
