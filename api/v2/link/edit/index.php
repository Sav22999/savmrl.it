<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");
global $redirect_table, $opened_table;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed("POST");
}

$request = get_json_body();

$old_name = isset($request["old_name"]) ? validate_name($request["old_name"]) : null;
$new_name = isset($request["new_name"]) ? validate_name($request["new_name"]) : null;

if ($old_name === null || $old_name === "" || $new_name === null || $new_name === "") {
    api_bad_request("Missing required fields: old_name, new_name");
}

if ($old_name === $new_name) {
    api_bad_request("The new name must be different from the old name");
}

$ip_address = getIpAddress();
$authenticated_user = get_authenticated_user();

$c = get_db_connection();

$c->query("LOCK TABLES `$redirect_table` WRITE, `$opened_table` WRITE");

$is_pro_owner = false;
if ($authenticated_user && (int)$authenticated_user['pro_features'] === 1) {
    $query_old = "SELECT * FROM `$redirect_table` WHERE `name` = ?";
} else {
    $query_old = "SELECT * FROM `$redirect_table` WHERE `name` = ? AND `inserted_timestamp` >= (CURRENT_TIMESTAMP - INTERVAL 30 MINUTE)";
}
$stmt_old = $c->prepare($query_old);
$stmt_old->bind_param("s", $old_name);
$stmt_old->execute();
$result_old = $stmt_old->get_result();

if ($result_old->num_rows === 0) {
    $stmt_old->close();
    $c->query("UNLOCK TABLES");
    $c->close();
    api_not_found("Link doesn't exist or was created more than 30 minutes ago");
}

$link_row = $result_old->fetch_assoc();
$stmt_old->close();

$is_owner = $authenticated_user && $link_row['user_id'] && (int)$link_row['user_id'] === (int)$authenticated_user['id'];
$is_pro_owner = $is_owner && (int)$authenticated_user['pro_features'] === 1;

if ($link_row['admin_blocked']) {
    $c->query("UNLOCK TABLES");
    $c->close();
    api_unauthorized("This link has been blocked by an administrator");
}

$query_new = "SELECT COUNT(*) AS c FROM `$redirect_table` WHERE `name` = ?";
$stmt_new = $c->prepare($query_new);
$stmt_new->bind_param("s", $new_name);
$stmt_new->execute();
$result_new = $stmt_new->get_result();
$row_new = $result_new->fetch_array();
$stmt_new->close();

if ($row_new["c"] > 0) {
    $c->query("UNLOCK TABLES");
    $c->close();
    api_conflict("The name chosen is already taken");
}

if ($is_pro_owner) {
    $query_update = "UPDATE `$redirect_table` SET `name` = ? WHERE `name` = ? AND `user_id` = ?";
    $stmt_update = $c->prepare($query_update);
    $stmt_update->bind_param("ssi", $new_name, $old_name, $authenticated_user['id']);
} else {
    $query_update = "UPDATE `$redirect_table` SET `name` = ? WHERE `name` = ? AND `inserted_from_ip` = ?";
    $stmt_update = $c->prepare($query_update);
    $stmt_update->bind_param("sss", $new_name, $old_name, $ip_address);
}
$stmt_update->execute();
$affected = $stmt_update->affected_rows;
$stmt_update->close();

if ($affected === 0) {
    $c->query("UNLOCK TABLES");
    $c->close();
    api_unauthorized("Unauthorized to edit this link");
}

$query_update2 = "UPDATE `$opened_table` SET `name` = ? WHERE `name` = ?";
$stmt_update2 = $c->prepare($query_update2);
$stmt_update2->bind_param("ss", $new_name, $old_name);
$stmt_update2->execute();
$stmt_update2->close();

$c->query("UNLOCK TABLES");
$c->close();

api_success("Link renamed successfully", [
    "old_name" => $old_name,
    "new_name" => $new_name,
    "short_url" => SHORT_URL_BASE . $new_name,
]);
