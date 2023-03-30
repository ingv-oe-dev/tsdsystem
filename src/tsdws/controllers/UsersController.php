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

			case 'PATCH':
				$this->getInput();
				if (!$this->check_input_patch()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"admin"
				));
				$this->patch();
				break;

			default:
				# code...
				break;
		}

		$this->elaborateResponse();
	}

	public function patch() {
		
		$input = $this->getParams();
		$auth_data = $this->_get_auth_data();
		
		$result = $this->obj->update($input);
		
		if ($result["status"]) {
			unset($result["query"]);
			$this->setData($result);
			if(isset($result["rows"]) and $result["rows"] > 0) {
				$this->setStatusCode(202);
			} else {
				$this->setStatusCode(207);
			}
		} else {
			$this->setStatusCode(409);
			$this->setError($result);
		}
	}

}
?>