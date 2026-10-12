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

$query = "SELECT t1.name, t1.limit_times, t1.expiry_date, t1.inserted_timestamp, t1.access_code IS NOT NULL AS has_access_code, t1.reported,
          COALESCE(t2.click_count, 0) AS click_count
          FROM `$redirect_table` AS t1
          LEFT JOIN (SELECT name, COUNT(*) AS click_count FROM `$opened_table` GROUP BY name) AS t2
          ON t1.name = t2.name
          WHERE t1.name = ?";
$stmt = $c->prepare($query);
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows === 0) {
    $c->close();
    api_not_found("Link doesn't exist");
}

$row = $result->fetch_assoc();
$c->close();

$is_expired = false;
if ($row["limit_times"] !== null && $row["click_count"] >= (int) $row["limit_times"]) {
    $is_expired = true;
}
if ($row["expiry_date"] !== null && strtotime($row["expiry_date"]) < strtotime(date('Y-m-d'))) {
    $is_expired = true;
}

api_success("Link info retrieved", [
    "name" => $row["name"],
    "short_url" => SHORT_URL_BASE . $row["name"],
    "click_count" => (int) $row["click_count"],
    "openings_limit" => $row["limit_times"],
    "expiry_date" => $row["expiry_date"],
    "has_access_code" => (bool) $row["has_access_code"],
    "is_reported" => $row["reported"] === 1,
    "is_expired" => $is_expired,
    "created_at" => $row["inserted_timestamp"],
]);

?>
