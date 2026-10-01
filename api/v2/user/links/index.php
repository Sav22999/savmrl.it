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
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

global $redirect_table, $opened_table;
$c = get_db_connection();

$where = "r.`user_id` = ?";
$params = [$user['id']];
$types = "i";

if ($search !== '') {
    $like = "%" . $search . "%";
    $where .= " AND (r.`name` LIKE ? OR r.`redirect_link` LIKE ?)";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

$count_query = "SELECT COUNT(*) AS total FROM `$redirect_table` r WHERE $where";
$count_stmt = $c->prepare($count_query);
$count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

$order_clause = "r.`inserted_timestamp` DESC";
if ($sort === 'oldest') $order_clause = "r.`inserted_timestamp` ASC";
elseif ($sort === 'most-clicks') $order_clause = "click_count DESC";
elseif ($sort === 'least-clicks') $order_clause = "click_count ASC";
elseif ($sort === 'name-az') $order_clause = "r.`name` ASC";
elseif ($sort === 'name-za') $order_clause = "r.`name` DESC";

$has_admin_blocked = false;
$col_check = $c->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$redirect_table' AND COLUMN_NAME = 'admin_blocked'");
if ($col_check && $col_check->num_rows > 0) {
    $has_admin_blocked = true;
}

$has_redirect_seconds = false;
$col_check2 = $c->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$redirect_table' AND COLUMN_NAME = 'redirect_seconds'");
if ($col_check2 && $col_check2->num_rows > 0) {
    $has_redirect_seconds = true;
}

$admin_blocked_col = $has_admin_blocked ? "r.`admin_blocked`," : "";
$redirect_seconds_col = $has_redirect_seconds ? "r.`redirect_seconds`," : "";
$query = "SELECT r.`name`, r.`redirect_link`, r.`limit_times`, r.`expiry_date`, r.`inserted_timestamp`, r.`reported`, $admin_blocked_col $redirect_seconds_col r.`access_code` IS NOT NULL AS has_access_code, COALESCE(o.click_count, 0) AS click_count FROM `$redirect_table` r LEFT JOIN (SELECT `name`, COUNT(*) AS click_count FROM `$opened_table` GROUP BY `name`) o ON r.`name` = o.`name` WHERE $where ORDER BY $order_clause LIMIT ? OFFSET ?";
$all_params = $params;
$all_params[] = $per_page;
$all_params[] = $offset;
$all_types = $types . "ii";
$stmt = $c->prepare($query);
if (!$stmt) {
    $c->close();
    api_error('Database query error');
}
$stmt->bind_param($all_types, ...$all_params);
$stmt->execute();
$result = $stmt->get_result();

$links = [];
while ($row = $result->fetch_assoc()) {
    $is_expired = false;
    if ($row['expiry_date'] && strtotime($row['expiry_date']) < time()) $is_expired = true;
    if ($row['limit_times'] && (int)$row['click_count'] >= (int)$row['limit_times']) $is_expired = true;

    $links[] = [
        'name' => $row['name'],
        'short_url' => SHORT_URL_BASE . $row['name'],
        'original_url' => $row['redirect_link'],
        'click_count' => (int)$row['click_count'],
        'openings_limit' => $row['limit_times'] ? (int)$row['limit_times'] : null,
        'expiry_date' => $row['expiry_date'],
        'has_access_code' => (bool)$row['has_access_code'],
        'is_reported' => (bool)$row['reported'],
        'is_admin_blocked' => $has_admin_blocked ? (bool)$row['admin_blocked'] : false,
        'is_expired' => $is_expired,
        'redirect_seconds' => $has_redirect_seconds ? (int)($row['redirect_seconds'] ?? 0) : 0,
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
