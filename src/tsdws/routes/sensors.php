<?php

if (getenv("ENV") == 'development') {
	error_reporting(-1);
	ini_set('display_errors', 'On');
}

require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."SensorsController.php");
$controller = new SensorsController();