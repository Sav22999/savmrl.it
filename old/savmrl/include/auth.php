<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/credentials.php");

$current_user = null;
$current_session_id = null;

function auth_start_session() {
    global $current_user, $current_session_id, $localhost_db, $username_db, $password_db, $database_savmrl;

    $session_id = null;
    if (isset($_COOKIE['savmrl_session'])) {
        $session_id = $_COOKIE['savmrl_session'];
    }
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

    $stmt = $c->prepare("SELECT s.*, u.id AS uid, u.username, u.email, u.email_verified, u.pro_features, u.two_fa_enabled, u.blocked, u.preferred_language, u.created_at AS user_created_at FROM `sessions_savmrl` s JOIN `users_savmrl` u ON s.user_id = u.id WHERE s.id = ? AND s.expires_at > NOW()");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ((int)$row['blocked'] === 1) {
            $stmt->close();
            $del = $c->prepare("DELETE FROM `sessions_savmrl` WHERE `id` = ?");
            $del->bind_param("s", $session_id);
            $del->execute();
            $del->close();
            $c->close();
            setcookie('savmrl_session', '', ['expires' => time() - 3600, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
            return false;
        }

        $current_user = [
            'id' => $row['uid'],
            'username' => $row['username'],
            'email' => $row['email'],
            'email_verified' => (int)$row['email_verified'],
            'pro_features' => (int)$row['pro_features'],
            'two_fa_enabled' => (int)$row['two_fa_enabled'],
            'blocked' => (int)$row['blocked'],
            'preferred_language' => $row['preferred_language'],
            'created_at' => $row['user_created_at'],
            'csrf_token' => $row['csrf_token'],
        ];
        $current_session_id = $session_id;

        $update = $c->prepare("UPDATE `sessions_savmrl` SET `last_activity` = NOW() WHERE `id` = ?");
        $update->bind_param("s", $session_id);
        $update->execute();
        $update->close();

        if (random_int(1, 100) === 1) {
            auth_cleanup($c);
        }

        $stmt->close();
        $c->close();
        return true;
    }

    $stmt->close();
    $c->close();
    return false;
}

function auth_create_session($user_id) {
    global $current_session_id, $localhost_db, $username_db, $password_db, $database_savmrl;

    $session_id = bin2hex(random_bytes(32));
    $csrf_token = bin2hex(random_bytes(32));
    $ip = getIpAddress();
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 512) : null;
    $expires = date('Y-m-d H:i:s', time() + 30 * 86400);

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return false;
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("INSERT INTO `sessions_savmrl` (`id`, `user_id`, `csrf_token`, `ip_address`, `user_agent`, `expires_at`) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $session_id, $user_id, $csrf_token, $ip, $ua, $expires);

    if (!$stmt->execute()) {
        $stmt->close();
        $c->close();
        return false;
    }
    $stmt->close();
    $c->close();

    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('savmrl_session', $session_id, [
        'expires' => time() + 30 * 86400,
        'path' => '/',
        'secure' => $is_https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    $current_session_id = $session_id;
    return $session_id;
}

function auth_destroy_session() {
    global $current_user, $current_session_id, $localhost_db, $username_db, $password_db, $database_savmrl;

    if (!$current_session_id) return;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if (!$c->connect_error) {
        $c->set_charset("utf8mb4");
        $stmt = $c->prepare("DELETE FROM `sessions_savmrl` WHERE `id` = ?");
        $stmt->bind_param("s", $current_session_id);
        $stmt->execute();
        $stmt->close();
        $c->close();
    }

    setcookie('savmrl_session', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    $current_user = null;
    $current_session_id = null;
}

function auth_destroy_all_sessions($user_id) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return;
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("DELETE FROM `sessions_savmrl` WHERE `user_id` = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    $c->close();
}

function auth_register($email, $password, $username) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $email = strtolower(trim($email));
    $username = trim($username);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'Invalid email format'];
    }
    if (strlen($password) < 8) {
        return ['error' => 'Password must be at least 8 characters'];
    }
    if (empty($username) || strlen($username) < 2 || strlen($username) > 100) {
        return ['error' => 'Username must be between 2 and 100 characters'];
    }
    if (!preg_match('/^[a-zA-Z0-9_.\-]+$/', $username)) {
        return ['error' => 'Username can only contain letters, numbers, underscores, dots and hyphens'];
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $check = $c->prepare("SELECT `id` FROM `users_savmrl` WHERE `email` = ? OR `username` = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $check->close();
        $c->close();
        return ['error' => 'Email or username already taken'];
    }
    $check->close();

    $stmt = $c->prepare("INSERT INTO `users_savmrl` (`username`, `email`, `password_hash`) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hash);
    if (!$stmt->execute()) {
        $stmt->close();
        $c->close();
        return ['error' => 'Registration failed'];
    }
    $user_id = $stmt->insert_id;
    $stmt->close();
    $c->close();

    return ['user_id' => $user_id, 'email' => $email, 'username' => $username];
}

function auth_login($email, $password) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $email = strtolower(trim($email));

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("SELECT `id`, `password_hash`, `two_fa_enabled`, `email_verified`, `blocked` FROM `users_savmrl` WHERE `email` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $stmt->close();
        $c->close();
        return ['error' => 'Invalid email or password'];
    }

    $user = $result->fetch_assoc();
    $stmt->close();
    $c->close();

    if (!password_verify($password, $user['password_hash'])) {
        return ['error' => 'Invalid email or password'];
    }

    if ((int)$user['blocked'] === 1) {
        return ['error' => 'Your account has been blocked by an administrator'];
    }

    if ((int)$user['email_verified'] !== 1) {
        return ['email_not_verified' => true, 'user_id' => $user['id']];
    }

    if ((int)$user['two_fa_enabled'] === 1) {
        return ['requires_2fa' => true, 'user_id' => $user['id']];
    }

    $session_id = auth_create_session($user['id']);
    if (!$session_id) {
        return ['error' => 'Session creation failed'];
    }

    return ['session_id' => $session_id, 'user_id' => $user['id']];
}

function auth_get_current_user() {
    global $current_user;
    return $current_user;
}

function auth_require_login() {
    $user = auth_get_current_user();
    if (!$user) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['code' => '401', 'status' => 'Error', 'description' => 'Authentication required']);
            exit;
        }
        header('Location: /account/login/');
        exit;
    }
    return $user;
}

function generate_csrf_token() {
    global $current_user;
    if ($current_user && isset($current_user['csrf_token'])) {
        return $current_user['csrf_token'];
    }
    return bin2hex(random_bytes(32));
}

function verify_csrf_token($token) {
    global $current_user;
    if (!$current_user || !isset($current_user['csrf_token'])) {
        return false;
    }
    return hash_equals($current_user['csrf_token'], $token);
}

function generate_otp($user_id, $purpose = 'login_2fa') {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return false;
    $c->set_charset("utf8mb4");

    $del = $c->prepare("DELETE FROM `otp_codes_savmrl` WHERE `user_id` = ? AND `purpose` = ? AND `used` = 0");
    $del->bind_param("is", $user_id, $purpose);
    $del->execute();
    $del->close();

    $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $code_hash = password_hash($code, PASSWORD_DEFAULT);
    $temp_token = bin2hex(random_bytes(32));
    $ttl = ($purpose === 'email_verification') ? 86400 : 1800;
    $expires = date('Y-m-d H:i:s', time() + $ttl);

    $stmt = $c->prepare("INSERT INTO `otp_codes_savmrl` (`user_id`, `code`, `purpose`, `temp_token`, `expires_at`) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $code_hash, $purpose, $temp_token, $expires);
    if (!$stmt->execute()) {
        $stmt->close();
        $c->close();
        return false;
    }
    $stmt->close();
    $c->close();

    return ['code' => $code, 'temp_token' => $temp_token];
}

function verify_otp($temp_token, $code, $purpose = 'login_2fa') {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("SELECT * FROM `otp_codes_savmrl` WHERE `temp_token` = ? AND `purpose` = ? AND `used` = 0 AND `expires_at` > NOW() ORDER BY `created_at` DESC LIMIT 1");
    $stmt->bind_param("ss", $temp_token, $purpose);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        $stmt->close();
        $c->close();
        return ['error' => 'Code expired or not found'];
    }

    $otp = $result->fetch_assoc();
    $stmt->close();

    if ((int)$otp['attempts'] >= 5) {
        $mark = $c->prepare("UPDATE `otp_codes_savmrl` SET `used` = 1 WHERE `id` = ?");
        $mark->bind_param("i", $otp['id']);
        $mark->execute();
        $mark->close();
        $c->close();
        return ['error' => 'Too many attempts'];
    }

    $code_match = false;
    if (strpos($otp['code'], '$') === 0) {
        $code_match = password_verify($code, $otp['code']);
    } else {
        $code_match = hash_equals($otp['code'], $code);
    }
    if (!$code_match) {
        $inc = $c->prepare("UPDATE `otp_codes_savmrl` SET `attempts` = `attempts` + 1 WHERE `id` = ?");
        $inc->bind_param("i", $otp['id']);
        $inc->execute();
        $inc->close();
        $c->close();
        return ['error' => 'Wrong code'];
    }

    $mark = $c->prepare("UPDATE `otp_codes_savmrl` SET `used` = 1 WHERE `id` = ?");
    $mark->bind_param("i", $otp['id']);
    $mark->execute();
    $mark->close();
    $c->close();

    return ['success' => true, 'user_id' => $otp['user_id']];
}

function auth_change_password($user_id, $current_password, $new_password) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    if (strlen($new_password) < 8) {
        return ['error' => 'Password must be at least 8 characters'];
    }

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("SELECT `password_hash` FROM `users_savmrl` WHERE `id` = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows !== 1) {
        $stmt->close();
        $c->close();
        return ['error' => 'User not found'];
    }
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!password_verify($current_password, $user['password_hash'])) {
        $c->close();
        return ['error' => 'Current password is incorrect'];
    }

    $new_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
    $upd = $c->prepare("UPDATE `users_savmrl` SET `password_hash` = ? WHERE `id` = ?");
    $upd->bind_param("si", $new_hash, $user_id);
    $upd->execute();
    $upd->close();
    $c->close();

    auth_destroy_all_sessions($user_id);

    return ['success' => true];
}

function auth_request_password_change($user_id, $current_password, $new_password) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    if (strlen($new_password) < 8) {
        return ['error' => 'Password must be at least 8 characters'];
    }

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("SELECT `password_hash` FROM `users_savmrl` WHERE `id` = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows !== 1) {
        $stmt->close();
        $c->close();
        return ['error' => 'User not found'];
    }
    $user = $result->fetch_assoc();
    $stmt->close();
    $c->close();

    if (!password_verify($current_password, $user['password_hash'])) {
        return ['error' => 'Current password is incorrect'];
    }

    $otp = generate_otp($user_id, 'password_change');
    if (!$otp) {
        return ['error' => 'Failed to generate verification code'];
    }

    return ['temp_token' => $otp['temp_token'], 'code' => $otp['code']];
}

function auth_confirm_password_change($user_id, $new_password) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $new_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $upd = $c->prepare("UPDATE `users_savmrl` SET `password_hash` = ? WHERE `id` = ?");
    $upd->bind_param("si", $new_hash, $user_id);
    $upd->execute();
    $upd->close();
    $c->close();

    auth_destroy_all_sessions($user_id);
    return ['success' => true];
}

function auth_delete_account($user_id, $password) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("SELECT `password_hash` FROM `users_savmrl` WHERE `id` = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows !== 1) {
        $stmt->close();
        $c->close();
        return ['error' => 'User not found'];
    }
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!password_verify($password, $user['password_hash'])) {
        $c->close();
        return ['error' => 'Password is incorrect'];
    }

    $c->close();
    return ['verified' => true];
}

function auth_confirm_delete_account($user_id) {
    global $localhost_db, $username_db, $password_db, $database_savmrl, $redirect_table;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $del_sessions = $c->prepare("DELETE FROM `sessions_savmrl` WHERE `user_id` = ?");
    $del_sessions->bind_param("i", $user_id);
    $del_sessions->execute();
    $del_sessions->close();

    $del_otp = $c->prepare("DELETE FROM `otp_codes_savmrl` WHERE `user_id` = ?");
    $del_otp->bind_param("i", $user_id);
    $del_otp->execute();
    $del_otp->close();

    $nullify = $c->prepare("UPDATE `redirect_savmrl` SET `user_id` = NULL WHERE `user_id` = ?");
    $nullify->bind_param("i", $user_id);
    $nullify->execute();
    $nullify->close();

    $del_user = $c->prepare("DELETE FROM `users_savmrl` WHERE `id` = ?");
    $del_user->bind_param("i", $user_id);
    $del_user->execute();
    $del_user->close();

    $c->close();
    return ['success' => true];
}

function auth_resend_verification($user_id) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return false;
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("SELECT `email`, `username`, `email_verified` FROM `users_savmrl` WHERE `id` = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows !== 1) {
        $stmt->close();
        $c->close();
        return false;
    }
    $user = $result->fetch_assoc();
    $stmt->close();
    $c->close();

    if ((int)$user['email_verified'] === 1) {
        return false;
    }

    $otp = generate_otp($user_id, 'email_verification');
    if (!$otp) return false;

    return ['code' => $otp['code'], 'temp_token' => $otp['temp_token'], 'email' => $user['email'], 'username' => $user['username']];
}

function auth_forgot_password_request($email) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $email = strtolower(trim($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'Invalid email format'];
    }

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("SELECT `id`, `username`, `email`, `blocked` FROM `users_savmrl` WHERE `email` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows !== 1) {
        $stmt->close();
        $c->close();
        return ['success' => true];
    }
    $user = $result->fetch_assoc();
    $stmt->close();
    $c->close();

    if ((int)$user['blocked'] === 1) {
        return ['success' => true];
    }

    $otp = generate_otp($user['id'], 'password_reset');
    if (!$otp) {
        return ['error' => 'Failed to generate verification code'];
    }

    return ['success' => true, 'user_id' => $user['id'], 'code' => $otp['code'], 'temp_token' => $otp['temp_token'], 'email' => $user['email'], 'username' => $user['username']];
}

function auth_reset_password($temp_token, $code, $new_password) {
    if (strlen($new_password) < 8) {
        return ['error' => 'Password must be at least 8 characters'];
    }

    $result = verify_otp($temp_token, $code, 'password_reset');
    if (isset($result['error'])) {
        return $result;
    }

    global $localhost_db, $username_db, $password_db, $database_savmrl;
    $new_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return ['error' => 'Database error'];
    $c->set_charset("utf8mb4");

    $upd = $c->prepare("UPDATE `users_savmrl` SET `password_hash` = ? WHERE `id` = ?");
    $upd->bind_param("si", $new_hash, $result['user_id']);
    $upd->execute();
    $upd->close();
    $c->close();

    auth_destroy_all_sessions($result['user_id']);
    return ['success' => true];
}

function auth_cleanup($c) {
    $c->query("DELETE FROM `sessions_savmrl` WHERE `expires_at` < NOW()");
    $c->query("DELETE FROM `otp_codes_savmrl` WHERE `expires_at` < NOW()");
}

function auth_get_user_sessions($user_id) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return [];
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("SELECT `id`, `ip_address`, `user_agent`, `created_at`, `last_activity` FROM `sessions_savmrl` WHERE `user_id` = ? AND `expires_at` > NOW() ORDER BY `last_activity` DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sessions = [];
    while ($row = $result->fetch_assoc()) {
        $sessions[] = $row;
    }
    $stmt->close();
    $c->close();
    return $sessions;
}

function auth_revoke_session($session_id, $user_id) {
    global $localhost_db, $username_db, $password_db, $database_savmrl;

    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if ($c->connect_error) return false;
    $c->set_charset("utf8mb4");

    $stmt = $c->prepare("DELETE FROM `sessions_savmrl` WHERE `id` = ? AND `user_id` = ?");
    $stmt->bind_param("si", $session_id, $user_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    $c->close();
    return $affected > 0;
}
