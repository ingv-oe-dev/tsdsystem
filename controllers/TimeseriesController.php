<?php

require_once("RESTController.php");
require_once("..\classes\Timeseries.php");

// Timeseries class
Class TimeseriesController extends RESTController {
	
	public function __construct() {
		$this->obj = new Timeseries();
		$this->route();
	}
	
	// ====================================================================//
	// ****************** post - timeseries instance **********************//
	// ====================================================================//
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (1) $input["schema"]
		if (!array_key_exists("schema", $input)){
			$this->setInputError("This required input is missing: 'schema' [string]");
			return false;
		}
		// (2) $input["name"] 
		if (!array_key_exists("name", $input)){
			$this->setInputError("This required input is missing: 'name' [string]");
			return false;
		}
		// (3) $input["columns"] 
		if (!array_key_exists("columns", $input) || !is_array($input["columns"])){
			$this->setInputError("This required input is missing: 'columns'[array]");
			return false;
		}
		// (4) $input["metadata"] is json
		if (array_key_exists("metadata", $input) and !$this->validate_json($input["metadata"])){
			$this->setInputError("Error on decoding 'metadata' JSON input");
			return false;
		}
		// default sampling value is null
		if (!array_key_exists("sampling", $input) || !is_int($input["sampling"]) || $input["sampling"] < 0) {
			$this->setInputError("This required input is missing: 'sampling'[integer > 0] <in seconds>");
			return false;
		}
		
		return true;
	}
	
	// ====================================================================//
	// ****************** get - timeseries instance(s) ********************//
	// ====================================================================//
	
	public function get($jsonfields=array("metadata")) {
		
		parent::get($jsonfields);

	}
	
}
?>