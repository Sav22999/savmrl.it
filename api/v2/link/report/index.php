<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/alpha/include/mailer.php';

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

$has_user_reports = false;
$col_check = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND COLUMN_NAME = 'user_reports'");
if ($col_check && $col_check->num_rows > 0) {
    $has_user_reports = true;
}

if ($has_user_reports) {
    $get_reasons = $conn->prepare("SELECT `user_report_reason` FROM `$table` WHERE `name` = ?");
    $get_reasons->bind_param('s', $name);
    $get_reasons->execute();
    $row = $get_reasons->get_result()->fetch_assoc();
    $get_reasons->close();

    $reasons = [];
    if ($row && $row['user_report_reason']) {
        $decoded = json_decode($row['user_report_reason'], true);
        if (is_array($decoded)) $reasons = $decoded;
    }
    $reasons[$reason] = isset($reasons[$reason]) ? $reasons[$reason] + 1 : 1;
    $reasons_json = json_encode($reasons);

    $stmt2 = $conn->prepare("UPDATE `$table` SET `user_reports` = `user_reports` + 1, `user_report_reason` = ? WHERE `name` = ?");
    $stmt2->bind_param('ss', $reasons_json, $name);
    $stmt2->execute();
    $stmt2->close();
}
$conn->close();

$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $_SERVER['REMOTE_ADDR']);
$reason_label = ucfirst($reason);
$link_url = SHORT_URL_BASE . htmlspecialchars($name);
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
    <p style='color:#888;font-size:13px'>This report requires manual review. The link has not been blocked automatically.</p>
</div>";

send_email('saverio.morelli@protonmail.com', $subject, $html);

api_success('Link reported successfully. Thank you for helping keep the web safe.');
