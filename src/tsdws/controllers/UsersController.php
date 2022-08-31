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

}
?>