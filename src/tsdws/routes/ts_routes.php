<?php

if (getenv("ENV") == 'development') {
	error_reporting(-1);
	ini_set('display_errors', 'On');
}

// set Access-Control-Allow-Origin if set on environment variables
$ACAO = getenv("Access-Control-Allow-Origin");
if ($ACAO) header("Access-Control-Allow-Origin: " . getenv("Access-Control-Allow-Origin"));

if (strripos($_SERVER["REQUEST_URI"], "values")) {
	
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."TimeseriesValuesController.php");
	
	$tsc = new TimeseriesValuesController();
}
else {
	
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."TimeseriesController.php");
	
	$tsc = new TimeseriesController();
}
	