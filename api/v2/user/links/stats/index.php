<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/v2/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_method_not_allowed('GET');
}

auth_start_session();
$user = auth_require_login();

$name = isset($_GET['name']) ? validate_name($_GET['name']) : '';
if (empty($name)) {
    api_bad_request('Link name is required');
}

global $redirect_table, $opened_table;
$c = get_db_connection();

$stmt = $c->prepare("SELECT `user_id`, `redirect_link`, `limit_times`, `expiry_date`, `inserted_timestamp`, `reported`, `admin_blocked` FROM `$redirect_table` WHERE `name` = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $c->close();
    api_not_found('Link not found');
}

$link = $result->fetch_assoc();
$stmt->close();

if ((int)$link['user_id'] !== (int)$user['id']) {
    $c->close();
    api_unauthorized('You do not own this link');
}

$count_stmt = $c->prepare("SELECT COUNT(*) AS total FROM `$opened_table` WHERE `name` = ?");
$count_stmt->bind_param("s", $name);
$count_stmt->execute();
$total_clicks = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

$daily_stmt = $c->prepare("SELECT DATE(`visited_timestamp`) AS click_date, COUNT(*) AS click_count FROM `$opened_table` WHERE `name` = ? GROUP BY DATE(`visited_timestamp`) ORDER BY click_date DESC LIMIT 30");
$daily_stmt->bind_param("s", $name);
$daily_stmt->execute();
$daily_result = $daily_stmt->get_result();
$clicks_by_day = [];
while ($row = $daily_result->fetch_assoc()) {
    $clicks_by_day[] = ['date' => $row['click_date'], 'count' => (int)$row['click_count']];
}
$daily_stmt->close();

$recent_stmt = $c->prepare("SELECT `visited_timestamp` FROM `$opened_table` WHERE `name` = ? ORDER BY `visited_timestamp` DESC LIMIT 10");
$recent_stmt->bind_param("s", $name);
$recent_stmt->execute();
$recent_result = $recent_stmt->get_result();
$recent_clicks = [];
while ($row = $recent_result->fetch_assoc()) {
    $recent_clicks[] = $row['visited_timestamp'];
}
$recent_stmt->close();

$c->close();

api_success('Stats retrieved', [
    'name' => $name,
    'original_url' => $link['redirect_link'],
    'total_clicks' => (int)$total_clicks,
    'openings_limit' => $link['limit_times'] ? (int)$link['limit_times'] : null,
    'expiry_date' => $link['expiry_date'],
    'is_reported' => (bool)$link['reported'],
    'is_admin_blocked' => (bool)$link['admin_blocked'],
    'created_at' => $link['inserted_timestamp'],
    'clicks_by_day' => $clicks_by_day,
    'recent_clicks' => $recent_clicks,
]);
