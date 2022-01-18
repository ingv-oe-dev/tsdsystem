<?php

	error_reporting(-1);
	ini_set('display_errors', 'On');
	
	
	if (strripos($_SERVER["REQUEST_URI"], "channels")) {
		
		require_once("../controllers/ChannelsController.php");
		
		$cc = new ChannelsController();
	}
	
	if (strripos($_SERVER["REQUEST_URI"], "nets")) {
		
		require_once("../controllers/NetsController.php");
		
		$nc = new NetsController();
	}
	
	if (strripos($_SERVER["REQUEST_URI"], "sensortypes")) {
		
		require_once("../controllers/SensortypesController.php");
		
		$nc = new SensortypesController();
	}