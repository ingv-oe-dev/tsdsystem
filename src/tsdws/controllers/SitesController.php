<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_Sites.php");

// Sites Controller class
Class SitesController extends RESTController {
	
	public function __construct() {
		$this->obj = new Sites();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				if (!$this->check_input_post()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"sites-edit"
				));
				$this->post();
				break;

			case 'GET':
				$this->getInput();
				if (!$this->check_input_get()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"sites-read"
				));
				$this->get();
				break;

			case 'PATCH':
				$this->readInput();
				$input = $this->getParams();
				if (!$this->check_input_patch()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"sites-edit",
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
					"scope"=>"sites-edit",
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
	// ****************** post - sites **********************//
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
		// (3) $input["quote"] 
		if (array_key_exists("quote", $input) and !is_numeric($input["quote"])) {
			$this->setInputError("Uncorrect input: 'quote' [float]");
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
	// ****************** patch - sites **********************//
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
		// (2) $input["lat"] 
		if (array_key_exists("lat", $input) and !is_numeric($input["lat"])) {
			$this->setInputError("Uncorrect input: 'lat' [float]");
			return false;
		}
		// (3) $input["lon"] 
		if (array_key_exists("lon", $input) and !is_numeric($input["lon"])) {
			$this->setInputError("Uncorrect input: 'lon' [float]");
			return false;
		}
		// (3) $input["quote"] 
		if (array_key_exists("quote", $input) and !is_numeric($input["quote"])) {
			$this->setInputError("Uncorrect input: 'quote' [float]");
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