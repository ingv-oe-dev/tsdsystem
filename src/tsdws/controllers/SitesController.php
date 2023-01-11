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

	public function check_coords() {
		
		$input = $this->getParams();
		
		if (!array_key_exists("coords", $input)) {
			$this->setInputError("This required input is missing: 'coords' [GeoJSON]");
			return false;
		} else {
			if (!$this->validate_json($input["coords"])) {
				$this->setInputError("Error on decoding 'coords' [GeoJSON]");
				return false;
			}
			if (!array_key_exists("type", $input["coords"])) {
				$this->setInputError("Error on decoding 'coords' [GeoJSON]: missing 'type' section");
				return false;
			}
			if (!in_array($input["coords"]["type"], array("Point", "Polygon"))) {
				$this->setInputError("Error on decoding 'coords' [GeoJSON]: error on 'type' section. Your value = " . $input["coords"]["type"] . ". Available ('Point', 'Polygon') [CASE SENSITIVE]");
				return false;
			}
			if (!array_key_exists("coordinates", $input["coords"])) {
				$this->setInputError("Error on decoding 'coords' [GeoJSON]: missing 'coordinates' section");
				return false;
			}
			if (!is_array($input["coords"]["coordinates"])) {
				$this->setInputError("Error on decoding 'coords' [GeoJSON]: error on 'coordinates' section. It must be an array");
				return false;
			}
			if ($input["coords"]["type"] == "Polygon" and count($input["coords"]["coordinates"]) < 4) {
				$this->setInputError("Error on decoding 'coords' [GeoJSON]: error on 'coordinates' section. For Polygon type it must contains at least 4 coordinates");
				return false;
			}
			if ($input["coords"]["type"] == "Point") {
				$input["coords"]["coordinates"] = $input["coords"]["coordinates"][0];
			} else {
				if ($input["coords"]["coordinates"][0] != $input["coords"]["coordinates"][count($input["coords"]["coordinates"]) - 1]) {
					$this->setInputError("Error on decoding 'coords' [GeoJSON]: error on 'coordinates' section. For Polygon the last coordinate must be equal to first coordinate");
					return false;
				}
				$input["coords"]["coordinates"] = array($input["coords"]["coordinates"]);
			}
			$this->setParams($input);
		}

		return true;
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
		// (2) $input["coords"] 
		if (!$this->check_coords()) {
			return false;
		}
		// (3) $input["quote"] 
		if (array_key_exists("quote", $input) and !(is_numeric($input["quote"]) or is_null($input["quote"]))) {
			$this->setInputError("Uncorrect input: 'quote' [float]");
			return false;
		}
		// (4) $input["additional_info"] is json
		if (array_key_exists("additional_info", $input) and !$this->validate_json($input["additional_info"])){
			$this->setInputError("Error on decoding 'additional_info' JSON input");
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
		if (!array_key_exists("id", $input) or !is_numeric($input["id"])){
			$this->setInputError("This required input is missing: 'id' [integer]");
			return false;
		}
		// (1) $input["name]
		if (array_key_exists("name", $input) and empty($input["name"])){
			$this->setInputError("Uncorrect input: 'name' [string]");
			return false;
		}
		// (2) $input["coords"] 
		if (!$this->check_coords()) {
			return false;
		}
		// (3) $input["quote"] 
		if (array_key_exists("quote", $input) and !(is_numeric($input["quote"]) or is_null($input["quote"]))) {
			$this->setInputError("Uncorrect input: 'quote' [float]");
			return false;
		}
		// (4) $input["additional_info"] is json
		if (array_key_exists("additional_info", $input) and !$this->validate_json($input["additional_info"])){
			$this->setInputError("Error on decoding 'additional_info' JSON input");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function check_input_get() {
		// check only if spatial inputs are defined and numerical
		return $this->check_spatial_input();
	}
	
	public function get($jsonfields=array("coords","centroid","additional_info")) {
	
		parent::get($jsonfields);

	}
}
?>