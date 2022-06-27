<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."Permissions.php");

// Permissions Controller class
Class PermissionsController extends RESTController {
	
	public function __construct($role_type) {
		$this->obj = new Permissions($role_type);
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				if (!$this->check_input_post()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"admin"
				));
				$this->post();
				break;

			case 'GET':
				$this->getInput();
				if (!$this->check_input_get()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"admin"
				));
				$this->get();
				break;

			default:
				# code...
				break;
		}

		$this->elaborateResponse();
	}
	
	// ====================================================================//
	// ****************** post - permission  **********************//
	// ====================================================================//
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (0) $input["role_id"] 
		if (!array_key_exists("role_id", $input) or !is_int($input["role_id"])){
			$this->setInputError("This required input is missing: 'role_id' [integer]");
			return false;
		}
		// (1) $input["settings"] is json
		if (!array_key_exists("settings", $input) or !$this->validate_json($input["settings"])){
			$this->setInputError("Error on decoding 'settings' JSON input");
			return false;
		}
		// (2) $input["active"] 
		if (array_key_exists("active", $input) and !is_bool($input["active"])){
			$this->setInputError("This required input is missing: 'active' [boolean]");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function get($jsonfields=array("settings")) {
	
		parent::get($jsonfields);
		
	}
}
?>