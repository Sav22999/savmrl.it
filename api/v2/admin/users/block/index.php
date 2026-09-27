<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/admin-auth.php");

admin_auth_start_session();
admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed("POST");
}

$data = get_json_body();
$user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
$block = isset($data['block']) ? (bool)$data['block'] : true;

if ($user_id <= 0) {
    api_bad_request('User ID is required');
}

$c = get_db_connection();

$val = $block ? 1 : 0;
$stmt = $c->prepare("UPDATE `users_savmrl` SET `blocked` = ? WHERE `id` = ?");
$stmt->bind_param("ii", $val, $user_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($block && $affected > 0) {
    $del = $c->prepare("DELETE FROM `sessions_savmrl` WHERE `user_id` = ?");
    $del->bind_param("i", $user_id);
    $del->execute();
    $del->close();
}

$c->close();

if ($affected === 0) {
    api_not_found('User not found or no change');
}

api_success('User ' . ($block ? 'blocked' : 'unblocked'), [
    'user_id' => $user_id,
    'blocked' => $block,
]);
