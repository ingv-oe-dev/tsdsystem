<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."SimpleREST.php");

// Generic REST Controller class
Class RESTController extends SimpleREST {
	
	public $obj;

	// define scopes
    public $resources = array("owners","nets","stations","sensortypes","sensors","sites","channels","timeseries", "digitizers", "digitizertypes");
    public $actions = array("read", "edit");
    public $scopes = array("admin","all");
	
	//CONSTRUCTOR
	public function __construct() {

        // initialize scopes
        $this->scopes = array_merge($this->scopes, $this->resources);
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

		$input = $this->getParams();
		$auth_data = $this->_get_auth_data();
		$input["create_user"] = isset($auth_data) ? $auth_data["userId"] : NULL;

		$result = $this->obj->insert($input);
		
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

	public function check_spatial_input() {

		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (1) $input["minlatitude"] 
		if (array_key_exists("minlatitude", $input) and !is_numeric($input["minlatitude"])){
			$this->setInputError("Uncorrect input: 'minlatitude' [numeric]. Your value: '" . $input["minlatitude"] . "'");
			return false;
		}
		// (2) $input["maxlatitude"] 
		if (array_key_exists("maxlatitude", $input) and !is_numeric($input["maxlatitude"])){
			$this->setInputError("Uncorrect input: 'maxlatitude' [numeric]. Your value: '" . $input["maxlatitude"] . "'");
			return false;
		}
		// (3) $input["minlongitude"] 
		if (array_key_exists("minlongitude", $input) and !is_numeric($input["minlongitude"])){
			$this->setInputError("Uncorrect input: 'minlongitude' [numeric]. Your value: '" . $input["minlongitude"] . "'");
			return false;
		}
		// (4) $input["maxlongitude"] 
		if (array_key_exists("maxlongitude", $input) and !is_numeric($input["maxlongitude"])){
			$this->setInputError("Uncorrect input: 'maxlongitude' [numeric]. Your value: '" . $input["maxlongitude"] . "'");
			return false;
		}
		// (5) $input["latitude"] 
		if (array_key_exists("latitude", $input) and !is_numeric($input["latitude"])){
			$this->setInputError("Uncorrect input: 'latitude' [numeric]. Your value: '" . $input["latitude"] . "'");
			return false;
		}
		// (6) $input["longitude"] 
		if (array_key_exists("longitude", $input) and !is_numeric($input["longitude"])){
			$this->setInputError("Uncorrect input: 'longitude' [numeric]. Your value: '" . $input["longitude"] . "'");
			return false;
		}
		// (7) $input["minradius"] 
		if (array_key_exists("minradius", $input) and !is_numeric($input["minradius"])){
			$this->setInputError("Uncorrect input: 'minradius' [numeric]. Your value: '" . $input["minradius"] . "'");
			return false;
		}
		// (8) $input["maxradius"] 
		if (array_key_exists("maxradius", $input) and !is_numeric($input["maxradius"])){
			$this->setInputError("Uncorrect input: 'maxradius' [numeric]. Your value: '" . $input["maxradius"] . "'");
			return false;
		}
		// (9) $input["minradiuskm"] 
		if (array_key_exists("minradiuskm", $input) and !is_numeric($input["minradiuskm"])){
			$this->setInputError("Uncorrect input: 'minradiuskm' [numeric]. Your value: '" . $input["minradiuskm"] . "'");
			return false;
		}
		// (10) $input["maxradiuskm"] 
		if (array_key_exists("maxradiuskm", $input) and !is_numeric($input["maxradiuskm"])){
			$this->setInputError("Uncorrect input: 'maxradiuskm' [numeric]. Your value: '" . $input["maxradiuskm"] . "'");
			return false;
		}
		return true;
	}
	// ====================================================================//
	// *************************      PATCH       *************************//
	// ====================================================================//
	
	public function patch() {

		$input = $this->getParams();
		$auth_data = $this->_get_auth_data();
		$input["update_user"] = isset($auth_data) ? $auth_data["userId"] : NULL;
		$input["update_time"] = "timezone('utc'::text, now())";

		$result = $this->obj->update($input);
		
		if ($result["status"]) {
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

	public function check_input_patch() {
		return true;
	}

	// ====================================================================//
	// *************************      DELETE      *************************//
	// ====================================================================//
	public function delete() {

		$input = $this->getParams();
		$auth_data = $this->_get_auth_data();
		$input["remove_user"] = isset($auth_data) ? $auth_data["userId"] : NULL;
		$input["remove_time"] = "timezone('utc'::text, now())";

		$result = $this->obj->delete($input);
		
		if ($result["status"]) {
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

	public function check_input_delete() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();

		// (0) $input["id"] 
		if (!array_key_exists("id", $input)) {
			$this->setInputError("This required input is missing: 'id'");
			return false;
		}
		
		return true;
	}

	// ====================================================================//
	// *************************  AUTHORIZATION  **************************//
	// ====================================================================//
	protected function authorizedAction($auth_params=array()) {
		//var_dump($auth_params)

		// Check for a valid token in the header authorization and set into class variable JWT_payload
		$this->_setJWT_payload();
		
		// CHECK IF IS AN ADMIM TOKEN
        if (
			isset($this->JWT_payload) and 
			array_key_exists("rights", $this->JWT_payload) and 
			array_key_exists("admin", $this->JWT_payload["rights"]) and 
			$this->JWT_payload["rights"]["admin"]
		) {
            return true;
		}

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
		$rights = null;

		// CHECK IF SUPER USER
		if ($auth_data["userId"] == getenv("ADMIN_ID")) {
			return true;
		}

		// CHECK IF ADMIN USER
		if (
			array_key_exists("rights", $auth_data) and 
			is_array($auth_data["rights"]) and
			array_key_exists("admin", $auth_data["rights"]) and 
			$auth_data["rights"]["admin"]
		) {
			return true; 
		} 

		// CHECK IF ADMIN ACTION (if here the user is not admin neither super user)
		if ($auth_params["scope"] == "admin") {
			throw new Exception("Unauthorized action - Administrator privileges required");
		}
		
		// check if exists the section related to the scope
		try {
			$scope = explode('-', $auth_params['scope']); // view scope
			
			if (
				count($scope)>1 and 
				array_key_exists($scope[0],$auth_data["rights"]["resources"]) and 
				is_array($auth_data["rights"]["resources"][$scope[0]]) and
				array_key_exists($scope[1],$auth_data["rights"]["resources"][$scope[0]])
			) {
				$rights = $auth_data["rights"]["resources"][$scope[0]][$scope[1]];
			}
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