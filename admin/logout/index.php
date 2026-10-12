<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/include/header.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/include/admin-auth.php");
admin_auth_start_session();
admin_auth_destroy_session();
header('Location: /admin/login/');
exit;
