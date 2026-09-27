<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_method_not_allowed('GET');
}

auth_start_session();
$user = auth_require_login();

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? min(50, max(1, (int)$_GET['per_page'])) : 20;
$offset = ($page - 1) * $per_page;

global $redirect_table, $opened_table;
$c = get_db_connection();

$count_stmt = $c->prepare("SELECT COUNT(*) AS total FROM `$redirect_table` WHERE `user_id` = ?");
$count_stmt->bind_param("i", $user['id']);
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

$stmt = $c->prepare("SELECT r.`name`, r.`redirect_link`, r.`limit_times`, r.`expiry_date`, r.`inserted_timestamp`, r.`reported`, r.`admin_blocked`, r.`access_code` IS NOT NULL AS has_access_code, COALESCE(o.click_count, 0) AS click_count FROM `$redirect_table` r LEFT JOIN (SELECT `name`, COUNT(*) AS click_count FROM `$opened_table` GROUP BY `name`) o ON r.`name` = o.`name` WHERE r.`user_id` = ? ORDER BY r.`inserted_timestamp` DESC LIMIT ? OFFSET ?");
$stmt->bind_param("iii", $user['id'], $per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

$links = [];
while ($row = $result->fetch_assoc()) {
    $is_expired = false;
    if ($row['expiry_date'] && strtotime($row['expiry_date']) < time()) $is_expired = true;
    if ($row['limit_times'] && (int)$row['click_count'] >= (int)$row['limit_times']) $is_expired = true;

    $links[] = [
        'name' => $row['name'],
        'short_url' => 'https://savmrl.it/r/' . $row['name'],
        'original_url' => $row['redirect_link'],
        'click_count' => (int)$row['click_count'],
        'openings_limit' => $row['limit_times'] ? (int)$row['limit_times'] : null,
        'expiry_date' => $row['expiry_date'],
        'has_access_code' => (bool)$row['has_access_code'],
        'is_reported' => (bool)$row['reported'],
        'is_admin_blocked' => (bool)$row['admin_blocked'],
        'is_expired' => $is_expired,
        'created_at' => $row['inserted_timestamp'],
    ];
}
$stmt->close();
$c->close();

api_success('Links retrieved', [
    'links' => $links,
    'total' => (int)$total,
    'page' => $page,
    'per_page' => $per_page,
]);
