<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/alpha/include/emails.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

$data = get_json_body();
$email = isset($data['email']) ? $data['email'] : '';
$password = isset($data['password']) ? $data['password'] : '';

if (empty($email) || empty($password)) {
    api_bad_request('Email and password are required');
}

$result = auth_login($email, $password);

if (isset($result['error'])) {
    if (isset($result['notify_email'])) {
        send_login_failed_email($result['notify_email'], $result['notify_username']);
    }
    api_unauthorized($result['error']);
}

if (isset($result['email_not_verified'])) {
    $resend = auth_resend_verification($result['user_id']);
    if ($resend) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/alpha/include/emails.php';
        $verify_url = "https://savmrl.it/alpha/account/verify/?token=" . urlencode($resend['temp_token']);
        send_verification_email($resend['email'], $resend['username'], $verify_url);
    }
    api_response(403, "Error", "Email not verified. A new verification email has been sent.", [
        'email_not_verified' => true,
        'user_id' => $result['user_id'],
    ]);
}

if (isset($result['requires_2fa'])) {
    $otp = generate_otp($result['user_id'], 'login_2fa');
    if (!$otp) {
        api_error('Failed to generate verification code');
    }

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    $c->set_charset("utf8mb4");
    $stmt = $c->prepare("SELECT `email`, `username` FROM `users_savmrl` WHERE `id` = ?");
    $stmt->bind_param("i", $result['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $c->close();

    send_otp_email($user['email'], $user['username'], $otp['code']);

    api_success('Two-factor authentication required', [
        'requires_2fa' => true,
        'temp_token' => $otp['temp_token'],
    ]);
}

$c = get_db_connection();
$stmt = $c->prepare("SELECT `id`, `username`, `email`, `preferred_language` FROM `users_savmrl` WHERE `id` = ?");
$stmt->bind_param("i", $result['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$c->close();

if ($user) {
    send_login_success_email($user['email'], $user['username']);
}

api_success('Login successful', [
    'session_id' => $result['session_id'],
    'user' => [
        'id' => $result['user_id'],
        'username' => $user ? $user['username'] : '',
        'email' => $user ? $user['email'] : $email,
        'preferred_language' => $user ? $user['preferred_language'] : 'en',
    ],
]);
