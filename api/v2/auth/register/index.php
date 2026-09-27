<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/savmrl/include/emails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

$data = get_json_body();
$username = isset($data['username']) ? $data['username'] : '';
$email = isset($data['email']) ? $data['email'] : '';
$password = isset($data['password']) ? $data['password'] : '';

if (empty($username) || empty($email) || empty($password)) {
    api_bad_request('Username, email and password are required');
}

$result = auth_register($email, $password, $username);

if (isset($result['error'])) {
    api_bad_request($result['error']);
}

$otp = generate_otp($result['user_id'], 'email_verification');
if ($otp) {
    $verify_url = "https://savmrl.it/account/verify/?token=" . urlencode($otp['temp_token']);
    send_verification_email($result['email'], $result['username'], $verify_url);
}

api_created('Account created successfully. Please check your email to verify your address.', [
    'user_id' => $result['user_id'],
    'email' => $result['email'],
    'username' => $result['username'],
]);
