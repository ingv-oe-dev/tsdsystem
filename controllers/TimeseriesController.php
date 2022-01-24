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
		} else {
			$input["metadata"] = $input["columns"];
		}
		// default sampling value is null
		if (!array_key_exists("sampling", $input) || !is_int($input["sampling"]) || $input["sampling"] < 0) {
			$this->setInputError("This required input is missing: 'sampling'[integer > 0] <in seconds>");
			return false;
		}
		// check mapping values
		if (array_key_exists("mapping", $input) and !$this->check_mapping_values($input["mapping"])) {
			return false;
		}

		$this->setParams($input);

		return true;
	}
	
	public function check_mapping_values($input) {
		if (!$this->validate_json($input)){
			$this->setInputError("Error on decoding 'mapping' JSON input");
			return false;
		}
		if (array_key_exists("channel_id", $input)){
			if(is_array($input["channel_id"])) {
				foreach($input["channel_id"] as $index => $id) {
					if (!is_int($id)) {
						$this->setInputError("Error on index $index into 'mapping->channel_id' [array of int]: NOT AN INTEGER VALUE. Your value = " . strval($id));
						return false;
					}
				}
			} else {
				$this->setInputError("Error on input 'mapping->channel_id' [array of int]");
				return false;
			}
		}
		if (array_key_exists("sensor_id", $input)){
			if(is_array($input["sensor_id"])) {
				foreach($input["sensor_id"] as $index => $id) {
					if (!is_int($id)) {
						$this->setInputError("Error on index $index into 'mapping->sensor_id' [array of int]: NOT AN INTEGER VALUE. Your value = " . strval($id));
						return false;
					}
				}
			} else {
				$this->setInputError("Error on input 'mapping->sensor_id' [array of int]");
				return false;
			}
		}
		return true;
	}

	// ====================================================================//
	// ****************** get - timeseries instance(s) ********************//
	// ====================================================================//
	
	public function get($jsonfields=array("metadata")) {
		
		parent::get($jsonfields);

	}
	
	// ====================================================================//
	// ****************** patch - timeseries instance **********************//
	// ====================================================================//
	public function check_input_patch() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		// (0) $input["timeseries_id"]
		if (!array_key_exists("timeseries_id", $input) or !$this->isValidUUID($input["timeseries_id"])) {
			$this->setInputError("This required input is missing: 'timeseries_id' [uuid string]");
			return false;
		}
		// $input["metadata"] is json
		if (array_key_exists("metadata", $input) and !$this->validate_json($input["metadata"])){
			$this->setInputError("Error on decoding 'metadata' JSON input");
			return false;
		}
		// $input["sampling"]
		if (array_key_exists("sampling", $input) and (!is_int($input["sampling"]) || $input["sampling"] < 0)) {
			$this->setInputError("This required input is missing: 'sampling'[integer > 0] <in seconds>");
			return false;
		}
		// check mapping values
		if (array_key_exists("mapping", $input) and !$this->check_mapping_values($input["mapping"])) {
			return false;
		}

		return true;
	}
}
?>