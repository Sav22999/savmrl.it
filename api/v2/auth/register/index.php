<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/alpha/include/emails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

$data = get_json_body();
$username = isset($data['username']) ? $data['username'] : '';
$email = isset($data['email']) ? $data['email'] : '';
$password = isset($data['password']) ? $data['password'] : '';
$terms_accepted = !empty($data['terms_accepted']);
$altcha = isset($data['altcha']) ? $data['altcha'] : '';

if (empty($username) || empty($email) || empty($password)) {
    api_bad_request('Username, email and password are required');
}

if (!$terms_accepted) {
    api_bad_request('You must accept the Privacy Policy and Terms of Service');
}

if (!verify_altcha($altcha)) {
    api_bad_request('Captcha verification failed');
}

$result = auth_register($email, $password, $username);

if (isset($result['error'])) {
    api_bad_request($result['error']);
}

$otp = generate_otp($result['user_id'], 'email_verification');
if ($otp) {
    $verify_url = "https://savmrl.it/alpha/account/verify/?token=" . urlencode($otp['temp_token']);
    send_verification_email($result['email'], $result['username'], $verify_url);
}

api_created('Account created successfully. Please check your email to verify your address.', [
    'user_id' => $result['user_id'],
    'email' => $result['email'],
    'username' => $result['username'],
]);
