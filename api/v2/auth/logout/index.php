<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

auth_start_session();
$user = auth_get_current_user();
if (!$user) {
    api_unauthorized('Not authenticated');
}

auth_destroy_session();
api_success('Logged out successfully');
