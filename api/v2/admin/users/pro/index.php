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
$pro = isset($data['pro_features']) ? (bool)$data['pro_features'] : false;

if ($user_id <= 0) {
    api_bad_request('User ID is required');
}

$c = get_db_connection();

$val = $pro ? 1 : 0;
$stmt = $c->prepare("UPDATE `users_savmrl` SET `pro_features` = ? WHERE `id` = ?");
$stmt->bind_param("ii", $val, $user_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();
$c->close();

if ($affected === 0) {
    api_not_found('User not found');
}

api_success('Pro features ' . ($pro ? 'enabled' : 'disabled'), [
    'user_id' => $user_id,
    'pro_features' => $pro,
]);
