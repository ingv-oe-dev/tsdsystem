<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."Users.php");

// Users Controller class
Class UsersController extends RESTController {
	
	public function __construct() {
		$this->obj = new Users();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {

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
	// ****************** PATCH - user  **********************//
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
        // (1) $input["role_id"] 
		if (!array_key_exists("role_id", $input) or !is_int($input["role_id"])){
			$this->setInputError("This required input is missing: 'role_id' [integer]");
			return false;
		}
		// (2) $input["priority"] 
		if (array_key_exists("priority", $input)){
			if (!is_int($input["priority"])) {
				$this->setInputError("Uncorrect input: 'priority' [integer]");
				return false;
			}
		} else {
			$input["priority"] = 0;
		}

		$this->setParams($input);
		
		return true;
	}
}
?>