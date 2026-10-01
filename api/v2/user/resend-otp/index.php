<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/alpha/include/emails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

$data = get_json_body();
$temp_token = isset($data['temp_token']) ? $data['temp_token'] : '';
$purpose = isset($data['purpose']) ? $data['purpose'] : '';

if (empty($temp_token) || empty($purpose)) {
    api_bad_request('temp_token and purpose are required');
}

$allowed_purposes = ['login_2fa', 'password_change', 'account_deletion', 'password_reset'];
if (!in_array($purpose, $allowed_purposes)) {
    api_bad_request('Invalid purpose');
}

global $localhost_db, $username_db, $password_db, $database_savmrl;
$c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
if ($c->connect_error) {
    api_error('Database connection error');
}
$c->set_charset("utf8mb4");

$stmt = $c->prepare("SELECT `user_id` FROM `otp_codes_savmrl` WHERE `temp_token` = ? AND `purpose` = ? ORDER BY `created_at` DESC LIMIT 1");
$stmt->bind_param("ss", $temp_token, $purpose);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $c->close();
    api_not_found('Token not found');
}

$row = $result->fetch_assoc();
$user_id = (int)$row['user_id'];
$stmt->close();

$user_stmt = $c->prepare("SELECT `email`, `username` FROM `users_savmrl` WHERE `id` = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows !== 1) {
    $user_stmt->close();
    $c->close();
    api_not_found('User not found');
}

$user = $user_result->fetch_assoc();
$user_stmt->close();
$c->close();

$otp = generate_otp($user_id, $purpose);
if (!$otp) {
    api_error('Failed to generate verification code');
}

if ($purpose === 'password_reset') {
    send_password_reset_email($user['email'], $user['username'], $otp['code']);
} else {
    send_otp_email($user['email'], $user['username'], $otp['code']);
}

api_success('New verification code sent', [
    'temp_token' => $otp['temp_token'],
]);
