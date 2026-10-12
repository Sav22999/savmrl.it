<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/credentials.php");

$current_admin = null;
$current_admin_session_id = null;

function admin_auth_start_session() {
    global $current_admin, $current_admin_session_id, $localhost_db, $username_db, $password_db, $database_savmrl;

    $session_id = isset($_COOKIE['savmrl_admin_session']) ? $_COOKIE['savmrl_admin_session'] : null;
    $auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
    if (!$session_id && preg_match('/^Bearer\s+(\S+)$/i', $auth_header, $m)) {
        $session_id = $m[1];
    }

    if (!$session_id || strlen($session_id) !== 64 || !ctype_xdigit($session_id)) {
        return false;
    }

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return false;
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("SELECT s.*, a.id AS aid, a.username, a.email FROM `admin_sessions_savmrl` s JOIN `admins_savmrl` a ON s.admin_id = a.id WHERE s.id = ? AND s.expires_at > NOW()");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $current_admin = [
            'id' => $row['aid'],
            'username' => $row['username'],
            'email' => $row['email'],
            'csrf_token' => $row['csrf_token'],
        ];
        $current_admin_session_id = $session_id;

        $upd = $c->prepare("UPDATE `admin_sessions_savmrl` SET `last_activity` = NOW() WHERE `id` = ?");
        $upd->bind_param("s", $session_id);
        $upd->execute();
        $upd->close();

        if (random_int(1, 100) === 1) {
            $c->query("DELETE FROM `admin_sessions_savmrl` WHERE `expires_at` < NOW()");
        }

        $stmt->close();
        $c->close();
        return true;
    }

    $stmt->close();
    $c->close();
    return false;
}

function admin_auth_create_session($admin_id) {
    global $current_admin_session_id, $localhost_db, $username_db, $password_db, $database_savmrl;

    $session_id = bin2hex(random_bytes(32));
    $csrf_token = bin2hex(random_bytes(32));
    $ip = getIpAddress();
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 512) : null;
    $expires = date('Y-m-d H:i:s', time() + 7 * 86400);

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return false;
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("INSERT INTO `admin_sessions_savmrl` (`id`, `admin_id`, `csrf_token`, `ip_address`, `user_agent`, `expires_at`) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $session_id, $admin_id, $csrf_token, $ip, $ua, $expires);

    if (!$stmt->execute()) {
        $stmt->close();
        $c->close();
        return false;
    }
    $stmt->close();

    $upd = $c->prepare("UPDATE `admins_savmrl` SET `last_login` = NOW() WHERE `id` = ?");
    $upd->bind_param("i", $admin_id);
    $upd->execute();
    $upd->close();

    $c->close();

    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('savmrl_admin_session', $session_id, [
        'expires' => time() + 7 * 86400,
        'path' => '/',
        'secure' => $is_https,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    $current_admin_session_id = $session_id;
    return $session_id;
}

function admin_auth_destroy_session() {
    global $current_admin, $current_admin_session_id, $localhost_db, $username_db, $password_db, $database_savmrl;

    if (!$current_admin_session_id) return;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if (!$c->connect_error) {
        $c->set_charset("utf8mb4");
        $stmt = $c->prepare("DELETE FROM `admin_sessions_savmrl` WHERE `id` = ?");
        $stmt->bind_param("s", $current_admin_session_id);
        $stmt->execute();
        $stmt->close();
        $c->close();
    }

    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('savmrl_admin_session', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => $is_https,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    $current_admin = null;
    $current_admin_session_id = null;
}

function admin_auth_login($username, $password) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("SELECT `id`, `password_hash` FROM `admins_savmrl` WHERE `username` = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $stmt->close();
        $c->close();
        return ['error' => 'Invalid credentials'];
    }

    $admin = $result->fetch_assoc();
    $stmt->close();
    $c->close();

    if (!password_verify($password, $admin['password_hash'])) {
        return ['error' => 'Invalid credentials'];
    }

    if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
        $rehash = password_hash($password, PASSWORD_DEFAULT);
        $rc = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
        if (!$rc->connect_error) {
            $rc->set_charset("utf8mb4");
            $ru = $rc->prepare("UPDATE `admins_savmrl` SET `password_hash` = ? WHERE `id` = ?");
            $ru->bind_param("si", $rehash, $admin['id']);
            $ru->execute();
            $ru->close();
            $rc->close();
        }
    }

    $session_id = admin_auth_create_session($admin['id']);
    if (!$session_id) return ['error' => 'Session creation failed'];

    return ['session_id' => $session_id, 'admin_id' => $admin['id']];
}

function admin_get_current() {
    global $current_admin;
    return $current_admin;
}

function admin_require_login() {
    $admin = admin_get_current();
    if (!$admin) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['code' => '401', 'status' => 'Error', 'description' => 'Admin authentication required']);
            exit;
        }
        $prefix = (strpos($_SERVER['REQUEST_URI'], '/alpha/') === 0) ? '/alpha' : '';
        header('Location: ' . $prefix . '/admin/login/');
        exit;
    }
    return $admin;
}
