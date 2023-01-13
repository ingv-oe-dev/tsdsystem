<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_Sensortypes.php");

// Sensortypes Controller class
Class SensortypesController extends RESTController {
	
	public function __construct() {
		$this->obj = new Sensortypes();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				if (!$this->check_input_post()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"sensortypes-edit"
				));
				$this->post();
				break;

			case 'GET':
				$this->getInput();
				if (!$this->check_input_get()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"sensortypes-read"
				));
				$this->get();
				break;

			case 'PATCH':
				$this->readInput();
				$input = $this->getParams();
				if (!$this->check_input_patch()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"sensortypes-edit",
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
					"scope"=>"sensortypes-edit",
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

	public function validateSensortypeResponseParametersByJSONSchema($input) {
		
		require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_SensortypeCategories.php");
		
		if (
			array_key_exists("id", $input) and $input["id"]
		) {
			// go here on patch (if id is defined)
			$selected_sensortype = $this->obj->getList(array("id" => $input["id"]));

			// if sensortype_category_id is not defined on patch input, retrieve sensortype_category_id from record
			if (!array_key_exists("sensortype_category_id", $input)) {
				if (
					$selected_sensortype and 
					$selected_sensortype["status"] and
					is_array($selected_sensortype["data"]) and
					count($selected_sensortype["data"]) > 0 and
					isset($selected_sensortype["data"][0]["sensortype_category_id"]) and 
					!empty($selected_sensortype["data"][0]["sensortype_category_id"])
				) {
					// input sensortype_category_id from record
					$input["sensortype_category_id"] = $selected_sensortype["data"][0]["sensortype_category_id"];
				}
			}
			// if sensortype_category_id is defined on patch input (checked before is numeric), retrieve response_parameters from record 
			else {
				if (
					$selected_sensortype and 
					$selected_sensortype["status"] and
					is_array($selected_sensortype["data"]) and
					count($selected_sensortype["data"]) > 0 and
					!array_key_exists("response_parameters", $input)
				) {
					// input response_parameters from record
					$input["response_parameters"] = json_decode($selected_sensortype["data"][0]["response_parameters"]);
				}
			}
		}

		// here validate from input values and/or existent values
		if(
			array_key_exists("sensortype_category_id", $input) and 
			isset($input["sensortype_category_id"]) and 
			array_key_exists("response_parameters", $input) and 
			isset($input["response_parameters"])
		 ) {
			$sensortypeCategoryObj = new SensortypeCategories();
			$selected = $sensortypeCategoryObj->getList(array("id" => $input["sensortype_category_id"]));
			$result = array("status" => false, "message" => "", "errors" => []);
			if (
				$selected and 
				$selected["status"] and
				is_array($selected["data"]) and
				count($selected["data"]) > 0 and
				isset($selected["data"][0]["json_schema"]) and 
				!empty($selected["data"][0]["json_schema"])
			) {
				$json_string = json_encode((object) $input["response_parameters"]);
				$schema = $selected["data"][0]["json_schema"];
				$result = $this->validate_json_by_schema($json_string, $schema);
			}
			$result["sensortype_category_id"] = $input["sensortype_category_id"];
			return $result;
		}
		return null;
	}
	
	// ====================================================================//
	// ****************** post - sensortype **********************//
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
		// (2) $input["model"] 
		if (array_key_exists("model", $input) and empty($input["model"])){
			$this->setInputError("Uncorrect input: 'model' [string]");
			return false;
		}
		// (3) $input["components"] is array
		if (array_key_exists("components", $input)) {
			if (
				!is_array($input["components"]) or 
				array_sum(array_map('is_string', $input["components"])) != count($input["components"])
			) {
				$this->setInputError("Uncorrect input 'components' [array of strings]");
				return false;
			}
		}
		// (4) $input["response_parameters"] is json
		if (array_key_exists("response_parameters", $input)) {
			if (!$this->validate_json($input["response_parameters"])){
				$this->setInputError("Error on decoding 'response_parameters' JSON input");
				return false;
			}
			if (array_key_exists("sensortype_category_id", $input) and is_int($input["sensortype_category_id"]) and $input["sensortype_category_id"] > 0) {
				// check if response_parameters properties are valid for the selected sensortype 
				$validAgainstSchema = $this->validateSensortypeResponseParametersByJSONSchema($input);
				if (isset($validAgainstSchema) and !$validAgainstSchema["status"]) {
					$error = array(
						"message" => "'response_parameters' are not valid for the selected sensortype_category_id = " . $input["sensortype_category_id"] . ". See the violations.",
						"violations" => $validAgainstSchema["errors"]
					);
					$this->setInputError($error);
					return false;
				}
			}
		} 
		// (5) $input["sensortype_category_id"] is integer
		if (array_key_exists("sensortype_category_id", $input)) {
			if (!((is_int($input["sensortype_category_id"]) and $input["sensortype_category_id"] > 0) or is_null($input["sensortype_category_id"]))) {
				$this->setInputError("Uncorrect input: 'sensortype_category_id' [int] > 0");
				return false;
			}
			if (array_key_exists("response_parameters", $input)) {
				// check if response_parameters properties are valid for the selected sensortype 
				$validAgainstSchema = $this->validateSensortypeResponseParametersByJSONSchema($input);
				if (isset($validAgainstSchema) and !$validAgainstSchema["status"]) {
					$error = array(
						"message" => "'response_parameters' are not valid for the selected sensortype_category_id = " . $input["sensortype_category_id"] . ". See the violations.",
						"violations" => $validAgainstSchema["errors"]
					);
					$this->setInputError($error);
					return false;
				}
			}
		}
		// (6) $input["additional_info"] is json
		if (array_key_exists("additional_info", $input) and !$this->validate_json($input["additional_info"])){
			$this->setInputError("Error on decoding 'additional_info' JSON input");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** patch - sensortype **********************//
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
		// (2) $input["model"] 
		if (array_key_exists("model", $input) and empty($input["model"])){
			$this->setInputError("Uncorrect input: 'model' [string]");
			return false;
		}
		// (3) $input["components"] is array
		if (array_key_exists("components", $input)) {
			if (
				!is_array($input["components"]) or 
				array_sum(array_map('is_string', $input["components"])) != count($input["components"])
			) {
				$this->setInputError("Uncorrect input 'components' [array of strings]");
				return false;
			}
		}
		// (4) $input["response_parameters"] is json
		if (array_key_exists("response_parameters", $input)) {
			if (!$this->validate_json($input["response_parameters"])){
				$this->setInputError("Error on decoding 'response_parameters' JSON input");
				return false;
			}
			// check if response_parameters properties are valid for the selected sensortype 
			$validAgainstSchema = $this->validateSensortypeResponseParametersByJSONSchema($input);
			if (isset($validAgainstSchema) and !$validAgainstSchema["status"]) {
				$error = array(
					"message" => "'response_parameters' are not valid for the sensortype_category_id = " . $validAgainstSchema["sensortype_category_id"] . ". See the violations.",
					"violations" => $validAgainstSchema["errors"]
				);
				$this->setInputError($error);
				return false;
			}
		} 
		// (5) $input["sensortype_category_id"] is integer
		if (array_key_exists("sensortype_category_id", $input)) {
			if (!((is_int($input["sensortype_category_id"]) and $input["sensortype_category_id"] > 0) or is_null($input["sensortype_category_id"]))) {
				$this->setInputError("Uncorrect input: 'sensortype_category_id' [int]");
				return false;
			}
			// check if response_parameters (inputed or existent) properties are valid for the selected sensortype 
			$validAgainstSchema = $this->validateSensortypeResponseParametersByJSONSchema($input);
			if (isset($validAgainstSchema) and !$validAgainstSchema["status"]) {
				$error = array(
					"message" => "'response_parameters' are not valid for the selected sensortype_category_id = " . $input["sensortype_category_id"] . ". See the violations.",
					"violations" => $validAgainstSchema["errors"]
				);
				$this->setInputError($error);
				return false;
			}
		}
		// (6) $input["additional_info"] is json
		if (array_key_exists("additional_info", $input) and !$this->validate_json($input["additional_info"])){
			$this->setInputError("Error on decoding 'additional_info' JSON input");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function get($jsonfields=array("components", "response_parameters", "additional_info")) {
	
		parent::get($jsonfields);
		
	}
}
?>