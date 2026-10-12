<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

$data = get_json_body();
$username = isset($data['username']) ? trim($data['username']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';

if (empty($username) || empty($email) || empty($password)) {
    api_bad_request('Username, email, and password are required');
}

if (strlen($username) < 2) {
    api_bad_request('Username must be at least 2 characters');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    api_bad_request('Invalid email address');
}

if (strlen($password) < 8) {
    api_bad_request('Password must be at least 8 characters');
}

global $localhost_db, $username_db, $password_db, $database_savmrl;

$c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
if ($c->connect_error) {
    api_error('Database error');
}
$c->set_charset("utf8mb4");

$r = $c->query("SELECT COUNT(*) AS cnt FROM `admins_savmrl`");
if ($r && $row = $r->fetch_assoc()) {
    if ((int)$row['cnt'] > 0) {
        $c->close();
        api_response(403, "Error", "An administrator account already exists");
    }
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $c->prepare("INSERT INTO `admins_savmrl` (`username`, `email`, `password_hash`) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hash);

if (!$stmt->execute()) {
    $stmt->close();
    $c->close();
    api_error('Failed to create admin account');
}

$admin_id = $c->insert_id;
$stmt->close();
$c->close();

api_response(201, "Success", "Admin account created", ['admin_id' => $admin_id]);
