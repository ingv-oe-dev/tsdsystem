<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RolesMapping.php");

// RolesMapping Controller class
Class RolesMappingController extends RESTController {
	
	public function __construct() {
		$this->obj = new RolesMapping();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				$input = $this->getParams();
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

			case 'DELETE':
				$this->readInput();
				$input = $this->getParams();
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
	// ****************** POST - roles mapping  **********************//
	// ====================================================================//
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (0) $input["member_id"] 
		if (!array_key_exists("member_id", $input) or !is_numeric($input["member_id"])){
			$this->setInputError("This required input is missing: 'member_id' [integer]");
			return false;
		}
        // (1) $input["role_id"] 
		if (!array_key_exists("role_id", $input) or !is_numeric($input["role_id"])){
			$this->setInputError("This required input is missing: 'role_id' [integer]");
			return false;
		}
		// (2) $input["priority"] 
		if (array_key_exists("priority", $input)){
			if (!is_numeric($input["priority"])) {
				$this->setInputError("Uncorrect input: 'priority' [integer]");
				return false;
			}
		} else {
			$input["priority"] = 0;
		}

		$this->setParams($input);
		
		return true;
	}

	// ====================================================================//
	// ****************** DELETE - roles mapping  **********************//
	// ====================================================================//
	public function check_input_delete() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (0) $input["member_id"] 
		if (!array_key_exists("member_id", $input) or !is_numeric($input["member_id"])) {
			$this->setInputError("This required input is missing: 'member_id'");
			return false;
		}

		// (0) $input["role_id"] 
		if (!array_key_exists("role_id", $input) or !is_numeric($input["role_id"])) {
			$this->setInputError("This required input is missing: 'role_id'");
			return false;
		}
		
		return true;
	}
}
?>