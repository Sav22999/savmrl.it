<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/include/header.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/include/auth.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /account/login/');
    exit;
}

auth_start_session();
auth_destroy_session();
header('Location: /');
exit;
