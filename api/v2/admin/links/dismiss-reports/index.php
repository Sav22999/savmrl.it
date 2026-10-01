<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/admin-auth.php");

admin_auth_start_session();
admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed("POST");
}

$data = get_json_body();
$name = isset($data['name']) ? validate_name($data['name']) : '';

if (empty($name)) {
    api_bad_request('Link name is required');
}

global $redirect_table;
$c = get_db_connection();

$stmt = $c->prepare("UPDATE `$redirect_table` SET `user_reports` = 0, `user_report_reason` = NULL WHERE `name` = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();
$c->close();

if ($affected === 0) {
    api_not_found('Link not found or no user reports');
}

api_success('User reports dismissed', ['name' => $name]);
