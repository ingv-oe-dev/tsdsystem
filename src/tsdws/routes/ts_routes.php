<?php

if (getenv("ENV") == 'development') {
	error_reporting(-1);
	ini_set('display_errors', 'On');
}

if (strripos($_SERVER["REQUEST_URI"], "values")) {
	
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."TimeseriesValuesController.php");
	
	$tsc = new TimeseriesValuesController();
}
else if (strripos($_SERVER["REQUEST_URI"], "uploadFromFile")) {
	
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."TimeseriesFromCSVController.php");
	
	$tsc = new TimeseriesFromCSVController();
}
else {
	
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."TimeseriesController.php");
	
	$tsc = new TimeseriesController();
}
	