<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_method_not_allowed('GET');
}

auth_start_session();
$user = auth_require_login();

if (!savmrl_is_export_open()) {
    api_bad_request('Export period has ended');
}

global $redirect_table, $opened_table;
$c = get_db_connection();

$stmt = $c->prepare("SELECT r.`name`, r.`redirect_link`, r.`limit_times`, r.`expiry_date`, r.`inserted_timestamp`, r.`access_code`, r.`redirect_seconds`, COALESCE(o.click_count, 0) AS click_count FROM `$redirect_table` r LEFT JOIN (SELECT `name`, COUNT(*) AS click_count FROM `$opened_table` GROUP BY `name`) o ON r.`name` = o.`name` WHERE r.`user_id` = ? ORDER BY r.`inserted_timestamp` DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();

$links = [];
while ($row = $result->fetch_assoc()) {
    $link = [
        'name' => $row['name'],
        'short_url' => SHORT_URL_BASE . $row['name'],
        'destination_url' => $row['redirect_link'],
        'has_access_code' => !empty($row['access_code']),
        'click_count' => (int)$row['click_count'],
        'created_at' => $row['inserted_timestamp'],
    ];
    if ($row['expiry_date']) $link['expiry_date'] = $row['expiry_date'];
    if ($row['limit_times']) $link['openings_limit'] = (int)$row['limit_times'];
    if ($row['redirect_seconds']) $link['redirect_seconds'] = (int)$row['redirect_seconds'];
    $links[] = $link;
}
$stmt->close();
$c->close();

$export = [
    'exported_at' => date('c'),
    'user' => [
        'email' => $user['email'],
        'username' => $user['username'],
    ],
    'total_links' => count($links),
    'links' => $links,
];

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="savmrl-export.json"');
echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;
