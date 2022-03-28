<?php

require_once("RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_Channels.php");

// Channels Controller class
Class ChannelsController extends RESTController {
	
	public function __construct() {
		$this->obj = new Channels();
		$this->route();
	}
	
	// ====================================================================//
	// ****************** post - channel **********************//
	// ====================================================================//
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (1) $input["sensor_id"]
		if (!array_key_exists("sensor_id", $input)){
			$this->setInputError("This required input is missing: 'sensor_id' [integer]");
			return false;
		}
		// (2) $input["name"] 
		if (!array_key_exists("name", $input)){
			$this->setInputError("This required input is missing: 'name' [string]");
			return false;
		}
		// (3) $input["info"] is json
		if (array_key_exists("info", $input) and !$this->validate_json($input["info"])){
			$this->setInputError("Error on decoding 'info' JSON input");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function get($jsonfields=array("info")) {
	
		parent::get($jsonfields);
		
	}
}
?>