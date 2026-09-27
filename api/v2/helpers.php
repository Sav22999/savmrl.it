<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/credentials.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/auth.php");

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: https://savmrl.it");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function api_response($http_code, $status, $description, $data = null) {
    http_response_code($http_code);
    $response = [
        "code" => (string) $http_code,
        "status" => $status,
        "timestamp" => getTimestamp(),
        "description" => $description,
    ];
    if ($data !== null) {
        $response["data"] = $data;
    }
    echo json_encode($response);
    exit;
}

function api_success($description, $data = null) {
    api_response(200, "Successful", $description, $data);
}

function api_created($description, $data = null) {
    api_response(201, "Created", $description, $data);
}

function api_bad_request($description) {
    api_response(400, "Error", $description);
}

function api_unauthorized($description) {
    api_response(403, "Error", $description);
}

function api_not_found($description) {
    api_response(404, "Error", $description);
}

function api_conflict($description) {
    api_response(409, "Error", $description);
}

function api_error($description) {
    api_response(500, "Error", $description);
}

function api_method_not_allowed($allowed) {
    header("Allow: " . $allowed);
    api_response(405, "Error", "Method not allowed. Use: " . $allowed);
}

function get_json_body() {
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    if ($data === null && $body !== "" && $body !== "null") {
        api_bad_request("Invalid JSON body");
    }
    return $data ?? [];
}

function get_db_connection() {
    global $localhost_db, $username_db, $password_db, $database_savmrl;
    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) {
        api_error("Database connection failed");
    }
    $c->set_charset("utf8mb4");
    return $c;
}

function validate_name($name) {
    $validated = preg_replace('/[^0-9a-zA-Z\-]/', '', $name);
    return substr($validated, 0, 100);
}

function get_authenticated_user() {
    auth_start_session();
    return auth_get_current_user();
}

?>
