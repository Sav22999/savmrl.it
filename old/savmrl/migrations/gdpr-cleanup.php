<?php
/**
 * GDPR IP Cleanup Script
 * Nullifies IP addresses older than 30 days from all relevant tables.
 * Schedule this as a daily cron job: php /path/to/gdpr-cleanup.php
 */
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../include/credentials.php';

$conn = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
if ($conn->connect_error) {
    fwrite(STDERR, "DB connection failed: " . $conn->connect_error . "\n");
    exit(1);
}
$conn->set_charset('utf8mb4');

$cutoff = date('Y-m-d H:i:s', strtotime('-30 days'));
$total = 0;

$tables = [
    ['opened_savmrl', 'ip_address', 'visited_timestamp'],
    ['opened_alpha_savmrl', 'ip_address', 'visited_timestamp'],
    ['redirect_savmrl', 'inserted_from_ip', 'inserted_timestamp'],
    ['redirect_alpha_savmrl', 'inserted_from_ip', 'inserted_timestamp'],
    ['sessions_savmrl', 'ip_address', 'created_at'],
];

foreach ($tables as [$table, $ip_col, $ts_col]) {
    $stmt = $conn->prepare(
        "UPDATE `$table` SET `$ip_col` = NULL WHERE `$ip_col` IS NOT NULL AND `$ts_col` < ?"
    );
    $stmt->bind_param('s', $cutoff);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    $total += $affected;
    echo "$table: $affected IPs cleaned\n";
}

$conn->close();
echo "Done. Total: $total IPs nullified.\n";
