<?php

	error_reporting(-1);
	ini_set('display_errors', 'On');
	
	$controller = null;
	
	if (strripos($_SERVER["REQUEST_URI"], "channels")) {	
		require_once("../controllers/ChannelsController.php");
		$controller = new ChannelsController();
	}
	if (strripos($_SERVER["REQUEST_URI"], "nets")) {
		require_once("../controllers/NetsController.php");
		$controller = new NetsController();
	}
	if (strripos($_SERVER["REQUEST_URI"], "sensortypes")) {
		require_once("../controllers/SensortypesController.php");
		$controller = new SensortypesController();
	}
	if (strripos($_SERVER["REQUEST_URI"], "sensors")) {
		require_once("../controllers/SensorsController.php");
		$controller = new SensorsController();
	}
	if (strripos($_SERVER["REQUEST_URI"], "owners")) {
		require_once("../controllers/OwnersController.php");
		$controller = new OwnersController();
	}
	if (strripos($_SERVER["REQUEST_URI"], "sites")) {
		require_once("../controllers/SitesController.php");
		$controller = new SitesController();
	}