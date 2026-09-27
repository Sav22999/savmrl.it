<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/savmrl/include/emails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

$data = get_json_body();
$email = isset($data['email']) ? strtolower(trim($data['email'])) : '';

if (empty($email)) {
    api_bad_request('Email is required');
}

$conn = get_db_connection();
$stmt = $conn->prepare("SELECT `id`, `username`, `email_verified` FROM `users_savmrl` WHERE `email` = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    api_success('If the email is registered, a verification email has been sent.');
}

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

if ((int)$user['email_verified'] === 1) {
    api_success('If the email is registered, a verification email has been sent.');
}

$otp = generate_otp($user['id'], 'email_verification');
if ($otp) {
    $verify_url = "https://savmrl.it/alpha/account/verify/?token=" . urlencode($otp['temp_token']);
    send_verification_email($email, $user['username'], $verify_url);
}

api_success('If the email is registered, a verification email has been sent.');
