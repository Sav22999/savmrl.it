<?php
// Database credentials — used by: mysqli($localhost_db, $username_db, $password_db, $database_savmrl)
$localhost_db = "<YOUR DATABASE LOCALHOST>";
$username_db = "<YOUR DATABASE USERNAME>";
$password_db = "<YOUR DATABASE PASSWORD>";
$database_savmrl = "<YOUR DATABASE NAME>";

// Link tables (non-alpha names here; header.php overrides with alpha tables)
$redirect_table = "redirect_savmrl";
$opened_table = "opened_savmrl";

// SMTP credentials for email (PHPMailer)
// $smtp_username must be a valid email address (e.g. noreply@savmrl.it)
$smtp_host = "smtps.aruba.it";
$smtp_port = 465;
$smtp_username = "<YOUR SMTP EMAIL ADDRESS>";
$smtp_password = "<YOUR SMTP PASSWORD>";
$smtp_from_name = "savmrl.it";
?>
