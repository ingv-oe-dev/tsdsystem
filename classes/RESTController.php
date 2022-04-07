<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."SimpleREST.php");

// Generic REST Controller class
Class RESTController extends SimpleREST {
	
	public $obj;

	// define scopes
    public $resources = array("owners","nets","sensortypes","sensors","sites","channels","timeseries");
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

	// ====================================================================//
	// *************************  AUTHORIZATION  **************************//
	// ====================================================================//
	protected function authorizedAction($auth_params=array()) {

		// Check for a valid token in the header authorization and set into class variable JWT_payload
		$this->_setJWT_payload();
		
		// CHECK IF IS A MAGIC TOKEN
        if (isset($this->JWT_payload) and array_key_exists("magic", $this->JWT_payload) and $this->JWT_payload["magic"]) 
            return $this->JWT_payload["data"];

        // Get authorization data
        $auth_data = $this->_get_auth_data();
        //var_dump($auth_data);

		// Check if present authorization data, exit otherwise
		if(!isset($auth_data)) {
			$this->setStatusCode(401);
			$this->setError("Authorization Not Found!");
			$this->elaborateResponse();
			exit();
		}
		
		// Check if 'rights' are into $auth_data
		if (!array_key_exists("rights", $auth_data) and array_key_exists("userId", $auth_data) and isset($auth_data["userId"])) {
			
			// load rights from users database by userId
			require_once ("Users.php");
			$UserObj = new Users($auth_data["userId"]);			
			$auth_data["rights"] = $UserObj->getPermissions();
		}
		
		// Check permissions
        try {
			$this->comparePermissions($auth_params, $auth_data); 
		} catch (Exception $e) {
			$this->setStatusCode(401);
			$this->setError($e->getMessage());
			$this->elaborateResponse();
			exit();
		}

    }

	/**
	 * Check authorization contained into $auth_data 
	 * by $auth_params["scope"] <resource>-<read|edit> 
	 * and/or $auth_params["resource_id"]
	 */
	public function comparePermissions($auth_params, $auth_data) {
		
		$errorMessagePrefix = "Unauthorized action - ";
		
		// check if exists the section related to the scope
		try {
			$scope = explode('-', $auth_params['scope']); // view scope

			$rights = $auth_data["rights"]["resources"][$scope[0]][$scope[1]];
			// echo "rights:";
			// var_dump($rights);
		} catch (Exception $e) {
			throw new Exception($errorMessagePrefix . $e->getMessage());
		}

		// Check if exists 'rights' section
		if (!isset($rights)) 
			throw new Exception($errorMessagePrefix . "No 'rights' section found into auth_data");

		// Check if enabled === true
		if (!array_key_exists("enabled", $rights) or !$rights["enabled"]) 
			throw new Exception($errorMessagePrefix . $auth_params['scope'] . " not enabled");

		// Check for ip address restrictions
		if (
			array_key_exists("ip", $rights) and 
			is_array($rights["ip"]) and 
			(count($rights["ip"]) > 0) and 
			!in_array($_SERVER["REMOTE_ADDR"], $rights["ip"])
		) throw new Exception($errorMessagePrefix . "IP restriction raised");

		// If here, return true if not exists a specific 'permissions' 
		if (!array_key_exists("permissions", $rights)) return true;
		
		// If here, return true if the resource with id = $auth_params["resource_id"] is into $rights["permissions"]["id"] array
		if (
			array_key_exists("resource_id", $auth_params) and 
			array_key_exists("id", $rights["permissions"]) and 
			is_array($rights["permissions"]["id"]) and 
			in_array($auth_params["resource_id"], $rights["permissions"]["id"])
		) return true;

		// Check if the resource with id = $auth_params["resource_id"] is into the not empty $rights["permissions"]["id"] array
		if (
			array_key_exists("resource_id", $auth_params) and 
			array_key_exists("id", $rights["permissions"]) and 
			is_array($rights["permissions"]["id"]) and 
			count($rights["permissions"]["id"]) > 0 and
			!in_array($auth_params["resource_id"], $rights["permissions"]["id"])
		) throw new Exception($errorMessagePrefix . "Not allowed for id = " . $auth_params["resource_id"]);

	}
}
?>