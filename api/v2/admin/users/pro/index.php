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
$pro = isset($data['enable']) ? (bool)$data['enable'] : (isset($data['pro_features']) ? (bool)$data['pro_features'] : false);

if ($user_id <= 0) {
    api_bad_request('User ID is required');
}

$c = get_db_connection();

$check = $c->prepare("SELECT `id`, `pro_features` FROM `users_savmrl` WHERE `id` = ?");
$check->bind_param("i", $user_id);
$check->execute();
$user = $check->get_result()->fetch_assoc();
$check->close();

if (!$user) {
    $c->close();
    api_not_found('User not found');
}

$val = $pro ? 1 : 0;
$stmt = $c->prepare("UPDATE `users_savmrl` SET `pro_features` = ? WHERE `id` = ?");
$stmt->bind_param("ii", $val, $user_id);

if (!$stmt->execute()) {
    $stmt->close();
    $c->close();
    api_error('Failed to update pro features');
}
$stmt->close();
$c->close();

api_success('Pro features ' . ($pro ? 'enabled' : 'disabled'), [
    'user_id' => $user_id,
    'pro_features' => $pro,
]);
