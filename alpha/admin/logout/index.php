<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/admin-auth.php");
admin_auth_start_session();
admin_auth_destroy_session();
header('Location: /alpha/admin/login/');
exit;
