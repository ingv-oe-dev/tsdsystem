<?php
if (getenv("ENV") == 'development') {
    error_reporting(-1);
    ini_set('display_errors', 'On');
}

if (strripos($_SERVER["REQUEST_URI"], "mapping")) {
	
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."RolesMappingController.php");
	
	$controller = new RolesMappingController();
}
else {
	
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."RolesController.php");
	
	$controller = new RolesController();
}