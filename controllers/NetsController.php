<?php

require_once("RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."PNet_Nets.php");

// Nets Controller class
Class NetsController extends RESTController {
	
	public function __construct() {
		$this->obj = new Nets();
		$this->route();
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
		// (1) $input["name"] 
		if (!array_key_exists("name", $input)){
			$this->setInputError("This required input is missing: 'name' [string]");
			return false;
		}
		// (2) $input["owner_id"] is integer
		if (array_key_exists("owner_id", $input) and !is_int($input["owner_id"])){
			$this->setInputError("Uncorrect input: 'owner_id' [int]");
			return false;
		}
		
		return true;
	}
}
?>