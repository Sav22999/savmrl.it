<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /alpha/account/login/');
    exit;
}

auth_start_session();
auth_destroy_session();
header('Location: /alpha/');
exit;
