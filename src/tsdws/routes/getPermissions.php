<?php
if (getenv("ENV") == 'development') {
    error_reporting(-1);
    ini_set('display_errors', 'On');
}

$role_type = isset($_GET["role_type"]) ? $_GET["role_type"] : null;

require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."PermissionsController.php");
$controller = new PermissionsController($role_type);
