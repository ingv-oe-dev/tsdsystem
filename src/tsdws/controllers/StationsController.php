<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_Stations.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."encoders".DIRECTORY_SEPARATOR."PNet_Stations_Encoder.php");

// Stations Controller class
Class StationsController extends RESTController {
	
	public $contentTypesArray = array(
		"json" => "application/json",
		"geojson" => "application/json"
	);

	public function __construct() {
		$this->obj = new Stations();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				if (!$this->check_input_post()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"stations-edit"
				));
				$this->post();
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
	// ************* OVERRIDE SimpleREST->elaborateResponse() ************ //
	// ====================================================================//
	public function elaborateResponse() {
		
		// set header
		$this->setHttpHeaders($this->response["statusCode"]);

		// instantiation of Encoder class
		$encoder = new PNet_Stations_Encoder();

		// compress the response before send response
		ob_start("ob_gzhandler"); // start compression
		echo $encoder->encodeResponse($this->response);
		ob_end_flush(); // end compression
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
		if (array_key_exists("quote", $input) and !(is_numeric($input["quote"]) or is_null($input["quote"]))) {
			$this->setInputError("Uncorrect input: 'quote' [float]");
			return false;
		}
		// (5) $input["additional_info"] is json
		if (array_key_exists("additional_info", $input) and !$this->validate_json($input["additional_info"])){
			$this->setInputError("Error on decoding 'additional_info' JSON input");
			return false;
		}
		// (6) $input["net_id"] is integer
		if (array_key_exists("net_id", $input) and !(is_int($input["net_id"]) or is_null($input["net_id"]))) {
			$this->setInputError("Uncorrect input: 'net_id' [int]");
			return false;
		}
		// (7) $input["site_id"] is integer
		if (array_key_exists("site_id", $input) and !(is_int($input["site_id"]) or is_null($input["site_id"]))){
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
		if (!array_key_exists("id", $input) or !is_numeric($input["id"])){
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
		if (array_key_exists("quote", $input) and !(is_numeric($input["quote"]) or is_null($input["quote"]))) {
			$this->setInputError("Uncorrect input: 'quote' [float]");
			return false;
		}
		// (4) $input["additional_info"] is json
		if (array_key_exists("additional_info", $input) and !$this->validate_json($input["additional_info"])){
			$this->setInputError("Error on decoding 'additional_info' JSON input");
			return false;
		}
		// (5) $input["net_id"] is integer
		if (array_key_exists("net_id", $input) and !(is_int($input["net_id"]) or is_null($input["net_id"]))) {
			$this->setInputError("Uncorrect input: 'net_id' [int]");
			return false;
		}
		// (6) $input["site_id"] is integer
		if (array_key_exists("site_id", $input) and !(is_int($input["site_id"]) or is_null($input["site_id"]))){
			$this->setInputError("Uncorrect input: 'site_id' [int]");
			return false;
		}
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function check_input_get() {

		$input = $this->getParams();

		// $input["format"] 
		if (array_key_exists("format", $input)){
			if (!in_array(strtolower($input["format"]), array_keys($this->contentTypesArray))) {
				$this->setInputError("Uncorrect input: 'format' [available: " . implode(",", array_keys($this->contentTypesArray)) . "]. Your value: " . $input["format"]);
				return false;
			}
		} else {
			$input["format"] = "json"; // default
		}

		// if here, 'format' input is set
		$input["contentType"] = $this->contentTypesArray[$input["format"]];

		$this->setParams($input);

		// check only if spatial inputs are defined and numerical
		return $this->check_spatial_input();
	}
	
	public function get($jsonfields=array("coords","additional_info")) {
		// coords will be returned in GeoJSON format (as in SitesController.php)
		parent::get($jsonfields);
	}
}
?>