<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");
global $redirect_table, $opened_table;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_method_not_allowed("GET");
}

$name = isset($_GET["name"]) ? validate_name($_GET["name"]) : null;
if ($name === null || $name === "") {
    api_bad_request("Missing required parameter: name");
}

$c = get_db_connection();

$query_exists = "SELECT * FROM `$redirect_table` WHERE `name` = ?";
$stmt_exists = $c->prepare($query_exists);
$stmt_exists->bind_param("s", $name);
$stmt_exists->execute();
$result_exists = $stmt_exists->get_result();
$stmt_exists->close();

if ($result_exists->num_rows === 0) {
    $c->close();
    api_not_found("Link doesn't exist");
}

$link_row = $result_exists->fetch_assoc();

$query_count = "SELECT COUNT(*) AS `count` FROM `$opened_table` WHERE `name` = ?";
$stmt_count = $c->prepare($query_count);
$stmt_count->bind_param("s", $name);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$count_row = $result_count->fetch_assoc();
$stmt_count->close();

$query_recent = "SELECT `visited_timestamp` FROM `$opened_table` WHERE `name` = ? ORDER BY `visited_timestamp` DESC LIMIT 10";
$stmt_recent = $c->prepare($query_recent);
$stmt_recent->bind_param("s", $name);
$stmt_recent->execute();
$result_recent = $stmt_recent->get_result();
$recent_clicks = [];
while ($row = $result_recent->fetch_assoc()) {
    $recent_clicks[] = $row["visited_timestamp"];
}
$stmt_recent->close();

$c->close();

api_success("Statistics retrieved", [
    "name" => $name,
    "short_url" => "https://savmrl.it/r/" . $name,
    "total_clicks" => (int) $count_row["count"],
    "openings_limit" => $link_row["limit_times"],
    "expiry_date" => $link_row["expiry_date"],
    "created_at" => $link_row["inserted_timestamp"],
    "recent_clicks" => $recent_clicks,
]);

?>
