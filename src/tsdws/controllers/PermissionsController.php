<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."Permissions.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."Users.php");

// Permissions Controller class
Class PermissionsController extends RESTController {
	
	private $role_type;

	public function __construct($role_type) {
		$this->role_type = $role_type;
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

			case 'PATCH':
				// no update provided!
				// each time a new permission for the same role_id is posted, all the previous permissions with same role_id will be tagged as removed
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
	// ****************** post - permission  **********************//
	// ====================================================================//
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (0) $input["role_id"] 
		if (!array_key_exists("role_id", $input) or !is_numeric($input["role_id"])){
			$this->setInputError("This required input is missing: 'role_id' [integer]");
			return false;
		}
		// (1) $input["settings"] is json
		if (!array_key_exists("settings", $input) or !$this->validate_json($input["settings"])){
			$this->setInputError("Error on decoding 'settings' JSON input");
			return false;
		}
		// (2) $input["active"] 
		if (array_key_exists("active", $input)){
			$input["active"] = (intval($input["active"]) === 1 or $input["active"] === true or $input["active"] === "true");
		} else {
			$input["active"] = true;
		}

		$this->setParams($input);
		
		return true;
	}

	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function get($jsonfields=array("settings")) {
	
		$input = $this->getParams();
		
		// merge user permissions with its role permissions
		if (
			$this->role_type == Permissions::MEMBER_TYPE and 
			array_key_exists("role_id", $input) and
			is_numeric($input["role_id"]) and
			array_key_exists("merge_with_role_permissions", $input) and
			(intval($input["merge_with_role_permissions"]) === 1 or $input["merge_with_role_permissions"] === true or $input["merge_with_role_permissions"] === "true")
		) {
			$UserObj = new Users($input["role_id"]);			
			$result = array("settings" => null);
			try {
				$result["settings"] = $UserObj->getPermissions();
				$this->setData($result);
			} catch (Exception $e) {
				$this->setStatusCode(404);
				$this->setError($e->getMessage());
			}
		} 
		// list specific user permissions only
		else {
			parent::get($jsonfields);
		}
		
	}

	// ====================================================================//
	// ****************** delete  ********************//
	// ====================================================================//
	public function check_input_delete() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();

		// (0) $input["role_id"] 
		if (array_key_exists("role_id", $input) and !is_numeric($input["role_id"])){
			$this->setInputError("Uncorrect input: 'role_id' [integer]");
			return false;
		}
		
		return true;
	}
}
?>