<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");
global $redirect_table, $opened_table;

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed("DELETE, POST");
}

$request = ($_SERVER['REQUEST_METHOD'] === 'DELETE') ? $_GET : get_json_body();

$name = isset($request["name"]) ? validate_name($request["name"]) : null;
if ($name === null || $name === "") {
    api_bad_request("Missing required field: name");
}

$ip_address = getIpAddress();
$authenticated_user = get_authenticated_user();

$c = get_db_connection();

if ($authenticated_user && (int)$authenticated_user['pro_features'] === 1) {
    $query_check = "SELECT * FROM `$redirect_table` WHERE `name` = ?";
} else {
    $query_check = "SELECT * FROM `$redirect_table` WHERE `name` = ? AND `inserted_timestamp` >= (CURRENT_TIMESTAMP - INTERVAL 30 MINUTE)";
}
$stmt_check = $c->prepare($query_check);
$stmt_check->bind_param("s", $name);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    $stmt_check->close();
    $c->close();
    api_not_found("Link doesn't exist or was created more than 30 minutes ago");
}

$link_row = $result_check->fetch_assoc();
$stmt_check->close();

$is_owner = $authenticated_user && $link_row['user_id'] && (int)$link_row['user_id'] === (int)$authenticated_user['id'];
$is_pro_owner = $is_owner && (int)$authenticated_user['pro_features'] === 1;

if ($link_row['admin_blocked']) {
    $c->close();
    api_unauthorized("This link has been blocked by an administrator and cannot be deleted");
}

if ($is_pro_owner) {
    $query_delete = "DELETE FROM `$redirect_table` WHERE `name` = ? AND `user_id` = ?";
    $stmt_delete = $c->prepare($query_delete);
    $stmt_delete->bind_param("si", $name, $authenticated_user['id']);
} else {
    $query_delete = "DELETE FROM `$redirect_table` WHERE `name` = ? AND `inserted_from_ip` = ?";
    $stmt_delete = $c->prepare($query_delete);
    $stmt_delete->bind_param("ss", $name, $ip_address);
}
$stmt_delete->execute();
$affected = $stmt_delete->affected_rows;
$stmt_delete->close();

if ($affected === 0) {
    $c->close();
    api_unauthorized("Unauthorized to delete this link");
}

$query_cleanup = "DELETE FROM `$opened_table` WHERE `name` = ?";
$stmt_cleanup = $c->prepare($query_cleanup);
$stmt_cleanup->bind_param("s", $name);
$stmt_cleanup->execute();
$stmt_cleanup->close();

$c->close();

api_success("Link deleted successfully", [
    "name" => $name,
]);
