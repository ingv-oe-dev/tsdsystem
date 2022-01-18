<?php

require_once("RESTController.php");
require_once("..\classes\Sensors.php");

// Sensors Controller class
Class SensorsController extends RESTController {
	
	public function __construct() {
		$this->obj = new Sensors();
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
		
		// (1) $input["name"] 
		if (!array_key_exists("name", $input)){
			$this->setInputError("This required input is missing: 'name' [string]");
			return false;
		}
		// (2) $input["lon"] 
		if (array_key_exists("lat", $input) and (!array_key_exists("lon", $input) || !is_numeric($input["lon"]))) {
			$this->setInputError("Uncorrect input: 'lon' [float]");
			return false;
		}
		// (3) $input["lat"] 
		if (array_key_exists("lon", $input) and (!array_key_exists("lat", $input) || !is_numeric($input["lat"]))) {
			$this->setInputError("Uncorrect input: 'lat' [float]");
			return false;
		}
		// (4) $input["metadata"] is json
		if (array_key_exists("metadata", $input) and !$this->validate_json($input["metadata"])){
			$this->setInputError("Error on decoding 'metadata' JSON input");
			return false;
		}
		// (5) $input["custom_props"] is json
		if (array_key_exists("custom_props", $input) and !$this->validate_json($input["custom_props"])){
			$this->setInputError("Error on decoding 'custom_props' JSON input");
			return false;
		}
		// (6) $input["sensortype_id"] is integer
		if (array_key_exists("sensortype_id", $input) and !is_int($input["sensortype_id"])){
			$this->setInputError("Uncorrect input: 'sensortype_id' [int]");
			return false;
		}
		// (7) $input["net_id"] is integer
		if (array_key_exists("net_id", $input) and !is_int($input["net_id"])){
			$this->setInputError("Uncorrect input: 'net_id' [int]");
			return false;
		}
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function get($jsonfields=array("coords","metadata","custom_props")) {
	
		parent::get($jsonfields);

	}
}
?>