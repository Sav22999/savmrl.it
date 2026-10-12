<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

auth_start_session();
$user = auth_require_login();

$data = get_json_body();
$lang = isset($data['language']) ? $data['language'] : '';

$supported = ['en', 'it', 'fr', 'de', 'es'];
if (!in_array($lang, $supported)) {
    api_bad_request('Unsupported language. Supported: ' . implode(', ', $supported));
}

$conn = get_db_connection();
$stmt = $conn->prepare("UPDATE `users_savmrl` SET `preferred_language` = ? WHERE `id` = ?");
$stmt->bind_param('si', $lang, $user['id']);
$stmt->execute();
$stmt->close();
$conn->close();

api_success('Language preference updated', ['language' => $lang]);
