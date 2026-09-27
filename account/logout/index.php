<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/auth.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /account/login/');
    exit;
}

auth_start_session();
auth_destroy_session();
header('Location: /');
exit;
