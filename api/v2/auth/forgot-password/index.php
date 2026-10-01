<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/alpha/include/emails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

$data = get_json_body();
$action = isset($data['action']) ? $data['action'] : '';

if ($action === 'request') {
    $email = isset($data['email']) ? $data['email'] : '';
    if (empty($email)) {
        api_bad_request('Email is required');
    }

    $result = auth_forgot_password_request($email);
    if (isset($result['error'])) {
        api_bad_request($result['error']);
    }

    if (isset($result['code'])) {
        send_password_reset_email($result['email'], $result['username'], $result['code']);
    }

    api_success('If an account with that email exists, a reset code has been sent.', [
        'temp_token' => isset($result['temp_token']) ? $result['temp_token'] : null,
    ]);

} elseif ($action === 'reset') {
    $temp_token = isset($data['temp_token']) ? $data['temp_token'] : '';
    $code = isset($data['code']) ? $data['code'] : '';
    $new_password = isset($data['new_password']) ? $data['new_password'] : '';

    if (empty($temp_token) || empty($code) || empty($new_password)) {
        api_bad_request('Token, code, and new password are required');
    }

    $result = auth_reset_password($temp_token, $code, $new_password);
    if (isset($result['error'])) {
        api_bad_request($result['error']);
    }

    if (isset($result['email'])) {
        send_password_changed_email($result['email'], $result['username']);
    }

    api_success('Password has been reset successfully');

} else {
    api_bad_request('Invalid action. Use "request" or "reset".');
}
