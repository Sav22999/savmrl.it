<?php
/**
 * One-time script to create the first admin.
 * Run from CLI: php create-admin.php
 * Delete after use.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/credentials.php");
global $localhost_db, $username_db, $password_db, $database_savmrl;

echo "=== Create admin account ===\n";
echo "Username: ";
$username = trim(fgets(STDIN));
echo "Email: ";
$email = trim(fgets(STDIN));
echo "Password: ";
system('stty -echo 2>/dev/null');
$password = trim(fgets(STDIN));
system('stty echo 2>/dev/null');
echo "\n";

if (empty($username) || empty($email) || empty($password)) {
    echo "Error: all fields are required.\n";
    exit(1);
}

if (strlen($password) < 8) {
    echo "Error: password must be at least 8 characters.\n";
    exit(1);
}

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

$c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
if ($c->connect_error) {
    echo "Database error: " . $c->connect_error . "\n";
    exit(1);
}
$c->set_charset("utf8mb4");

$stmt = $c->prepare("INSERT INTO `admins_savmrl` (`username`, `email`, `password_hash`) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hash);

if ($stmt->execute()) {
    echo "Admin created (ID: " . $c->insert_id . ")\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}

$stmt->close();
$c->close();
