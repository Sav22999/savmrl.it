<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/credentials.php");
global $localhost_db, $password_db, $database_savmrl, $username_db;

global $title;
if (isset($title)) {
    echo "<title>" . $title . "</title>";
} else {
    $title = "?";
    echo "<title>savmrl.it</title>";
}

$title_header = "<a href='/' id='title-page-with-icon'>savmrl.it</a>"; //TODO : set manually
$seconds = 0; //TODO : set manually

$url_opengraph = "https://www.savmrl.it/savmrl/images/banner.png";

function getUrlFromName($name)
{
    $name_to_use = $name;

    $found = false;
    $invalid = false;
    if (strlen($name_to_use) !== 5) $invalid = true;

    if (!$invalid) {
        global $localhost_db, $username_db, $password_db, $database_savmrl;
        if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
            $c->set_charset("utf8");

            // Snippet: SELECT * FROM `redirect_savmrl` WHERE `name`='$name_to_use'
            // Using prepared stataments -> the safest techniques to manage queries of a database
            $query = "SELECT * FROM `redirect_savmrl` WHERE `name`=?";
            $stmt = $c->prepare($query);
            $stmt->bind_param("s", $name_to_use);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $found = true;

                while ($row = $result->fetch_array()) {
                    $url = $row['redirect_link'];

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
    $name_to_use = $name;

    $found = false;
    $invalid = true;
    if (strlen($name_to_use) === 5) $invalid = false;

    if (!$invalid) {
        global $localhost_db, $username_db, $password_db, $database_savmrl;

        //Using prepared stataments -> it's the safest way for MySQL queries
        if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
            $c->set_charset("utf8");

            // Snippet 1: SELECT * FROM `redirect_savmrl` WHERE `name`='$name_to_use'
            $query_exists = "SELECT * FROM `redirect_savmrl` WHERE `name`=?";
            $stmt_exists = $c->prepare($query_exists);
            $stmt_exists->bind_param("s", $name_to_use);
            $stmt_exists->execute();
            $result_exists = $stmt_exists->get_result();
            $stmt_exists->close();

            if ($result_exists->num_rows > 0) {
                // Snippet 2: SELECT COUNT(*) AS `count` FROM `opened_savmrl` WHERE `name`='$name_to_use'
                $query_count = "SELECT COUNT(*) AS `count` FROM `opened_savmrl` WHERE `name`=?";
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

function getIpAddress()
{
    $ip_address = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $_SERVER['REMOTE_ADDR']);
    $ip_address = $ip_address ?: "Unknown";
    return $ip_address;
}

function redirectTo($name, $url, $seconds)
{
    if ($name !== false) {
        $name_to_use = $name;

        $ip_address = getIpAddress();

        global $localhost_db, $username_db, $password_db, $database_savmrl;
        if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
            $c->set_charset("utf8");

            // Snippet 1: SELECT * FROM `redirect_savmrl` WHERE `name`='$name_to_use'
            $query_select = "SELECT * FROM `redirect_savmrl` WHERE `name`=?";
            $stmt_select = $c->prepare($query_select);
            $stmt_select->bind_param("s", $name_to_use);
            $stmt_select->execute();
            $result_select = $stmt_select->get_result();
            $stmt_select->close();

            if ($result_select->num_rows > 0) {
                // Snippet 2: INSERT INTO `opened_savmrl` (`id`, `name`, `ip_address`, `visited_timestamp`) VALUES (NULL, '$name_to_use', '$ip_address', CURRENT_TIMESTAMP);
                $query_insert = "INSERT INTO `opened_savmrl` (`id`, `name`, `ip_address`, `visited_timestamp`) VALUES (NULL, ?, ?, CURRENT_TIMESTAMP)";
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

function insertNewRedirect($link)
{
    $value_to_return = "error";
    $attempts = 20; //TODO : set manually the max attempts to do before to get error in case no strings is found
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    if ($c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl)) {
        $c->autocommit(false);
        $c->set_charset("utf8");

        $foundUniqueValue = false;
        $ip_address = getIpAddress();

        $link_to_use = getGoodString($link);

        while (!$foundUniqueValue && $attempts > 0) {
            $newValue = generateRandomString(5);

            // Snippet: SELECT * FROM `redirect_savmrl` WHERE `name` = '$newValue' FOR UPDATE
            $query_select = "SELECT * FROM `redirect_savmrl` WHERE `name` = ? FOR UPDATE";
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

                // Snippet: INSERT INTO `redirect_savmrl` (`id`, `name`, `redirect_link`, `access_code`, `limit_times`, `expiry_date`, `inserted_timestamp`, `inserted_from_ip`) VALUES (NULL, '$newValue', '$link_to_use', NULL, NULL, NULL, CURRENT_TIMESTAMP, '$ip_address')
                $query_insert = "INSERT INTO `redirect_savmrl` (`id`, `name`, `redirect_link`, `access_code`, `limit_times`, `expiry_date`, `inserted_timestamp`, `inserted_from_ip`) VALUES (NULL, ?, ?, NULL, NULL, NULL, CURRENT_TIMESTAMP, ?)";
                $stmt_insert = $c->prepare($query_insert);
                $stmt_insert->bind_param("sss", $newValue, $link_to_use, $ip_address);
                $stmt_insert->execute();
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
    }

    return $value_to_return;
}

?>

<link rel="stylesheet" href="https://www.saveriomorelli.com/style/site.css"/>
<link rel="stylesheet" href="/savmrl/css/style.css"/>
<link rel="icon" href="/savmrl/images/icon.svg"/>
<meta http-equiv="content-type" content="text/html; charset=UTF-16">
<meta name="viewport" content="width=device-width, initial-scale=0.8"/>

<meta property="og:locale" content="en"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="savmrl.it"/>
<meta property="og:description"
      content="The best anonymous and free link shortener"/>
<meta property="og:url" content="https://www.savmrl.it"/>
<meta property="og:site_name" content="savmrl.it"/>
<meta property="og:image" content="<?php echo $url_opengraph; ?>"/>
<meta property="og:image:secure_url" content="<?php echo $url_opengraph; ?>"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:description"
      content="The best anonymous and free link shortener"/>
<meta name="twitter:title" content="savmrl.it"/>
<meta name="twitter:site" content="@Sav22999"/>
<meta name="twitter:image" content="<?php echo $url_opengraph; ?>"/>
<meta name="twitter:creator" content="@Sav22999"/>