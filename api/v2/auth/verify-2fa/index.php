<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

$data = get_json_body();
$temp_token = isset($data['temp_token']) ? $data['temp_token'] : '';
$code = isset($data['code']) ? $data['code'] : '';

if (empty($temp_token) || empty($code)) {
    api_bad_request('Temp token and code are required');
}

$result = verify_otp($temp_token, $code, 'login_2fa');

if (isset($result['error'])) {
    api_unauthorized($result['error']);
}

$session_id = auth_create_session($result['user_id']);
if (!$session_id) {
    api_error('Session creation failed');
}

auth_start_session();
$user = auth_get_current_user();

api_success('Login successful', [
    'session_id' => $session_id,
    'user' => [
        'id' => $user ? $user['id'] : $result['user_id'],
        'username' => $user ? $user['username'] : '',
        'email' => $user ? $user['email'] : '',
        'preferred_language' => $user ? $user['preferred_language'] : 'en',
    ],
]);
