<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/alpha/include/emails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

auth_start_session();
$user = auth_require_login();

$data = get_json_body();
$enable = isset($data['enable']) ? (bool)$data['enable'] : true;

$otp = generate_otp($user['id'], 'login_2fa');
if (!$otp) {
    api_error('Failed to generate verification code');
}

send_otp_email($user['email'], $user['username'], $otp['code']);

api_success('Verification code sent', [
    'temp_token' => $otp['temp_token'],
]);
