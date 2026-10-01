<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/admin-auth.php");

admin_auth_start_session();
admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_method_not_allowed("GET");
}

global $redirect_table;

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? min(50, max(1, (int)$_GET['per_page'])) : 20;
$offset = ($page - 1) * $per_page;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$c = get_db_connection();

$where = "1=1";
$params = [];
$types = "";

if ($search !== '') {
    $like = "%" . $search . "%";
    $where .= " AND (u.`email` LIKE ? OR u.`username` LIKE ?)";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

$count_query = "SELECT COUNT(*) AS total FROM `users_savmrl` u WHERE $where";
$count_stmt = $c->prepare($count_query);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

$order_clause = "u.`created_at` DESC";
if ($sort === 'oldest') $order_clause = "u.`created_at` ASC";
elseif ($sort === 'most-links') $order_clause = "link_count DESC";
elseif ($sort === 'least-links') $order_clause = "link_count ASC";
elseif ($sort === 'username-az') $order_clause = "u.`username` ASC";
elseif ($sort === 'username-za') $order_clause = "u.`username` DESC";

$query = "SELECT u.*, (SELECT COUNT(*) FROM `$redirect_table` WHERE `user_id` = u.`id`) AS link_count FROM `users_savmrl` u WHERE $where ORDER BY $order_clause LIMIT ? OFFSET ?";

$all_params = $params;
$all_params[] = $per_page;
$all_params[] = $offset;
$all_types = $types . "ii";

$stmt = $c->prepare($query);
$stmt->bind_param($all_types, ...$all_params);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        'id' => (int)$row['id'],
        'email' => $row['email'],
        'username' => $row['username'],
        'email_verified' => (bool)$row['email_verified'],
        'pro_features' => (bool)$row['pro_features'],
        'two_fa_enabled' => (bool)$row['two_fa_enabled'],
        'blocked' => isset($row['blocked']) ? (int)$row['blocked'] : 0,
        'link_count' => (int)$row['link_count'],
        'created_at' => $row['created_at'],
    ];
}
$stmt->close();
$c->close();

api_success('Users retrieved', [
    'users' => $users,
    'total' => (int)$total,
    'page' => $page,
    'per_page' => $per_page,
]);
