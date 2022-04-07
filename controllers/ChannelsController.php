<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_Channels.php");

// Channels Controller class
Class ChannelsController extends RESTController {
	
	public function __construct() {
		$this->obj = new Channels();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				if (!$this->check_input_post()) break;
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
				# code...
				break;

			case 'DELETE':
				# code...
				break;

			default:
				# code...
				break;
		}

		$this->elaborateResponse();
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
		if (!array_key_exists("name", $input)){
			$this->setInputError("This required input is missing: 'name' [string]");
			return false;
		}
		// (3) $input["info"] is json
		if (array_key_exists("info", $input) and !$this->validate_json($input["info"])){
			$this->setInputError("Error on decoding 'info' JSON input");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function get($jsonfields=array("info")) {
	
		parent::get($jsonfields);
		
	}
}
?>