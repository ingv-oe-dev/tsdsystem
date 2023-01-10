<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_StationConfigs.php");

// StationConfigs Controller class
Class StationConfigsController extends RESTController {
	
	public function __construct() {
		$this->obj = new StationConfigs();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				if (strripos($_SERVER["REQUEST_URI"], "generateChannels")) { // generate channels for the configuration by id

					if (!$this->check_input_generateChannels()) break;
					// check if authorized action
					$this->authorizedAction(array(
						"scope"=>"stations-edit"
					));
					$this->generateChannels();

				} else { // save a new configuration

					if (!$this->check_input_post()) break;
					// check if authorized action
					$this->authorizedAction(array(
						"scope"=>"stations-edit"
					));
					$this->post();
				}
				
				break;

			case 'GET':
				$this->getInput();
				if (!$this->check_input_get()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"stations-read"
				));
				$this->get();
				break;

			case 'PATCH':
				$this->readInput();
				$input = $this->getParams();
				if (!$this->check_input_patch()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"stations-edit",
					"resource_id"=>$input["id"]
				));
				$this->patch();
				break;

			case 'DELETE':
				$this->getInput();
				$input = $this->getParams();
				if (!$this->check_input_delete()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"stations-edit",
					"resource_id"=>$input["id"]
				));
				$this->delete();
				break;

			default:
				# code...
				break;
		}

		$this->elaborateResponse();
	}
	
	// ====================================================================//
	// ****************** post - station config **********************//
	// ====================================================================//
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (0) $input["station_id"]
		if (!array_key_exists("station_id", $input)){
			$this->setInputError("This required input is missing: 'station_id' [integer]");
			return false;
		}
		// (1) $input["sensor_id"]
		if (array_key_exists("sensor_id", $input) and !is_int($input["sensor_id"])){
			$this->setInputError("This required input is missing: 'sensor_id' [integer]");
			return false;
		}
		// (2) $input["digitizer_id"] 
		if (array_key_exists("digitizer_id", $input) and !is_int($input["digitizer_id"])){
			$this->setInputError("This required input is missing: 'digitizer_id' [integer]");
			return false;
		}
		// (3) $input["start_datetime"]
		if (!array_key_exists("start_datetime", $input)){
			$this->setInputError("This required input is missing: 'start_datetime' [integer]");
			return false;
		}
		if(array_key_exists("start_datetime", $input) and !$this->verifyDate($input["start_datetime"])) {
			$this->setInputError("This input is incorrect: 'start_datetime' [string] <format ISO 8601>. Your value = " . strval($input["start_datetime"]));
			return false;
		}
		// (4) $input["end_datetime"]
		if(array_key_exists("end_datetime", $input) and !$this->verifyDate($input["end_datetime"])) {
			$this->setInputError("This input is incorrect: 'end_datetime' [string] <format ISO 8601>. Your value = " . strval($input["end_datetime"]));
			return false;
		}
		// (5) $input["additional_info"] is json
		if (array_key_exists("additional_info", $input) and !$this->validate_json($input["additional_info"])){
			$this->setInputError("Error on decoding 'additional_info' JSON input");
			return false;
		}
		return true;
	}

	// ====================================================================//
	// ****************** patch - station config  **********************//
	// ====================================================================//
	public function check_input_patch() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (0) $input["id"] 
		if (!array_key_exists("id", $input) or !is_numeric($input["id"])){
			$this->setInputError("This required input is missing: 'id' [integer]");
			return false;
		}
		// (1) $input["sensor_id"]
		if (array_key_exists("sensor_id", $input) and !is_int($input["sensor_id"])){
			$this->setInputError("This required input is missing: 'sensor_id' [integer]");
			return false;
		}
		// (2) $input["digitizer_id"] 
		if (array_key_exists("digitizer_id", $input) and !is_int($input["digitizer_id"])){
			$this->setInputError("This required input is missing: 'digitizer_id' [integer]");
			return false;
		}
		// (3) $input["start_datetime"]
		if(array_key_exists("start_datetime", $input) and !$this->verifyDate($input["start_datetime"])) {
			$this->setInputError("This input is incorrect: 'start_datetime' [string] <format ISO 8601>. Your value = " . strval($input["start_datetime"]));
			return false;
		}
		// (4) $input["end_datetime"]
		if(array_key_exists("end_datetime", $input) and !$this->verifyDate($input["end_datetime"])) {
			$this->setInputError("This input is incorrect: 'end_datetime' [string] <format ISO 8601>. Your value = " . strval($input["end_datetime"]));
			return false;
		}
		// (5) $input["additional_info"] is json
		if (array_key_exists("additional_info", $input) and !$this->validate_json($input["additional_info"])){
			$this->setInputError("Error on decoding 'additional_info' JSON input");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function get($jsonfields=array("additional_info")) {
	
		parent::get($jsonfields);
		
	}

	// ====================================================================//
	// ****************** generate channels  ********************//
	// ====================================================================//
	public function check_input_generateChannels() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (0) $input["id"] 
		if (!array_key_exists("id", $input) or !is_numeric($input["id"])){
			$this->setInputError("This required input is missing: 'id' [integer]");
			return false;
		}
		
		return true;
	}

	public function generateChannels() {

		$input = $this->getParams();
		$auth_data = $this->_get_auth_data();
		$input["create_user"] = isset($auth_data) ? $auth_data["userId"] : NULL;
		$input["remove_user"] = isset($auth_data) ? $auth_data["userId"] : NULL;
		$input["remove_time"] = "timezone('utc'::text, now())";

		$result = $this->obj->generateChannels($input);
		
		if ($result["status"]) {
			$this->setData($result);
			if (isset($result["created_channels"])) {
				$this->setStatusCode(201);
			}
		} else {
			$this->setStatusCode(409);
			$this->setError($result);
		}
	}

}
?>