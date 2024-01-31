<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/credentials.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php");
global $redirect_table, $opened_table, $localhost_db, $username_db, $password_db, $database_savmrl;
header("Content-Type:application/json");
$request = json_decode(file_get_contents('php://input'), true); //POST request
//$request = $_GET; //GET request

$condition = isset($request["old_name"]) && isset($request["new_name"]);
if ($condition) {
    $old_name = getValidatedNewName($request["old_name"]);
    $new_name = getValidatedNewName($request["new_name"]);

    $ip_address = getIpAddress();

    $found = false;
    $invalid = false;
    $already_taken = false;
    $unauthorized = false;

    $response = null;

    //Using prepared statements -> it's the safest way for MySQL queries
    if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
        $c->set_charset("utf8mb4");

        // Lock the tables for both SELECT and UPDATE
        $c->query("LOCK TABLES `$redirect_table` READ, `$redirect_table` WRITE");

        //"c1" ("old name")
        //"c2" ("new name")
        $query_exists = "SELECT COUNT(*) AS `c1`, (SELECT COUNT(*) FROM `$redirect_table` WHERE `name` = ?) AS `c2` FROM `$redirect_table` WHERE `name` = ?";
        $stmt_exists = $c->prepare($query_exists);
        $stmt_exists->bind_param("ss", $new_name, $old_name);

        if ($stmt_exists->execute()) {
            $found = true;
            //successful
            $result_exists = $stmt_exists->get_result();
            $stmt_exists->close();

            if ($result_exists->num_rows === 1) {
                $res = $result_exists->fetch_array();

                if ($res["c1"] === 1 && $res["c2"] === 0) {
                    $query_update = "UPDATE `$redirect_table` SET `name` = ? WHERE `name` = ? AND `inserted_from_ip` = ?";
                    $query_update2 = "UPDATE `$opened_table` SET `name` = ? WHERE `name` = ?";

                    $stmt_update = $c->prepare($query_update);
                    $stmt_update->bind_param("sss", $new_name, $old_name, $ip_address);
                    if ($stmt_update->execute()) {
                        $affected_rows = $stmt_update->affected_rows;
                        if ($affected_rows > 0) {
                            $stmt_update2 = $c->prepare($query_update2);
                            $stmt_update2->bind_param("ss", $new_name, $old_name);
                            if ($stmt_update2->execute()) {
                                //ok
                                $response = echo_result($old_name, $new_name);
                            } else {
                                $invalid = true;
                            }
                            $stmt_update2->close();
                        } else {
                            $unauthorized = true;
                        }
                    } else {
                        $invalid = true;
                    }
                    $stmt_update->close();
                } else if ($res["c1"] === 1 && $res["c2"] === 1) {
                    $already_taken = true;
                } else {
                    $invalid = true;
                }
            } else {
                $invalid = true;
            }
        } else {
            // Handle the case where the SELECT query failed
            $invalid = true;
        }

        // Unlock the tables after SELECT and UPDATE
        $c->query("UNLOCK TABLES");

        $c->close();
    }

    if ($already_taken) {
        $response = echo_already_chosen();
    }
    if ($invalid) {
        $response = echo_invalid();
    }
    if ($unauthorized) {
        $response = echo_unauthorized();
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
    $response["description"] = "Name doesn't exist";
    return $response;
}

function echo_invalid()
{
    $response["code"] = "401";
    $response["status"] = "Error";
    $response["description"] = "Invalid link";
    return $response;
}

function echo_unauthorized()
{
    $response["code"] = "403";
    $response["status"] = "Error";
    $response["description"] = "Unauthorized to edit link";
    return $response;
}

function echo_already_chosen()
{
    $response["code"] = "402";
    $response["status"] = "Error";
    $response["description"] = "The name chosen is already taken";
    return $response;
}

function echo_result($old_name, $new_name)
{
    $response["code"] = "200";
    $response["status"] = "Successful";
    $data["old_name"] = $old_name;
    $data["new_name"] = $new_name;
    $response["data"] = $data;
    return $response;
}

function getValidatedNewName($name)
{
    // Replace any characters that are not 0-9, a-z, A-Z, or "-" with an empty string
    $validatedName = preg_replace('/[^0-9a-zA-Z\-]/', '', $name);

    // Limit the length to a maximum of 100 characters
    $validatedName = substr($validatedName, 0, 100);

    return $validatedName;
}

?>