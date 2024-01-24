<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/credentials.php");
global $redirect_table, $opened_table, $localhost_db, $username_db, $password_db, $database_savmrl;
header("Content-Type:application/json");
$post = json_decode(file_get_contents('php://input'), true);

$condition = isset($post["name"]);
if ($condition) {
    $name_to_use = $post["name"];

    $found = false;
    $invalid = false;

    $response = null;

    //Using prepared statements -> it's the safest way for MySQL queries
    if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
        $c->set_charset("utf8mb4");

        // Snippet 1: SELECT * FROM `redirect_savmrl` WHERE `name`='$name_to_use'
        $query_exists = "SELECT * FROM `$redirect_table` WHERE `name` = ?";
        $stmt_exists = $c->prepare($query_exists);
        $stmt_exists->bind_param("s", $name_to_use);
        if ($stmt_exists->execute()) {
            //successful
        } else {
            $invalid = true;
        }
        $result_exists = $stmt_exists->get_result();
        $stmt_exists->close();

        if ($result_exists->num_rows === 1) {
            $res = $result_exists->fetch_array();

            // Snippet 2: SELECT COUNT(*) AS `count` FROM `opened_savmrl` WHERE `name`='$name_to_use'
            $query_count = "SELECT COUNT(*) AS `count` FROM `$opened_table` WHERE `name` = ?";
            $stmt_count = $c->prepare($query_count);
            $stmt_count->bind_param("s", $name_to_use);
            $stmt_count->execute();
            $result_count = $stmt_count->get_result();
            $stmt_count->close();

            if ($result_count->num_rows > 0) {
                $found = true;

                while ($row = $result_count->fetch_array()) {
                    $response = echo_result($row['count'], $res["times_limit"], $res["date_expiry"], $res["inserted_timestamp"]);
                }
            }
        } else {
            //not found
        }
        $c->close();
    }

    if ($invalid) {
        $response = echo_invalid();
    }
    if (!$found) {
        $response = echo_not_exists();
    }

    echo json_encode($response);
} else {
    echo_null();
}

function echo_null()
{
    echo json_encode(null);
}

function echo_not_exists()
{
    $response["code"] = "404";
    $response["status"] = "Error";
    $response["description"] = "Page doesn't exist or has expired";
    return $response;
}

function echo_invalid()
{
    $response["code"] = "401";
    $response["status"] = "Error";
    $response["description"] = "Invalid link";
    return $response;
}

function echo_result($count, $openings, $date, $inserted_timestamp)
{
    $response["code"] = "200";
    $response["status"] = "Successful";
    $data["count"] = $count;
    $data["openings_expiry"] = $openings;
    $data["date_expiry"] = $date;
    $data["inserted_timestamp"] = $inserted_timestamp;
    $response["data"] = $data;
    return $response;
}

?>