<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/savmrl/include/emails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

auth_start_session();
$user = auth_require_login();

$data = get_json_body();
$temp_token = isset($data['temp_token']) ? $data['temp_token'] : '';
$code = isset($data['code']) ? $data['code'] : '';
$enable = isset($data['enable']) ? (bool)$data['enable'] : true;

if (empty($temp_token) || empty($code)) {
    api_bad_request('Token and code are required');
}

$result = verify_otp($temp_token, $code, 'login_2fa');
if (isset($result['error'])) {
    api_unauthorized($result['error']);
}

global $localhost_db, $username_db, $password_db, $database_savmrl;
$c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
$c->set_charset("utf8mb4");

$val = $enable ? 1 : 0;
$stmt = $c->prepare("UPDATE `users_savmrl` SET `two_fa_enabled` = ? WHERE `id` = ?");
$stmt->bind_param("ii", $val, $user['id']);
$stmt->execute();
$stmt->close();
$c->close();

send_2fa_changed_email($user['email'], $user['username'], $enable);

api_success('Two-factor authentication ' . ($enable ? 'enabled' : 'disabled'));
