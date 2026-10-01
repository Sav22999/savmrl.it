<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/admin-auth.php");

admin_auth_start_session();
admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed("POST");
}

$data = get_json_body();
$user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
$block = isset($data['block']) ? (bool)$data['block'] : true;
$block_links = isset($data['block_links']) ? (bool)$data['block_links'] : false;

if ($user_id <= 0) {
    api_bad_request('User ID is required');
}

$c = get_db_connection();

$check = $c->prepare("SELECT `id` FROM `users_savmrl` WHERE `id` = ?");
$check->bind_param("i", $user_id);
$check->execute();
$user = $check->get_result()->fetch_assoc();
$check->close();

if (!$user) {
    $c->close();
    api_not_found('User not found');
}

$val = $block ? 1 : 0;
$stmt = $c->prepare("UPDATE `users_savmrl` SET `blocked` = ? WHERE `id` = ?");
$stmt->bind_param("ii", $val, $user_id);

if (!$stmt->execute()) {
    $stmt->close();
    $c->close();
    api_error('Failed to update user');
}
$stmt->close();

$links_blocked = 0;
if ($block) {
    $del = $c->prepare("DELETE FROM `sessions_savmrl` WHERE `user_id` = ?");
    $del->bind_param("i", $user_id);
    $del->execute();
    $del->close();

    if ($block_links) {
        global $redirect_table;
        $bl = $c->prepare("UPDATE `$redirect_table` SET `admin_blocked` = 1 WHERE `user_id` = ? AND (`admin_blocked` IS NULL OR `admin_blocked` = 0)");
        $bl->bind_param("i", $user_id);
        $bl->execute();
        $links_blocked = $bl->affected_rows;
        $bl->close();
    }
}

$c->close();

api_success('User ' . ($block ? 'blocked' : 'unblocked'), [
    'user_id' => $user_id,
    'blocked' => $block,
    'links_blocked' => $links_blocked,
]);
