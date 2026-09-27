<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed('POST');
}

auth_start_session();
$user = auth_require_login();

if (!(int)$user['pro_features']) {
    api_unauthorized('Pro features required to update link settings');
}

$data = get_json_body();
$name = isset($data['name']) ? validate_name($data['name']) : '';

if (empty($name)) {
    api_bad_request('Link name is required');
}

global $redirect_table;
$c = get_db_connection();

$stmt = $c->prepare("SELECT * FROM `$redirect_table` WHERE `name` = ? AND `user_id` = ?");
$stmt->bind_param("si", $name, $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $c->close();
    api_not_found('Link not found or not owned by you');
}

$link = $result->fetch_assoc();
$stmt->close();

if ($link['admin_blocked']) {
    $c->close();
    api_unauthorized('This link has been blocked by an administrator');
}

$updates = [];
$params = [];
$types = "";

if (isset($data['destination_url']) && $data['destination_url'] !== '') {
    $new_url = filter_var($data['destination_url'], FILTER_VALIDATE_URL);
    if (!$new_url) {
        $c->close();
        api_bad_request('Invalid destination URL');
    }
    if ($link['access_code'] !== null) {
        if (!isset($data['access_code']) || $data['access_code'] === '') {
            $c->close();
            api_bad_request('Access code is required to change the destination of a protected link');
        }
        $code_hash = hash('sha512', $data['access_code']);
        if ($code_hash !== $link['access_code']) {
            $c->close();
            api_unauthorized('Invalid access code');
        }
        $new_url = encryptTextWithPassword($new_url, $data['access_code']);
    }
    $updates[] = "`redirect_link` = ?";
    $params[] = $new_url;
    $types .= "s";
}

if (isset($data['expiry_date'])) {
    $date = isValidDate($data['expiry_date']);
    $updates[] = "`expiry_date` = ?";
    $params[] = $date;
    $types .= "s";
}

if (isset($data['openings_limit'])) {
    $openings = isValidNumber($data['openings_limit']);
    $updates[] = "`limit_times` = ?";
    $params[] = $openings;
    $types .= "i";
}

if (isset($data['access_code']) && $data['access_code'] !== '') {
    $new_hash = hash('sha512', $data['access_code']);

    $destination_already_handled = isset($data['destination_url']) && $data['destination_url'] !== '';
    if (!$destination_already_handled && $link['access_code'] !== null) {
        $new_link = encryptTextWithPassword($link['redirect_link'], $data['access_code']);
        $updates[] = "`redirect_link` = ?";
        $params[] = $new_link;
        $types .= "s";
    }

    $updates[] = "`access_code` = ?";
    $params[] = $new_hash;
    $types .= "s";
}

if (empty($updates)) {
    $c->close();
    api_bad_request('No settings to update');
}

$query = "UPDATE `$redirect_table` SET " . implode(", ", $updates) . " WHERE `name` = ? AND `user_id` = ?";
$params[] = $name;
$params[] = $user['id'];
$types .= "si";

$upd = $c->prepare($query);
$upd->bind_param($types, ...$params);
$upd->execute();
$upd->close();
$c->close();

api_success('Link settings updated', ['name' => $name]);
