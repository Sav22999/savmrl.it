<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

auth_start_session();
$user = auth_require_login();

$data = get_json_body();
$name = isset($data['name']) ? validate_name($data['name']) : '';

if (empty($name)) {
    api_bad_request('Link name is required');
}

global $redirect_table;
$c = get_db_connection();

$stmt = $c->prepare("SELECT `user_id`, `admin_blocked` FROM `$redirect_table` WHERE `name` = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $c->close();
    api_not_found('Link not found');
}

$link = $result->fetch_assoc();
$stmt->close();

if ((int)$link['user_id'] !== (int)$user['id']) {
    $c->close();
    api_unauthorized('You do not own this link');
}

if ($link['admin_blocked']) {
    $c->close();
    api_unauthorized('This link has been blocked by an administrator');
}

$yesterday = date('Y-m-d', strtotime('-1 day'));
$upd = $c->prepare("UPDATE `$redirect_table` SET `expiry_date` = ? WHERE `name` = ? AND `user_id` = ?");
$upd->bind_param("ssi", $yesterday, $name, $user['id']);
$upd->execute();
$upd->close();
$c->close();

api_success('Link expired successfully', [
    'name' => $name,
    'expiry_date' => $yesterday,
]);
