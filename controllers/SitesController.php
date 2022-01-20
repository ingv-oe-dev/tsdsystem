<?php

require_once("RESTController.php");
require_once("..\classes\PNet_Sites.php");

// Sites Controller class
Class SitesController extends RESTController {
	
	public function __construct() {
		$this->obj = new Sites();
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
		// (2) $input["lat"] 
		if (!array_key_exists("lat", $input) || !is_numeric($input["lat"])) {
			$this->setInputError("This required input is missing: 'lat' [float]");
			return false;
		}
		// (3) $input["lon"] 
		if (!array_key_exists("lon", $input) || !is_numeric($input["lon"])) {
			$this->setInputError("This required input is missing: 'lon' [float]");
			return false;
		}
		// (4) $input["info"] is json
		if (array_key_exists("info", $input) and !$this->validate_json($input["info"])){
			$this->setInputError("Error on decoding 'info' JSON input");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function get($jsonfields=array("coords","info")) {
	
		parent::get($jsonfields);

	}
}
?>