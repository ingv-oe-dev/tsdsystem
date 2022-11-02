<?php

if (getenv("ENV") == 'development') {
	error_reporting(-1);
	ini_set('display_errors', 'On');
}

require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."ChannelsController.php");

if (strripos($_SERVER["REQUEST_URI"], "clone")) {
    $controller = new ChannelsController($cloning=true);
} else {	
    $controller = new ChannelsController();
}