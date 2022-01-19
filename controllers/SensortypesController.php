<?php

require_once("RESTController.php");
require_once("..\classes\pnet\Sensortypes.php");

// Sensortypes Controller class
Class SensortypesController extends RESTController {
	
	public function __construct() {
		$this->obj = new Sensortypes();
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
		// (2) $input["json_schema"] is json
		if (array_key_exists("json_schema", $input) and !$this->validate_json($input["json_schema"])){
			$this->setInputError("Error on decoding 'json_schema' JSON input");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function get($jsonfields=array("json_schema")) {
	
		parent::get($jsonfields);
		
	}
}
?>