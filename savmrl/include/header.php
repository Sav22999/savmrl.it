<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/credentials.php");
global $localhost_db, $password_db, $database_savmrl, $username_db, $title;

$title_header = "<a href='/' id='title-page-with-icon'>savmrl.it</a>"; //TODO : set manually //<span style='color: teal;font-family: serif'>αlpha</span>
$seconds = 0; //TODO : set manually

function getUrlFromName($name, $accessCode = false, $alreadyEncrypted = false)
{
    global $redirect_table, $opened_table;
    $name_to_use = $name;
    $access_to_use = false;
    $is_protected = false;
    if ($accessCode !== "" && $accessCode !== null && $accessCode !== false) {
        if (!$alreadyEncrypted) $access_to_use = hash('sha512', $accessCode);
        else $access_to_use = $accessCode;
        $is_protected = true;
    }

    $found = false;
    $invalid = false;

    if (!$invalid) {
        global $localhost_db, $username_db, $password_db, $database_savmrl;
        if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
            $c->set_charset("utf8mb4");

            // Snippet: SELECT * FROM `redirect_savmrl` WHERE `name`='$name_to_use'
            // Using prepared statements -> the safest techniques to manage queries of a database
            $query = "SELECT t1.*, t2.rows FROM `$redirect_table` AS t1 LEFT JOIN (SELECT name, COUNT(*) AS rows FROM `$opened_table` GROUP BY name) AS t2 ON t1.name = t2.name WHERE t1.name = ? AND (t1.limit_times IS NULL OR t2.rows IS NULL OR t2.rows < t1.limit_times) AND (t1.expiry_date IS NULL OR CURDATE() <= t1.expiry_date)";
            $stmt = $c->prepare($query);
            $stmt->bind_param("s", $name_to_use); //for password
            if ($stmt->execute()) {
                //successful
            } else {
                $stmt->close();
                $c->close();
                return "invalid";
            }
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $found = true;

                while ($row = $result->fetch_array()) {
                    $url = $row['redirect_link'];

                    if ($row["reported"] === 1) {
                        //the link has been marked as "reported"
                        $c->close();
                        return "reported";
                    } else {
                        //the link is valid
                    }

                    if ($row["access_code"] !== null) {
                        if ($is_protected) {
                            if ($row["access_code"] === $access_to_use) {
                                //access code correct -- do nothing
                            } else {
                                //access code incorrect
                                $c->close();
                                return "access_code_wrong";
                            }
                        } else {
                            //access code required and not inserted
                            $c->close();
                            return "access_code_required";
                        }
                    } else {
                        //no access code required
                    }

                    if ($is_protected) {
                        $url = decryptTextWithPassword($url, $accessCode);
                    }
                    if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
                        $c->close();
                        return $url;
                    } else {
                        $invalid = true;
                    }
                }
            } else {
                //not found
            }

            $c->close();
        }
    }

    if ($invalid) {
        return "invalid";
    }
    if (!$found) {
        return "not_exists";
    }

    return "?";
}

function getStatistics($name)
{
    global $redirect_table, $opened_table;
    $name_to_use = $name;

    $found = false;
    $invalid = false;

    if (!$invalid) {
        global $localhost_db, $username_db, $password_db, $database_savmrl;

        //Using prepared stataments -> it's the safest way for MySQL queries
        if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
            $c->set_charset("utf8mb4");

            // Snippet 1: SELECT * FROM `redirect_savmrl` WHERE `name`='$name_to_use'
            $query_exists = "SELECT * FROM `$redirect_table` WHERE `name` = ?";
            $stmt_exists = $c->prepare($query_exists);
            $stmt_exists->bind_param("s", $name_to_use);
            $stmt_exists->execute();
            $result_exists = $stmt_exists->get_result();
            $stmt_exists->close();

            if ($result_exists->num_rows > 0) {
                // Snippet 2: SELECT COUNT(*) AS `count` FROM `opened_savmrl` WHERE `name`='$name_to_use'
                $query_count = "SELECT COUNT(*) AS `count` FROM `$opened_table` WHERE `name`=?";
                $stmt_count = $c->prepare($query_count);
                $stmt_count->bind_param("s", $name_to_use);
                $stmt_count->execute();
                $result_count = $stmt_count->get_result();
                $stmt_count->close();

                if ($result_count->num_rows > 0) {
                    $found = true;

                    while ($row = $result_count->fetch_array()) {
                        $c->close();
                        return $row['count'];
                    }
                }
            } else {
                //not found
            }
            $c->close();
        }
    }

    if ($invalid) {
        return "invalid";
    }
    if (!$found) {
        return "not_exists";
    }

    return "?";
}

function getGoodString($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function isValidUrl($string)
{
    //return true -> it doesn't contain "https://www.savmrl.it, false -> otherwise
    return (strpos($string, "https://www.savmrl.it") === false && strpos($string, "https://savmrl.it") === false);
}

function getIpAddress()
{
    $ip_address = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $_SERVER['REMOTE_ADDR']);
    $ip_address = $ip_address ?: "Unknown";
    return $ip_address;
}

function redirectTo($name, $url, $seconds)
{
    global $redirect_table, $opened_table;
    if ($name !== false) {
        $name_to_use = $name;

        $ip_address = getIpAddress();

        global $localhost_db, $username_db, $password_db, $database_savmrl;
        if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
            $c->set_charset("utf8mb4");

            // Snippet 1: SELECT * FROM `redirect_savmrl` WHERE `name`='$name_to_use'
            $query_select = "SELECT t1.*, t2.rows
                 FROM `$redirect_table` AS t1 
                 LEFT JOIN (
                    SELECT name, COUNT(*) AS rows
                    FROM `$opened_table`
                    GROUP BY name
                 ) AS t2 ON t1.name = t2.name
                 WHERE t1.name = ? 
                 AND (t1.limit_times IS NULL OR t2.rows IS NULL OR t2.rows < t1.limit_times)
                 AND (t1.expiry_date IS NULL OR CURDATE() <= t1.expiry_date)";
            $stmt_select = $c->prepare($query_select);
            $stmt_select->bind_param("s", $name_to_use);
            $stmt_select->execute();
            $result_select = $stmt_select->get_result();
            $stmt_select->close();

            if ($result_select->num_rows > 0) {
                // Snippet 2: INSERT INTO `opened_savmrl` (`id`, `name`, `ip_address`, `visited_timestamp`) VALUES (NULL, '$name_to_use', '$ip_address', CURRENT_TIMESTAMP);
                $query_insert = "INSERT INTO `$opened_table` (`id`, `name`, `ip_address`, `visited_timestamp`) VALUES (NULL, ?, ?, CURRENT_TIMESTAMP)";
                $stmt_insert = $c->prepare($query_insert);
                $stmt_insert->bind_param("ss", $name_to_use, $ip_address);

                if ($stmt_insert->execute()) {
                    // Inserted correctly
                }

                $stmt_insert->close();
            }

            $c->close();
        }
    }
    $url_to_use = getGoodString($url);
    header("Refresh:$seconds;url=$url_to_use");
}

function generateRandomString($length = 5)
{
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
}

function deriveKeyFromPassword($password, $salt, $keyLength = 32, $iterations = 10000, $algorithm = 'sha256')
{
    return hash_pbkdf2($algorithm, $password, $salt, $iterations, $keyLength, true);
}

function encryptTextWithPassword($text, $password)
{
    $salt = openssl_random_pseudo_bytes(16); // Generate a random salt
    $key = deriveKeyFromPassword($password, $salt);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encryptedText = openssl_encrypt($text, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($salt . $iv . $encryptedText);
}

function decryptTextWithPassword($encryptedText, $password)
{
    $decoded = base64_decode($encryptedText);
    $salt = substr($decoded, 0, 16);
    $iv = substr($decoded, 16, openssl_cipher_iv_length('aes-256-cbc'));
    $encryptedText = substr($decoded, 16 + openssl_cipher_iv_length('aes-256-cbc'));
    $key = deriveKeyFromPassword($password, $salt);
    return openssl_decrypt($encryptedText, 'aes-256-cbc', $key, 0, $iv);
}

function insertNewRedirect($link, $openings = null, $date = null, $access_code = null)
{
    global $redirect_table;
    $value_to_return = "error";
    $attempts = 20; //TODO : set manually the max attempts to do before to get error in case no strings is found
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
        $c->autocommit(false);
        $c->set_charset("utf8mb4");

        $foundUniqueValue = false;
        $ip_address = getIpAddress();

        $access_code_to_use = null;
        $link_to_use = getGoodString($link);
        if ($access_code !== null && $access_code !== "") {
            $access_code_to_use = hash('sha512', $access_code);
        }
        $openings = isValidNumber($openings);
        $date = isValidDate($date);
        if (isValidUrl($link)) {
            while (!$foundUniqueValue && $attempts > 0) {
                $newValue = generateRandomString(5);

                // Snippet: SELECT * FROM `redirect_savmrl` WHERE `name` = '$newValue' FOR UPDATE
                $query_select = "SELECT * FROM `$redirect_table` WHERE `name` = ? FOR UPDATE";
                $stmt_select = $c->prepare($query_select);
                $stmt_select->bind_param("s", $newValue);
                $stmt_select->execute();
                $result_select = $stmt_select->get_result();
                $stmt_select->close();

                if ($result_select->num_rows === 0) {
                    if (filter_var($link_to_use, FILTER_VALIDATE_URL) !== false) {
                        // nothing, the URL is valid
                    } else {
                        $c->rollback();
                        $c->close();
                        return "invalid_url";
                    }

                    if ($access_code !== null && $access_code !== "") {
                        //encrypt the link
                        $link_to_use = encryptTextWithPassword($link, $access_code);
                    }

                    // Snippet: INSERT INTO `redirect_savmrl` (`id`, `name`, `redirect_link`, `access_code`, `limit_times`, `expiry_date`, `inserted_timestamp`, `inserted_from_ip`) VALUES (NULL, '$newValue', '$link_to_use', NULL, NULL, NULL, CURRENT_TIMESTAMP, '$ip_address')
                    $query_insert = "INSERT INTO `$redirect_table` (`id`, `name`, `redirect_link`, `access_code`, `limit_times`, `expiry_date`, `inserted_timestamp`, `inserted_from_ip`, `reported`) VALUES (NULL, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?, NULL)";
                    $stmt_insert = $c->prepare($query_insert);
                    $stmt_insert->bind_param("sssiss", $newValue, $link_to_use, $access_code_to_use, $openings, $date, $ip_address);

                    if ($stmt_insert->execute()) {
                        //successful
                    } else {
                        $c->rollback();
                        $c->close();
                        return "error";
                    }
                    $stmt_insert->close();

                    $c->commit();
                    $foundUniqueValue = true;
                    $value_to_return = $newValue;
                } else {
                    // The string already exists
                    $c->rollback();
                }

                $attempts--;
            }
            $c->query("UNLOCK TABLES");
            $c->close();
        } else {
            $c->close();
            return "invalid_url";
        }
    }

    return $value_to_return;
}

function customiseLink($old_name, $new_name)
{

}

function isValidDate($date)
{
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    if ($dateTime !== false && array_sum(DateTime::getLastErrors()) === 0) {
        return $dateTime->format('Y-m-d');
    }
    return null;
}

function isValidNumber($number)
{
    if (is_numeric($number) && $number > 0) {
        return $number;
    }
    return null;
}

function getTimestamp()
{
    return date('Y-m-d H:i:s');
}

?>