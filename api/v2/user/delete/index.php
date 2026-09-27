<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/savmrl/include/emails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

auth_start_session();
$user = auth_require_login();

$data = get_json_body();
$step = isset($data['step']) ? $data['step'] : 'request';

if ($step === 'request') {
    $password = isset($data['password']) ? $data['password'] : '';

    if (empty($password)) {
        api_bad_request('Password is required');
    }

    $result = auth_delete_account($user['id'], $password);

    if (isset($result['error'])) {
        api_bad_request($result['error']);
    }

    $otp = generate_otp($user['id'], 'account_deletion');
    if (!$otp) {
        api_error('Failed to generate verification code');
    }

    send_otp_email($user['email'], $user['username'], $otp['code']);

    api_success('Verification code sent to your email', [
        'temp_token' => $otp['temp_token'],
        'requires_verification' => true,
    ]);

} elseif ($step === 'confirm') {
    $temp_token = isset($data['temp_token']) ? $data['temp_token'] : '';
    $code = isset($data['code']) ? $data['code'] : '';

    if (empty($temp_token) || empty($code)) {
        api_bad_request('Verification code and token are required');
    }

    $verify = verify_otp($temp_token, $code, 'account_deletion');

    if (isset($verify['error'])) {
        api_bad_request($verify['error']);
    }

    if ((int)$verify['user_id'] !== (int)$user['id']) {
        api_unauthorized('Token mismatch');
    }

    auth_destroy_session();
    $result = auth_confirm_delete_account($user['id']);

    if (isset($result['error'])) {
        api_error($result['error']);
    }

    api_success('Account deleted successfully.');

} else {
    api_bad_request('Invalid step. Use "request" or "confirm".');
}
