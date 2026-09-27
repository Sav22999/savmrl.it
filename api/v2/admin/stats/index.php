<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/admin-auth.php");

admin_auth_start_session();
admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_method_not_allowed("GET");
}

global $redirect_table, $opened_table;
$c = get_db_connection();

$total_links = $c->query("SELECT COUNT(*) AS c FROM `$redirect_table`")->fetch_assoc()['c'];
$total_clicks = $c->query("SELECT COUNT(*) AS c FROM `$opened_table`")->fetch_assoc()['c'];
$total_users = $c->query("SELECT COUNT(*) AS c FROM `users_savmrl`")->fetch_assoc()['c'];
$reported_links = $c->query("SELECT COUNT(*) AS c FROM `$redirect_table` WHERE `reported` = 1")->fetch_assoc()['c'];
$blocked_links = $c->query("SELECT COUNT(*) AS c FROM `$redirect_table` WHERE `admin_blocked` = 1")->fetch_assoc()['c'];
$active_links = $c->query("SELECT COUNT(*) AS c FROM `$redirect_table` WHERE (`reported` IS NULL OR `reported` = 0) AND (`admin_blocked` IS NULL OR `admin_blocked` = 0) AND (`expiry_date` IS NULL OR `expiry_date` >= CURDATE())")->fetch_assoc()['c'];

$c->close();

api_success('Platform stats', [
    'total_links' => (int)$total_links,
    'total_clicks' => (int)$total_clicks,
    'total_users' => (int)$total_users,
    'reported_links' => (int)$reported_links,
    'blocked_links' => (int)$blocked_links,
    'active_links' => (int)$active_links,
]);
