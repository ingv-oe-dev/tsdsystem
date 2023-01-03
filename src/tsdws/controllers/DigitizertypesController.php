<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_Digitizertypes.php");

// Digitizertypes Controller class
Class DigitizertypesController extends RESTController {
	
	public function __construct() {
		$this->obj = new Digitizertypes();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				if (!$this->check_input_post()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"digitizertypes-edit"
				));
				$this->post();
				break;

			case 'GET':
				$this->getInput();
				if (!$this->check_input_get()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"digitizertypes-read"
				));
				$this->get();
				break;

			case 'PATCH':
				$this->readInput();
				$input = $this->getParams();
				if (!$this->check_input_patch()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"digitizertypes-edit",
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
					"scope"=>"digitizertypes-edit",
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
	// ****************** post - digitizertypes **********************//
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
		// (3) $input["final_sample_rate"] 
		if (array_key_exists("final_sample_rate", $input) and !is_numeric($input["final_sample_rate"])) {
			$this->setInputError("Uncorrect input: 'final_sample_rate' [numeric]");
			return false;
		}
		// (4) $input["final_sample_rate_measure_unit"] 
		if (array_key_exists("final_sample_rate_measure_unit", $input) and empty($input["final_sample_rate_measure_unit"])){
			$this->setInputError("Uncorrect input: 'final_sample_rate_measure_unit' [string]");
			return false;
		}
		// (5) $input["sensitivity"] 
		if (array_key_exists("sensitivity", $input) and !is_numeric($input["sensitivity"])) {
			$this->setInputError("Uncorrect input: 'sensitivity' [numeric]");
			return false;
		}
		// (6) $input["sensitivity_measure_unit"] 
		if (array_key_exists("sensitivity_measure_unit", $input) and empty($input["sensitivity_measure_unit"])){
			$this->setInputError("Uncorrect input: 'sensitivity_measure_unit' [string]");
			return false;
		}
		// (7) $input["dynamical_range"] 
		if (array_key_exists("dynamical_range", $input) and !is_numeric($input["dynamical_range"])) {
			$this->setInputError("Uncorrect input: 'dynamical_range' [numeric]");
			return false;
		}
		// (8) $input["dynamical_range_measure_unit"] 
		if (array_key_exists("dynamical_range_measure_unit", $input) and empty($input["dynamical_range_measure_unit"])){
			$this->setInputError("Uncorrect input: 'dynamical_range_measure_unit' [string]");
			return false;
		}
		// (9) $input["additional_info"] is json
		if (array_key_exists("additional_info", $input) and !$this->validate_json($input["additional_info"])){
			$this->setInputError("Error on decoding 'additional_info' JSON input");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** patch - digitizertypes **********************//
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
		// (3) $input["final_sample_rate"] 
		if (array_key_exists("final_sample_rate", $input) and (empty($input["final_sample_rate"]) or !is_numeric($input["final_sample_rate"]))) {
			$this->setInputError("Uncorrect input: 'final_sample_rate' [numeric]");
			return false;
		}
		// (4) $input["final_sample_rate_measure_unit"] 
		if (array_key_exists("final_sample_rate_measure_unit", $input) and empty($input["final_sample_rate_measure_unit"])){
			$this->setInputError("Uncorrect input: 'final_sample_rate_measure_unit' [string]");
			return false;
		}
		// (5) $input["sensitivity"] 
		if (array_key_exists("sensitivity", $input) and (empty($input["sensitivity"]) or !is_numeric($input["sensitivity"]))) {
			$this->setInputError("Uncorrect input: 'sensitivity' [numeric]");
			return false;
		}
		// (6) $input["sensitivity_measure_unit"] 
		if (array_key_exists("sensitivity_measure_unit", $input) and empty($input["sensitivity_measure_unit"])){
			$this->setInputError("Uncorrect input: 'sensitivity_measure_unit' [string]");
			return false;
		}
		// (7) $input["dynamical_range"] 
		if (array_key_exists("dynamical_range", $input) and (empty($input["dynamical_range"]) or !is_numeric($input["dynamical_range"]))) {
			$this->setInputError("Uncorrect input: 'dynamical_range' [numeric]");
			return false;
		}
		// (8) $input["dynamical_range_measure_unit"] 
		if (array_key_exists("dynamical_range_measure_unit", $input) and empty($input["dynamical_range_measure_unit"])){
			$this->setInputError("Uncorrect input: 'dynamical_range_measure_unit' [string]");
			return false;
		}
		// (9) $input["additional_info"] is json
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
}
?>