<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/api/v2/helpers.php");
global $redirect_table, $opened_table;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_method_not_allowed("POST");
}

$request = get_json_body();

$link = isset($request["link"]) ? trim($request["link"]) : null;
if ($link === null || $link === "") {
    api_bad_request("Missing required field: link");
}

$openings = isset($request["openings"]) ? $request["openings"] : null;
$date = isset($request["date"]) ? $request["date"] : null;
$access_code = isset($request["access_code"]) ? $request["access_code"] : null;
$custom_name = isset($request["name"]) ? validate_name($request["name"]) : null;

$openings = isValidNumber($openings);
$date = isValidDate($date);

if (!isValidUrl($link)) {
    api_bad_request("Links pointing to savmrl.it are not allowed");
}

if (!filter_var($link, FILTER_VALIDATE_URL)) {
    api_bad_request("Invalid URL format");
}

$authenticated_user = get_authenticated_user();
if (!$authenticated_user) {
    api_unauthorized('Authentication required to create links');
}
if (!(int)$authenticated_user['email_verified']) {
    api_unauthorized('Email verification required before creating links');
}
$user_id = (int)$authenticated_user['id'];

$c = get_db_connection();
$c->autocommit(false);

$ip_address = getIpAddress();

$access_code_hash = null;
$link_to_store = getGoodString($link);
if ($access_code !== null && $access_code !== "") {
    $access_code_hash = hash('sha512', $access_code);
    $link_to_store = encryptTextWithPassword($link, $access_code);
}

if ($custom_name !== null && $custom_name !== "") {
    $query_check = "SELECT * FROM `$redirect_table` WHERE `name` = ? FOR UPDATE";
    $stmt_check = $c->prepare($query_check);
    $stmt_check->bind_param("s", $custom_name);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $stmt_check->close();

    if ($result_check->num_rows > 0) {
        $c->rollback();
        $c->close();
        api_conflict("The custom name is already taken");
    }

    $new_name = $custom_name;
} else {
    $attempts = 20;
    $new_name = null;
    while ($attempts > 0) {
        $candidate = generateRandomString(5);
        $query_check = "SELECT * FROM `$redirect_table` WHERE `name` = ? FOR UPDATE";
        $stmt_check = $c->prepare($query_check);
        $stmt_check->bind_param("s", $candidate);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $stmt_check->close();

        if ($result_check->num_rows === 0) {
            $new_name = $candidate;
            break;
        }
        $c->rollback();
        $attempts--;
    }

    if ($new_name === null) {
        $c->close();
        api_error("Could not generate a unique short code. Try again");
    }
}

$query_insert = "INSERT INTO `$redirect_table` (`id`, `name`, `redirect_link`, `access_code`, `limit_times`, `expiry_date`, `inserted_timestamp`, `inserted_from_ip`, `reported`, `user_id`) VALUES (NULL, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?, NULL, ?)";
$stmt_insert = $c->prepare($query_insert);
$stmt_insert->bind_param("sssissi", $new_name, $link_to_store, $access_code_hash, $openings, $date, $ip_address, $user_id);

if (!$stmt_insert->execute()) {
    $c->rollback();
    $c->close();
    api_error("Failed to create the shortened link");
}
$stmt_insert->close();

$c->commit();
$c->close();

api_created("Link created successfully", [
    "name" => $new_name,
    "short_url" => "https://savmrl.it/r/" . $new_name,
    "original_url" => $link,
    "openings_limit" => $openings,
    "expiry_date" => $date,
    "has_access_code" => ($access_code !== null && $access_code !== ""),
]);

?>
