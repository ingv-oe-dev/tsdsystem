<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_Channels.php");

// Channels Controller class
Class ChannelsController extends RESTController {
	
	public $cloning = false;

	public function __construct($cloning=false) {
		$this->cloning = $cloning;
		$this->obj = new Channels();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				
				if ($this->cloning) {	
					if (!$this->check_input_post_clone()) break;
					// set input from item to clone
					$clone_data = $this->retrieve_item_to_clone($jsonfields=array("metadata","info"));
					if (!isset($clone_data)) {
						$this->setInputError("No item to clone found!");
						break;
					}
					$this->setParams($clone_data);
				} else {
					if (!$this->check_input_post()) break;
				}
				
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"channels-edit"
				));
				$this->post();
				break;

			case 'GET':
				$this->getInput();
				if (!$this->check_input_get()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"channels-read"
				));
				$this->get();
				break;

			case 'PATCH':
				$this->readInput();
				$input = $this->getParams();
				if (!$this->check_input_patch()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"channels-edit",
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
					"scope"=>"channels-edit",
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

	public function validateSensortypeMetadataByJSONSchema($input) {
		
		require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_Sensortypes.php");
		
		if (
			$input["id"] and 
			(!array_key_exists("sensortype_id", $input) or !is_int($input["sensortype_id"]))
		) {
			
			$selected_sensor = $this->obj->getList($input);
			if (
				$selected_sensor and 
				$selected_sensor["status"] and
				is_array($selected_sensor["data"]) and
				count($selected_sensor["data"]) > 0 and
				isset($selected_sensor["data"][0]["sensortype_id"]) and 
				!empty($selected_sensor["data"][0]["sensortype_id"])
			) {
				$input["sensortype_id"] = $selected_sensor["data"][0]["sensortype_id"];
			}
		}

		if(
			array_key_exists("sensortype_id", $input) and 
			isset($input["sensortype_id"]) and 
			array_key_exists("metadata", $input) and 
			isset($input["metadata"])
		 ) {
			$sensortypeObj = new Sensortypes();
			$selected = $sensortypeObj->getList(array("id" => $input["sensortype_id"]));
			//var_dump($selected);
			$result = array("status" => false);
			if (
				$selected and 
				$selected["status"] and
				is_array($selected["data"]) and
				count($selected["data"]) > 0 and
				isset($selected["data"][0]["json_schema"]) and 
				!empty($selected["data"][0]["json_schema"])
			) {
				$json_string = json_encode($input["metadata"]);
				$schema = $selected["data"][0]["json_schema"];
				$result = $this->validate_json_by_schema($json_string, $schema);
			}
			$result["sensortype_id"] = $input["sensortype_id"];
			return $result;
		}
		return null;
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
		if (!array_key_exists("name", $input) || empty($input["name"])){
			$this->setInputError("This required input is missing: 'name' [string]");
			return false;
		}
		// (3) $input["info"] is json
		if (array_key_exists("info", $input) and !$this->validate_json($input["info"])){
			$this->setInputError("Error on decoding 'info' JSON input");
			return false;
		}
		// (4) $input["metadata"] is json
		if (array_key_exists("metadata", $input)) {
			if (!$this->validate_json($input["metadata"])){
				$this->setInputError("Error on decoding 'metadata' JSON input");
				return false;
			}
			if (array_key_exists("sensortype_id", $input) and !is_int($input["sensortype_id"])) {
				// check if metadata properties are valid for the selected sensortype 
				$validAgainstSchema = $this->validateSensortypeMetadataByJSONSchema($input);
				if (isset($validAgainstSchema) and !$validAgainstSchema["status"]) {
					$error = array(
						"message" => "'metadata' are not valid for the selected sensortype_id = " . $input["sensortype_id"] . ". See the violations.",
						"violations" => $validAgainstSchema["errors"]
					);
					$this->setInputError($error);
					return false;
				}
			}
		} 
		// (5) $input["sensortype_id"] is integer
		if (array_key_exists("sensortype_id", $input)) {
			if (!(is_int($input["sensortype_id"]) or is_null($input["sensortype_id"]))) {
				$this->setInputError("Uncorrect input: 'sensortype_id' [int]");
				return false;
			}
			if (array_key_exists("metadata", $input)) {
				// check if metadata properties are valid for the selected sensortype 
				$validAgainstSchema = $this->validateSensortypeMetadataByJSONSchema($input);
				if (isset($validAgainstSchema) and !$validAgainstSchema["status"]) {
					$error = array(
						"message" => "'metadata' are not valid for the selected sensortype_id = " . $input["sensortype_id"] . ". See the violations.",
						"violations" => $validAgainstSchema["errors"]
					);
					$this->setInputError($error);
					return false;
				}
			}
		}
		// (6) $input["start_datetime"]
		if(array_key_exists("start_datetime", $input) and !$this->verifyDate($input["start_datetime"])) {
			$this->setInputError("This input is incorrect: 'start_datetime' [string] <format ISO 8601>. Your value = " . strval($input["start_datetime"]));
			return false;
		}
		// (7) $input["end_datetime"]
		if(array_key_exists("end_datetime", $input) and !$this->verifyDate($input["end_datetime"])) {
			$this->setInputError("This input is incorrect: 'end_datetime' [string] <format ISO 8601>. Your value = " . strval($input["end_datetime"]));
			return false;
		}
		return true;
	}

	// ====================================================================//
	// ****************** patch - channel **********************//
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
		// (1) $input["name]
		if (array_key_exists("name", $input) and empty($input["name"])){
			$this->setInputError("Uncorrect input: 'name' [string]");
			return false;
		}
		// (2) $input["sensor_id"]
		if (array_key_exists("sensor_id", $input) and !is_int($input["sensor_id"])){
			$this->setInputError("Uncorrect input: 'sensor_id' [integer]");
			return false;
		}
		// (3) $input["info"] is json
		if (array_key_exists("info", $input) and !$this->validate_json($input["info"])){
			$this->setInputError("Error on decoding 'info' JSON input");
			return false;
		}
		// (4) $input["metadata"] is json
		if (array_key_exists("metadata", $input)) {
			if (!$this->validate_json($input["metadata"])){
				$this->setInputError("Error on decoding 'metadata' JSON input");
				return false;
			}
			// check if metadata properties are valid for the selected sensortype 
			$validAgainstSchema = $this->validateSensortypeMetadataByJSONSchema($input);
			if (isset($validAgainstSchema) and !$validAgainstSchema["status"]) {
				$error = array(
					"message" => "'metadata' are not valid for the sensor's sensortype_id = " . $validAgainstSchema["sensortype_id"] . ". See the violations.",
					"violations" => $validAgainstSchema["errors"]
				);
				$this->setInputError($error);
				return false;
			}
		} 
		// (5) $input["sensortype_id"] is integer
		if (array_key_exists("sensortype_id", $input)) {
			if (!(is_int($input["sensortype_id"]) or is_null($input["sensortype_id"]))) {
				$this->setInputError("Uncorrect input: 'sensortype_id' [int]");
				return false;
			}
			if (array_key_exists("metadata", $input)) {
				// check if metadata properties are valid for the selected sensortype 
				$validAgainstSchema = $this->validateSensortypeMetadataByJSONSchema($input);
				if (isset($validAgainstSchema) and !$validAgainstSchema["status"]) {
					$error = array(
						"message" => "'metadata' are not valid for the selected sensortype_id = " . $input["sensortype_id"] . ". See the violations.",
						"violations" => $validAgainstSchema["errors"]
					);
					$this->setInputError($error);
					return false;
				}
			}
		}
		// (6) $input["start_datetime"]
		if(array_key_exists("start_datetime", $input) and !is_null($input["start_datetime"]) and !$this->verifyDate($input["start_datetime"])) {
			$this->setInputError("This input is incorrect: 'start_datetime' [string] <format ISO 8601>. Your value = " . strval($input["start_datetime"]));
			return false;
		}
		// (7) $input["end_datetime"]
		if(array_key_exists("end_datetime", $input) and !is_null($input["end_datetime"]) and !$this->verifyDate($input["end_datetime"])) {
			$this->setInputError("This input is incorrect: 'end_datetime' [string] <format ISO 8601>. Your value = " . strval($input["end_datetime"]));
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function get($jsonfields=array("metadata","info")) {
	
		parent::get($jsonfields);
		
	}
	
	// ====================================================================//
	// ************************ cloning functions  ************************//
	// ====================================================================//
	public function check_input_post_clone() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (0) $input["id"] 
		if (!array_key_exists("id", $input)){
			$this->setInputError("This required input is missing: 'id' [integer]");
			return false;
		}
		// (1) $input["clone_name]
		if (array_key_exists("clone_name", $input) and empty($input["clone_name"])){
			$this->setInputError("Uncorrect input: 'clone_name' [string]");
			return false;
		}

		return true;
	}

	public function retrieve_item_to_clone($jsonfields=array()) {
		
		$input = $this->getParams();
		try {	
			$result = $this->obj->getList($input);
			if ($result["status"] and count($result["data"])==1) {
				// ensure to 
				for($i=0; $i<count($result["data"]); $i++) {
					foreach($jsonfields as $fieldname) {
						$result["data"][$i][$fieldname] = isset($result["data"][$i][$fieldname]) ? json_decode($result["data"][$i][$fieldname]) : NULL;
					}
				}
				$cloning_item = $result["data"][0];
				$cloning_item["name"] .= "_copy_of_#" . $input["id"];
				if (array_key_exists("clone_name", $input)) {
					$cloning_item["name"] = $input["clone_name"];
				}
				return $cloning_item;
			}
			return null; 
		} catch(Exception $e) {
			return null;
		}
	}
}
?>