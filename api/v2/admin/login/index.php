<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/admin-auth.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

$data = get_json_body();
$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? $data['password'] : '';

if (empty($username) || empty($password)) {
    api_bad_request('Username and password are required');
}

$result = admin_auth_login($username, $password);

if (isset($result['error'])) {
    api_unauthorized($result['error']);
}

api_success('Login successful', ['admin_id' => $result['admin_id']]);
