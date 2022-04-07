<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."SimpleREST.php");

// Generic REST Controller class
Class RESTController extends SimpleREST {
	
	public $obj;

	// define scopes
    public $resources = array("owners","nets","sensortypes","sensors","channels","timeseries");
    public $actions = array("read", "edit");
    public $scopes;
	
	//CONSTRUCTOR
	public function __construct() {

        // initialize scopes
        $this->scopes = array_merge(array("all"), $this->resources);
        foreach($this->resources as $resource) {
            foreach($this->actions as $action) {
                array_push($this->scopes, "$resource-$action");
            }
        }
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				if (!$this->check_input_post()) break;
				$this->post();
				break;

			case 'GET':
				$this->getInput();
				if (!$this->check_input_get()) break;
				$this->get();
				break;

			case 'PATCH':
				$this->readInput();
				if (!$this->check_input_patch()) break;
				$this->patch();
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
	// *************************      POST       **************************//
	// ====================================================================//
	
	public function post() {

		$result = $this->obj->insert($this->getParams());
		
		if ($result["status"]) {
			$this->setData($result);
			if (isset($result["id"])) {
				if(isset($result["rows"]) and $result["rows"] > 0) {
					$this->setStatusCode(201);
				} else {
					$this->setStatusCode(207);
				}
			}
		} else {
			$this->setStatusCode(409);
			$this->setError($result);
		}
	}

	public function check_input_post() {
		return true;
	}
	
	// ====================================================================//
	// *************************      GET       ***************************//
	// ====================================================================//
	
	public function get($jsonfields=array()) {
	
		$result = $this->obj->getList($this->getParams());
	
		if ($result["status"]) {
			for($i=0; $i<count($result["data"]); $i++) {
				foreach($jsonfields as $fieldname) {
					$result["data"][$i][$fieldname] = isset($result["data"][$i][$fieldname]) ? json_decode($result["data"][$i][$fieldname]) : NULL;
				}
			}
			$this->setData($result["data"]);
		} else {
			$this->setStatusCode(404);
			$this->setError($result);
		}
	}

	public function check_input_get() {
		return true;
	}
	// ====================================================================//
	// *************************      PATCH       *************************//
	// ====================================================================//
	
	public function patch() {

		$result = $this->obj->update($this->getParams());
		
		if ($result["status"]) {
			$this->setData($result);
			if(isset($result["rows"]) and $result["rows"] > 0) {
				$this->setStatusCode(202);
			} else {
				$this->setStatusCode(207);
			}
		} else {
			if ($result["rows"] == 0) {
				$this->setStatusCode(404);
			} else {
				$this->setStatusCode(409);
			}
			$this->setError($result);
		}
	}

	public function check_input_patch() {
		return true;
	}
}
?>