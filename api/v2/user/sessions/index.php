<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_method_not_allowed('GET');
}

auth_start_session();
$user = auth_require_login();

$sessions = auth_get_user_sessions($user['id']);

$formatted = [];
foreach ($sessions as $s) {
    $formatted[] = [
        'id' => $s['id'],
        'ip_address' => $s['ip_address'],
        'user_agent' => $s['user_agent'],
        'created_at' => $s['created_at'],
        'last_activity' => $s['last_activity'],
    ];
}

api_success('Sessions retrieved', ['sessions' => $formatted]);
