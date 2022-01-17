<?php

require_once("PNetController.php");
require_once("..\classes\Channels.php");

// Channels Controller class
Class ChannelsController extends PNetController {
	
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
		
		return true;
	}
}
?>