<?php

if (getenv("ENV") == 'development') {
	error_reporting(-1);
	ini_set('display_errors', 'On');
}

// set Access-Control-Allow-Origin if set on environment variables
$ACAO = getenv("Access-Control-Allow-Origin");
if ($ACAO) header("Access-Control-Allow-Origin: " . getenv("Access-Control-Allow-Origin"));

$controller = null;

if (strripos($_SERVER["REQUEST_URI"], "channels")) {	
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."ChannelsController.php");
	$controller = new ChannelsController();
}
if (strripos($_SERVER["REQUEST_URI"], "nets")) {
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."NetsController.php");
	$controller = new NetsController();
}
if (strripos($_SERVER["REQUEST_URI"], "sensortypes")) {
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."SensortypesController.php");
	$controller = new SensortypesController();
}
if (strripos($_SERVER["REQUEST_URI"], "sensors")) {
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."SensorsController.php");
	$controller = new SensorsController();
}
if (strripos($_SERVER["REQUEST_URI"], "owners")) {
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."OwnersController.php");
	$controller = new OwnersController();
}
if (strripos($_SERVER["REQUEST_URI"], "sites")) {
	require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."SitesController.php");
	$controller = new SitesController();
}