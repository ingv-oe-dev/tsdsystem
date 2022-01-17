<?php

	error_reporting(-1);
	ini_set('display_errors', 'On');
	
	
	if (strripos($_SERVER["REQUEST_URI"], "values")) {
		
		require_once("../controllers/TimeseriesValuesController.php");
		
		$tsc = new TimeseriesValuesController();
	}
	else {
		
		require_once("../controllers/TimeseriesController.php");
		
		$tsc = new TimeseriesController();
	}
	