<?php

require_once("RESTController.php");
require_once("..\classes\Nets.php");

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
		
		// (2) $input["name"] 
		if (!array_key_exists("name", $input)){
			$this->setInputError("This required input is missing: 'name' [string]");
			return false;
		}
		
		return true;
	}
}
?>