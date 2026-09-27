<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/savmrl/include/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

$data = get_json_body();
$name = isset($data['name']) ? validate_name($data['name']) : '';
$reason = isset($data['reason']) ? $data['reason'] : '';

if (empty($name)) {
    api_bad_request('Link name is required');
}

$valid_reasons = ['phishing', 'spam', 'illegal', 'other'];
if (!in_array($reason, $valid_reasons)) {
    api_bad_request('Invalid reason. Use: ' . implode(', ', $valid_reasons));
}

$conn = get_db_connection();

$table = $redirect_table;
$stmt = $conn->prepare("SELECT `id`, `redirect_link`, `name` FROM `$table` WHERE `name` = ?");
$stmt->bind_param('s', $name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    api_not_found('Link not found');
}

$link = $result->fetch_assoc();
$stmt->close();

$stmt2 = $conn->prepare("UPDATE `$table` SET `reported` = 1 WHERE `name` = ?");
$stmt2->bind_param('s', $name);
$stmt2->execute();
$stmt2->close();
$conn->close();

$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $_SERVER['REMOTE_ADDR']);
$reason_label = ucfirst($reason);
$link_url = "https://savmrl.it/r/" . htmlspecialchars($name);
$dest_url = htmlspecialchars($link['redirect_link']);

$subject = "[savmrl.it] Link reported: $reason_label — $name";
$html = "
<div style='font-family:Inter,Arial,sans-serif;max-width:500px;margin:0 auto;padding:20px'>
    <h2 style='color:#00A7AA'>Link Report — savmrl.it</h2>
    <p><strong>Short link:</strong> <a href='$link_url'>$link_url</a></p>
    <p><strong>Destination:</strong> $dest_url</p>
    <p><strong>Reason:</strong> $reason_label</p>
    <p><strong>Reporter IP:</strong> $ip</p>
    <p><strong>Time:</strong> " . date('Y-m-d H:i:s') . " UTC</p>
    <hr style='border:none;border-top:1px solid #eee;margin:16px 0'>
    <p style='color:#888;font-size:13px'>The link has been automatically flagged as reported.</p>
</div>";

send_email('saverio.morelli@protonmail.com', $subject, $html);

api_success('Link reported successfully. Thank you for helping keep the web safe.');
