<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/credentials.php");
global $redirect_table, $opened_table, $localhost_db, $username_db, $password_db, $database_savmrl;
header("Content-Type:application/json");
//$request = json_decode(file_get_contents('php://input'), true); //POST request
$request = $_GET; //GET request

$condition = true; //no conditions
if ($condition) {
    $found = false;
    $invalid = false;

    $response = null;

    //Using prepared statements -> it's the safest way for MySQL queries
    if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
        $c->set_charset("utf8mb4");

        // Snippet 1: Check if there are any rows with the ip_address in the last 30 days
        $query_exists = "SELECT (SELECT COUNT(*) FROM `$redirect_table` WHERE `inserted_from_ip` != '' AND `inserted_timestamp` < NOW() - INTERVAL 30 DAY) + (SELECT COUNT(*) FROM `$opened_table` WHERE `ip_address` != '' AND `visited_timestamp` < NOW() - INTERVAL 30 DAY) AS `total_records`;";
        $stmt_exists = $c->prepare($query_exists);
        //$stmt_exists->bind_param();
        if ($stmt_exists->execute()) {
            //successful
        } else {
            $invalid = true;
        }
        $result_exists = $stmt_exists->get_result();
        $stmt_exists->close();

        if ($result_exists->num_rows === 1) {
            $res = $result_exists->fetch_array();

            if ($res["total_records"] > 0) {
                // Aggiorna redirect_table
                $query_delete1 = "UPDATE `$redirect_table` SET `inserted_from_ip` = '' WHERE `inserted_from_ip` != '' AND `inserted_timestamp` < NOW() - INTERVAL 30 DAY";
                if ($c->query($query_delete1)) {
                    // Aggiorna opened_table
                    $query_delete2 = "UPDATE `$opened_table` SET `ip_address` = '' WHERE `ip_address` != '' AND `visited_timestamp` < NOW() - INTERVAL 30 DAY";
                    if ($c->query($query_delete2)) {
                        $found = true;
                        $response = echo_result($res["total_records"]);
                    } else {
                        $invalid = true;
                    }
                } else {
                    $invalid = true;
                }
            }
        }
        $c->close();
    }

    if ($invalid) {
        $response = echo_invalid();
    }

    echo json_encode($response);
} else {
    echo_null();
}

function echo_null()
{
    echo json_encode(null);
}

function echo_invalid()
{
    $response["code"] = "401";
    $response["status"] = "Error";
    $response["description"] = "Invalid link";
    return $response;
}

function echo_result($count)
{
    $response["code"] = "200";
    $response["status"] = "Successful";
    $data["deleting_number"] = $count;
    $response["data"] = $data;
    return $response;
}

?>