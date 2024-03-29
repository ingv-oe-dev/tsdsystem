<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."Roles.php");

// Roles Controller class
Class RolesController extends RESTController {
	
	public function __construct() {
		$this->obj = new Roles();
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
			
			case 'PATCH':
				$this->readInput();
				if (!$this->check_input_patch()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"admin"
				));
				$this->patch();
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

			case 'DELETE':
				$this->getInput();
				if (!$this->check_input_delete()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"admin"
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
	// ****************** POST - role **********************//
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
		// (2) $input["description"]
		if (array_key_exists("description", $input) and empty($input["description"])){
			$this->setInputError("Uncorrect input: 'description' [string]");
			return false;
		}
		
		return true;
	}
	
	// ====================================================================//
	// ****************** PATCH - role  **********************//
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
        // (1) $input["name"] 
		if (array_key_exists("name", $input) and empty($input["name"])){
			$this->setInputError("Uncorrect input: 'name' [string]");
			return false;
		}
		// (2) $input["description"]
		if (array_key_exists("description", $input) and empty($input["description"])){
			$this->setInputError("Uncorrect input: 'description' [string]");
			return false;
		}
		
		return true;
	}
}
?>