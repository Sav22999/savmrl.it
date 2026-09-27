<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

$token = isset($_GET['token']) ? $_GET['token'] : '';
if (empty($token)) {
    api_bad_request('Verification token is required');
}

global $localhost_db, $username_db, $password_db, $database_savmrl;
$c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
if ($c->connect_error) api_error('Database error');
$c->set_charset("utf8mb4");

$stmt = $c->prepare("SELECT o.user_id FROM `otp_codes_savmrl` o WHERE o.temp_token = ? AND o.purpose = 'email_verification' AND o.used = 0 AND o.expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $c->close();
    api_bad_request('Invalid or expired verification token');
}

$row = $result->fetch_assoc();
$user_id = $row['user_id'];
$stmt->close();

$upd = $c->prepare("UPDATE `users_savmrl` SET `email_verified` = 1 WHERE `id` = ?");
$upd->bind_param("i", $user_id);
$upd->execute();
$upd->close();

$mark = $c->prepare("UPDATE `otp_codes_savmrl` SET `used` = 1 WHERE `temp_token` = ?");
$mark->bind_param("s", $token);
$mark->execute();
$mark->close();

$c->close();

api_success('Email verified successfully');
