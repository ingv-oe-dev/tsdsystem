<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_Sensors.php");

// Sensors Controller class
Class SensorsController extends RESTController {
	
	public function __construct() {
		$this->obj = new Sensors();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				if (!$this->check_input_post()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"sensors-edit"
				));
				$this->post();
				break;

			case 'GET':
				$this->getInput();
				if (!$this->check_input_get()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"sensors-read"
				));
				$this->get();
				break;

			case 'PATCH':
				$this->readInput();
				$input = $this->getParams();
				if (!$this->check_input_patch()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"sensors-edit",
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
					"scope"=>"sensors-edit",
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
	// ****************** post - sensor **********************//
	// ====================================================================//
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (1) $input["name"] 
		if (!array_key_exists("name", $input) || empty($input["name"])){
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
		// (3) $input["quote"] 
		if (array_key_exists("quote", $input) and !is_numeric($input["quote"])) {
			$this->setInputError("Uncorrect input: 'quote' [float]");
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
		// (5) $input["custom_props"] is json
		if (array_key_exists("custom_props", $input) and !$this->validate_json($input["custom_props"])){
			$this->setInputError("Error on decoding 'custom_props' JSON input");
			return false;
		}
		// (6) $input["sensortype_id"] is integer
		if (array_key_exists("sensortype_id", $input)) {
			if (!is_int($input["sensortype_id"])) {
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
		// (7) $input["net_id"] is integer
		if (array_key_exists("net_id", $input) and !is_int($input["net_id"])){
			$this->setInputError("Uncorrect input: 'net_id' [int]");
			return false;
		}
		// (7) $input["site_id"] is integer
		if (array_key_exists("site_id", $input) and !is_int($input["site_id"])){
			$this->setInputError("Uncorrect input: 'site_id' [int]");
			return false;
		}
		return true;
	}

	// ====================================================================//
	// ****************** patch - sensor **********************//
	// ====================================================================//
	public function check_input_patch() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (0) $input["id"] 
		if (!array_key_exists("id", $input) or !is_int($input["id"])){
			$this->setInputError("This required input is missing: 'id' [integer]");
			return false;
		}
		// (1) $input["name]
		if (array_key_exists("name", $input) and empty($input["name"])){
			$this->setInputError("Uncorrect input: 'name' [string]");
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
		// (3) $input["quote"] 
		if (array_key_exists("quote", $input) and !is_numeric($input["quote"])) {
			$this->setInputError("Uncorrect input: 'quote' [float]");
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
		// (5) $input["custom_props"] is json
		if (array_key_exists("custom_props", $input) and !$this->validate_json($input["custom_props"])){
			$this->setInputError("Error on decoding 'custom_props' JSON input");
			return false;
		}
		// (6) $input["sensortype_id"] is integer
		if (array_key_exists("sensortype_id", $input)) {
			if (!is_int($input["sensortype_id"])) {
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
		// (7) $input["net_id"] is integer
		if (array_key_exists("net_id", $input) and !is_int($input["net_id"])){
			$this->setInputError("Uncorrect input: 'net_id' [int]");
			return false;
		}
		// (7) $input["site_id"] is integer
		if (array_key_exists("site_id", $input) and !is_int($input["site_id"])){
			$this->setInputError("Uncorrect input: 'site_id' [int]");
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