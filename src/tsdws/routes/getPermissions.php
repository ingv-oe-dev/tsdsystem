<?php
if (getenv("ENV") == 'development') {
    error_reporting(-1);
    ini_set('display_errors', 'On');
}

require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."PermissionsController.php");

if (strripos($_SERVER["REQUEST_URI"], "member")) {
	$controller = new PermissionsController($role_type="member");
}
else {
	$controller = new PermissionsController($role_type="role");
}