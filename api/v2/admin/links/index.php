<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/admin-auth.php");

admin_auth_start_session();
admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_method_not_allowed("GET");
}

global $redirect_table, $opened_table;

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? min(50, max(1, (int)$_GET['per_page'])) : 20;
$offset = ($page - 1) * $per_page;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_from = isset($_GET['date_from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date_to']) ? $_GET['date_to'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$c = get_db_connection();

$has_admin_blocked = false;
$col_check = $c->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$redirect_table' AND COLUMN_NAME = 'admin_blocked'");
if ($col_check && $col_check->num_rows > 0) {
    $has_admin_blocked = true;
}

$has_user_reports = false;
$col_check_ur = $c->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$redirect_table' AND COLUMN_NAME = 'user_reports'");
if ($col_check_ur && $col_check_ur->num_rows > 0) {
    $has_user_reports = true;
}

$where = "1=1";
$params = [];
$types = "";

if ($filter === 'reported') {
    $where .= " AND r.`reported` = 1";
    if ($has_admin_blocked) { $where .= " AND (r.`admin_blocked` IS NULL OR r.`admin_blocked` = 0)"; }
}
elseif ($filter === 'user_reported' && $has_user_reports) {
    $where .= " AND r.`user_reports` > 0";
    if ($has_admin_blocked) { $where .= " AND (r.`admin_blocked` IS NULL OR r.`admin_blocked` = 0)"; }
}
elseif ($filter === 'admin_blocked' && $has_admin_blocked) { $where .= " AND r.`admin_blocked` = 1"; }
elseif ($filter === 'active') {
    $where .= " AND (r.`reported` IS NULL OR r.`reported` = 0)";
    if ($has_admin_blocked) { $where .= " AND (r.`admin_blocked` IS NULL OR r.`admin_blocked` = 0)"; }
    $where .= " AND (r.`expiry_date` IS NULL OR r.`expiry_date` >= CURDATE())";
}
elseif ($filter === 'expired') { $where .= " AND r.`expiry_date` IS NOT NULL AND r.`expiry_date` < CURDATE()"; }

if ($search !== '') {
    $like = "%" . $search . "%";
    $where .= " AND (r.`name` LIKE ? OR r.`redirect_link` LIKE ? OR r.`inserted_from_ip` LIKE ?)";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

if ($date_from !== '') {
    $where .= " AND DATE(r.`inserted_timestamp`) >= ?";
    $params[] = $date_from;
    $types .= "s";
}
if ($date_to !== '') {
    $where .= " AND DATE(r.`inserted_timestamp`) <= ?";
    $params[] = $date_to;
    $types .= "s";
}

$count_query = "SELECT COUNT(*) AS total FROM `$redirect_table` r WHERE $where";
$count_stmt = $c->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

$order_clause = "r.`inserted_timestamp` DESC";
if ($sort === 'oldest') $order_clause = "r.`inserted_timestamp` ASC";
elseif ($sort === 'most-clicks') $order_clause = "click_count DESC";
elseif ($sort === 'least-clicks') $order_clause = "click_count ASC";
elseif ($sort === 'name-az') $order_clause = "r.`name` ASC";
elseif ($sort === 'name-za') $order_clause = "r.`name` DESC";

$user_reports_cols = $has_user_reports ? " r.`user_reports`, r.`user_report_reason`," : "";
$query = "SELECT r.`id`, r.`name`, r.`redirect_link`, r.`limit_times`, r.`expiry_date`, r.`inserted_timestamp`, r.`inserted_from_ip`, r.`reported`, r.`user_id`," . ($has_admin_blocked ? " r.`admin_blocked`," : "") . $user_reports_cols . " COALESCE(o.click_count, 0) AS click_count, u.email AS user_email FROM `$redirect_table` r LEFT JOIN (SELECT `name`, COUNT(*) AS click_count FROM `$opened_table` GROUP BY `name`) o ON r.`name` = o.`name` LEFT JOIN `users_savmrl` u ON r.`user_id` = u.`id` WHERE $where ORDER BY $order_clause LIMIT ? OFFSET ?";

$all_params = $params;
$all_params[] = $per_page;
$all_params[] = $offset;
$all_types = $types . "ii";

$stmt = $c->prepare($query);
$stmt->bind_param($all_types, ...$all_params);
$stmt->execute();
$result = $stmt->get_result();

$links = [];
while ($row = $result->fetch_assoc()) {
    $links[] = [
        'name' => $row['name'],
        'short_url' => SHORT_URL_BASE . $row['name'],
        'original_url' => $row['redirect_link'],
        'click_count' => (int)$row['click_count'],
        'openings_limit' => $row['limit_times'] ? (int)$row['limit_times'] : null,
        'expiry_date' => $row['expiry_date'],
        'is_reported' => (bool)$row['reported'],
        'is_admin_blocked' => $has_admin_blocked && isset($row['admin_blocked']) ? (bool)$row['admin_blocked'] : false,
        'user_reports' => $has_user_reports ? (int)($row['user_reports'] ?? 0) : 0,
        'user_report_reason' => $has_user_reports ? ($row['user_report_reason'] ?? null) : null,
        'is_expired' => ($row['expiry_date'] && $row['expiry_date'] < date('Y-m-d')),
        'created_at' => $row['inserted_timestamp'],
        'created_from_ip' => $row['inserted_from_ip'],
        'user_email' => $row['user_email'],
        'user_id' => $row['user_id'] ? (int)$row['user_id'] : null,
    ];
}
$stmt->close();
$c->close();

api_success('Links retrieved', [
    'links' => $links,
    'total' => (int)$total,
    'page' => $page,
    'per_page' => $per_page,
    'filter' => $filter,
]);
