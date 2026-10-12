<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

auth_start_session();
$user = auth_require_login();

$data = get_json_body();
$session_id = isset($data['session_id']) ? $data['session_id'] : '';

if (empty($session_id)) {
    api_bad_request('Session ID is required');
}

$revoked = auth_revoke_session($session_id, $user['id']);
if (!$revoked) {
    api_not_found('Session not found');
}

api_success('Session revoked');
