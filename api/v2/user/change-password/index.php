<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/alpha/include/emails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

auth_start_session();
$user = auth_require_login();

$data = get_json_body();
$step = isset($data['step']) ? $data['step'] : 'request';

if ($step === 'request') {
    $current = isset($data['current_password']) ? $data['current_password'] : '';
    $new = isset($data['new_password']) ? $data['new_password'] : '';

    if (empty($current) || empty($new)) {
        api_bad_request('Current and new password are required');
    }

    $result = auth_request_password_change($user['id'], $current, $new);

    if (isset($result['error'])) {
        api_bad_request($result['error']);
    }

    send_otp_email($user['email'], $user['username'], $result['code']);

    api_success('Verification code sent to your email', [
        'temp_token' => $result['temp_token'],
        'requires_verification' => true,
    ]);

} elseif ($step === 'confirm') {
    $temp_token = isset($data['temp_token']) ? $data['temp_token'] : '';
    $code = isset($data['code']) ? $data['code'] : '';
    $new = isset($data['new_password']) ? $data['new_password'] : '';

    if (empty($temp_token) || empty($code) || empty($new)) {
        api_bad_request('Verification code, token and new password are required');
    }

    $verify = verify_otp($temp_token, $code, 'password_change');

    if (isset($verify['error'])) {
        api_bad_request($verify['error']);
    }

    if ((int)$verify['user_id'] !== (int)$user['id']) {
        api_unauthorized('Token mismatch');
    }

    $result = auth_confirm_password_change($user['id'], $new);

    if (isset($result['error'])) {
        api_bad_request($result['error']);
    }

    send_password_changed_email($user['email'], $user['username']);

    api_success('Password changed successfully. All sessions have been invalidated.');

} else {
    api_bad_request('Invalid step. Use "request" or "confirm".');
}
