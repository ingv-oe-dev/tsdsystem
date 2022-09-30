<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."TimeseriesValues.php");

// Timeseries class
Class TimeseriesValuesController extends RESTController {
	
	private $time_interval_regex = '/([0-9]+)\s((\bsecond[s]{0,1}\b)|(\bminute[s]{0,1}\b)|(\bhour[s]{0,1}\b)|(\bday[s]{0,1}\b)|(\bweek[s]{0,1}\b)|(\bmonth[s]{0,1}\b)|(\byear[s]{0,1}\b))/';
	private $aggregate_array = array("AVG","MEDIAN","COUNT","MAX","MIN","SUM");
	public $default_permission = array(
		"last_days" => true,
		"number_of_days" => 1
	);

	public function __construct() {
		
		// instantiate the object model
		$this->obj = new TimeseriesValues();
		
		// handle the request
		$this->route();
	}
	
	/**
	 * !! OVERRIDE RESTController function getInput !!
	 */
	public function route() {

		switch ($_SERVER['REQUEST_METHOD']) {
			// POST method
			case 'POST':				
				// read input
				$this->readInput();

				// check if correct input
				if (!$this->check_input_post()) break;
					
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"timeseries-edit",
					"resource_id" => $this->getParams()["id"]
				));

				// post action
				$this->post();

				break;
			
			// GET method
			case 'GET':
				
				// read input
				$this->getInput();

				// check if correct input
				if (!$this->check_input_get()) break;

				// get info about timeseries
				$ts_info = $this->obj->getInfo($this->getParams()["id"]);
				
				// if not public
				if (
					isset($ts_info) and 
					is_array($ts_info)
				) { 
					if(
						!array_key_exists("public", $ts_info) or
						!isset($ts_info["public"]) or 
						!$ts_info["public"]
					) {
						// then check if authorized action
						$this->authorizedAction(array(
							"scope"=>"timeseries-read",
							"resource_id" => $this->getParams()["id"]
						));
					}
				
					// append info to response
					$ts_info["metadata"] = isset($ts_info["metadata"]) ? json_decode($ts_info["metadata"]) : NULL;
					$this->response["additional_info"] = $ts_info;
				}

				// get action
				$this->get();

				break;
			
			// default action
			default:
				break;		
		}
		// elaborate response
		$this->elaborateResponse();
	}
	
	/**
	 * !! OVERRIDE SimpleREST function getInput !!
	 */
	public function getInput() {
		$this->setParams(array_key_exists("request", $_GET) ? json_decode($_GET["request"], true) : NULL);
	}

	/**
	 * !! OVERRIDE SimpleREST function comparePermissions !!
	 * Check authorization contained into $auth_data 
	 * by $auth_params["scope"] <resource>-<read|edit> 
	 * and/or $auth_params["resource_id"]
	 */
	public function comparePermissions($auth_params, $auth_data) {
		
		$errorMessagePrefix = "Unauthorized action - ";

		// CHECK IF SUPER USER
		if ($auth_data["userId"] == getenv("ADMIN_ID")) {
			return true;
		}

		// CHECK IF SUPER USER ACTION
		if ($this->compareAdminPermissions($auth_params, $auth_data)) return true;
		
		// check if exists the section related to the scope
		$scope = array();
		try {
			$scope = explode('-', $auth_params['scope']); // view scope
		} catch (Exception $e) {
			throw new Exception($errorMessagePrefix . $e->getMessage());
		}

		if (count($scope)>1) {
			// Differentiate check between read and edit for timeseries values
			switch ($scope[1]) {

				// EDIT criterion
				case "edit":
					$this->_comparePermissionsEdit($auth_params, $auth_data);
					break;

				// READ criterion
				case "read":
					$this->_comparePermissionsRead($auth_params, $auth_data);
					break;

				// not enabled other criteria
				default:
					throw new Exception($errorMessagePrefix . " Not enabled scope: " . $auth_params['scope']);
					break;
			}
		}
	}

	/**
	 * Sub function of comparePermissions for EDIT scope
	 */
	private function _comparePermissionsEdit($auth_params, $auth_data) {
		
		$rights = null;
		$errorMessagePrefix = "Unauthorized action - ";

		// check if exists the section related to the scope
		try {
			$scope = explode('-', $auth_params['scope']); // view scope
			if (count($scope)>1) {
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
		
		// If here, return true if the timeseries with id = $auth_params["resource_id"] is into $rights["permissions"]["id"] array
		if (
			array_key_exists("resource_id", $auth_params) and 
			array_key_exists("id", $rights["permissions"]) and 
			is_array($rights["permissions"]["id"]) and 
			in_array($auth_params["resource_id"], $rights["permissions"]["id"])
		) return true;

		// Check if the timeseries with id = $auth_params["resource_id"] is into the not empty $rights["permissions"]["id"] array
		if (
			array_key_exists("resource_id", $auth_params) and 
			array_key_exists("id", $rights["permissions"]) and 
			is_array($rights["permissions"]["id"]) and 
			count($rights["permissions"]["id"]) > 0 and
			!in_array($auth_params["resource_id"], $rights["permissions"]["id"])
		) throw new Exception($errorMessagePrefix . "Not allowed for id = " . $auth_params["resource_id"]);

		// Launch db query to retrieve all timeseries (with id = $auth_params["resource_id"]) dependencies
		$dependencies = $this->obj->getDependencies($auth_params["resource_id"]);
		// echo "dependencies:";
		// var_dump($dependencies);

		// Check if empty or null dependencies
		if (!isset($dependencies)) 
			throw new Exception($errorMessagePrefix . " Error on retrieving resource dependencies");

		// Check if empty or null $rights["permissions"]["net_id"] array
		if (
			!array_key_exists("net_id", $rights["permissions"])
			or
			(is_array($rights["permissions"]["net_id"]) and 
			count($rights["permissions"]["net_id"]) == 0)
			or
			!isset($rights["permissions"]["net_id"])
			or 
			(count(array_intersect($dependencies["net_id"], $rights["permissions"]["net_id"])) == 0)
		) throw new Exception($errorMessagePrefix . " Resource dependencies (net) not satisfied");

	}

	/**
	 * Sub function of comparePermissions for READ scope
	 */ 
	private function _comparePermissionsRead($auth_params, $auth_data) {
		
		$rights = null;
		$errorMessagePrefix = "Unauthorized action - ";

		// check if exists the section related to the scope
		try {
			$scope = explode('-', $auth_params['scope']); // view scope
			if (count($scope)>1) {
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
		
		// go to select the permission
		$selected_permission = $this->default_permission;

		// view if there is an 'all' section
		if (array_key_exists("all", $rights["permissions"])) 
			$selected_permission = $rights["permissions"]["all"]; 

		// If here, return true if the timeseries with id = $auth_params["resource_id"] is into $rights["permissions"]["id"] section
		if (
			array_key_exists("resource_id", $auth_params) and 
			array_key_exists("id", $rights["permissions"]) and 
			is_array($rights["permissions"]["id"]) and 
			in_array($auth_params["resource_id"], array_keys($rights["permissions"]["id"]))
		) {
			$selected_permission = $rights["permissions"]["id"][$auth_params["resource_id"]];
		} 
		else {
			// Launch db query to retrieve all timeseries (with id = $auth_params["resource_id"]) dependencies
			$dependencies = $this->obj->getDependencies($auth_params["resource_id"]);
			// echo "dependencies:";
			// var_dump($dependencies);

			// Check if empty or null dependencies
			if (!isset($dependencies)) 
				throw new Exception($errorMessagePrefix . " Error on retrieving resource dependencies");

			// Check if empty or null $rights["permissions"]["net_id"] array/section
			if (
				array_key_exists("net_id", $rights["permissions"]) and
				is_array($rights["permissions"]["net_id"]) and 
				count($rights["permissions"]["net_id"]) > 0
			) {
				$intersect = array_intersect($dependencies["net_id"], array_keys($rights["permissions"]["net_id"]));
				if (count($intersect)>0)
					$selected_permission = $rights["permissions"]["net_id"][$intersect[0]];
			}	
		}

		// check time interval rights
		$this->checkTimeIntervalRights($selected_permission);
	}

	/**
	 * Match $permission time with input time request
	 * Default permissions structure: {
	 * 		"last_days": true, 
	 * 		"end_period": null, 
	 * 		"start_period": null, 
	 * 		"number_of_days": 1
	 * }
	 */
	public function checkTimeIntervalRights($permission) {

		$input = $this->getParams();

		// defalt allowed starttime and endtime (1 day)
		$allowed_endtime = new DateTime("now", new DateTimeZone('UTC'));
		$allowed_starttime = new DateTime("now", new DateTimeZone('UTC'));
		$allowed_starttime->sub(new DateInterval('P'.$this->default_permission["number_of_days"].'D'));

		// calculate allowed starttime and endtime
		if (
			array_key_exists("last_days", $permission) and 
			$permission["last_days"] === true and 
			array_key_exists("number_of_days", $permission) and 
			is_int($permission["number_of_days"]) and 
			$permission["number_of_days"] >= 0
		) {
			$allowed_endtime = new DateTime("now", new DateTimeZone('UTC'));
			$allowed_starttime = new DateTime("now", new DateTimeZone('UTC'));
			$allowed_starttime->sub(new DateInterval('P'.intval($permission["number_of_days"]).'D'));
		}

		// force allowed starttime if start_period is set
		if (array_key_exists("start_period", $permission) and isset($permission["start_period"]) and $this->verifyDate($permission["start_period"])) {
			$allowed_starttime = DateTime::createFromFormat($this->DATE_ISO_FORMAT, $permission["start_period"], new DateTimeZone('UTC'));
		}

		// force allowed endtime if start_period is set
		if (array_key_exists("end_period", $permission) and isset($permission["end_period"]) and $this->verifyDate($permission["end_period"])) {
			$allowed_endtime = DateTime::createFromFormat($this->DATE_ISO_FORMAT, $permission["end_period"], new DateTimeZone('UTC'));
		}
		
		//check user rights on period
		$starttime = DateTime::createFromFormat($this->DATE_ISO_FORMAT, $input["starttime"], new DateTimeZone('UTC'));
		$endtime = DateTime::createFromFormat($this->DATE_ISO_FORMAT, $input["endtime"], new DateTimeZone('UTC'));
		
		if ($allowed_starttime > $starttime) 
			throw new Exception("Requested period unauthorized - Not before " . $allowed_starttime->format($this->DATE_ISO_FORMAT));

		if ($allowed_endtime < $endtime)
			throw new Exception("Requested period unauthorized - Not after " . $allowed_endtime->format($this->DATE_ISO_FORMAT));
		
	}

	// ====================================================================//
	// ******************* post - timeseries values **********************//
	// ====================================================================//

	public function post() {

		$result = $this->obj->insert_values($this->getParams());
		
		// evito di aggiungere l'input inviato nella risposta (in questi casi potrebbe essere molto grande)
		$this->setParams(null); 
		
		if ($result["status"]) {
			$this->setData($result);
			$this->setStatusCode(201);
			if (array_key_exists("updatedTimeseriesTable", $result) and !$result["updatedTimeseriesTable"]) {
				// Valori inseriti ma non è stata aggiornata la tabella delle serie temporali (last_time)
				$this->setStatusCode(202);
			}
			if (is_array($this->response["data"]) and array_key_exists("rows", $this->response["data"]) and ($this->response["data"]["rows"] == 0)) {
				$this->setStatusCode(207);
			}
		} else {
			if (array_key_exists("make_sql_error", $result) and $result["make_sql_error"]) {
				$this->setError($result["error"]);
				$this->setStatusCode(400);
			} else {
				$this->setError($result["error"]);
				$this->setStatusCode(500);
			}
		}
	}
	
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (1) $input["id"]
		if (!array_key_exists("id", $input)){
			$this->setInputError("This required input is missing: 'id' [string]");
			return false;
		}
		// (2) $input["columns"] 
		if (!array_key_exists("columns", $input) || !is_array($input["columns"])){
			$this->setInputError("This required input is missing: 'columns' [array of string]");
			return false;
		}
		// (2.1) $input["columns"][$this->obj->getTimeColumnName()] 
		if (!in_array($this->obj->getTimeColumnName(), $input["columns"])){
			$this->setInputError("This required column is missing: '" . $this->obj->getTimeColumnName() . "'. Your columns:" . implode(",", $input["columns"]));
			return false;
		}
		// (3) $input["data"] 
		if (!array_key_exists("data", $input) || !is_array($input["data"])){
			$this->setInputError("This required input is missing: 'data' [array of array]");
			return false;
		}
		
		return true;
	}
	
	// ====================================================================//
	// ******************* get - timeseries values ***********************//
	// ====================================================================//
	/**
	 * OVERRIDE RESTController 'get' function
	 */
	public function get($jsonfields=null) {
		
		$result = $this->obj->select_values($this->getParams());
		if (isset($result) and $result["status"]) {
			$this->setData($result["data"]);
		} else {
			$this->setStatusCode(500);
			$this->setError($result);
		}
	}
	
	public function check_input_get() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// transponse
		if(!array_key_exists("transpose", $input)) {
			$input["transpose"] = false;
		} else {
			$input["transpose"] = ($input["transpose"] === true);
		}
		
		// id
		if(!array_key_exists("id", $input)) {
			$this->setInputError("This required input is missing: 'id' [string]");
			return false;
		}
		
		// time_bucket
		if(array_key_exists("time_bucket", $input) and isset($input["time_bucket"])) {
			if (!preg_match($this->time_interval_regex, $input["time_bucket"])) {
				$this->setInputError("This input is incorrect: 'time_bucket' [string] <format: [0-9]+ second[s]|minute[s]|hour[s]|day[s]|week[s]|month[s]|year[s]>. Your value = " . strval($input["time_bucket"]));
				return false;
			} else {
				// aggregate function criteria
				if (array_key_exists("aggregate", $input)){
					if (!in_array(strtoupper($input["aggregate"]), $this->aggregate_array)) {
						$this->setInputError("This input is incorrect: 'aggregate'[string], must be a value in the following list: " . implode(", ", $this->aggregate_array) . ". Your value = " . strval($input["aggregate"]));
						return false;
					}
				} else {
					$input["aggregate"] = "AVG";
				}
			}
		}
		
		// starttime
		if(array_key_exists("starttime", $input)) {
			if (!$this->verifyDate($input["starttime"])) {
				$this->setInputError("This input is incorrect: 'starttime' [string] <format: " . $this->DATE_ISO_FORMAT . ">. Your value = " . strval($input["starttime"]));
				return false;
			}
		} else {
			$starttime = new DateTime('now', new DateTimeZone('UTC'));
			if(array_key_exists("endtime", $input) and $this->verifyDate($input["endtime"])) {
				$starttime = DateTime::createFromFormat($this->DATE_ISO_FORMAT, $input["endtime"], new DateTimeZone('UTC'));
			}
			$starttime->sub(new DateInterval('P1D'));
			$input["starttime"] = $starttime->format('Y-m-d H:i:s');
		}
		
		// endtime
		if(array_key_exists("endtime", $input)) {
			if (!$this->verifyDate($input["endtime"])) {
				$this->setInputError("This input is incorrect: 'endtime' [string] <format: " . $this->DATE_ISO_FORMAT . ">. Your value = " . strval($input["endtime"]));
				return false;
			}
		} else {
			$endtime = new DateTime('now', new DateTimeZone('UTC'));
			if(array_key_exists("starttime", $input) and $this->verifyDate($input["starttime"])) {
				$endtime = DateTime::createFromFormat($this->DATE_ISO_FORMAT, $input["starttime"], new DateTimeZone('UTC'));
				$endtime->add(new DateInterval('P1D'));
			}
			$input["endtime"] = $endtime->format('Y-m-d H:i:s');
		}

		// columns
		if (array_key_exists("columns", $input)){
			if (!is_array($input["columns"])) {
				if($input["columns"] !== "*") {
					$this->setInputError("This input is incorrect: 'columns'[array]. Your value = " . strval($input["columns"]));
					return false;
				} else {
					$input["columns"] = $this->obj->getColumnList($input["id"]); // select all columns
				}
			} else {
				if (empty($input["columns"])) {
					$input["columns"] = $this->obj->getColumnList($input["id"]); // select all columns
				}
			}
		} else {
			$input["columns"] = $this->obj->getColumnList($input["id"]); // select all columns
		}
		// check settings into each column 
		if (!$this->checkColumnSettings($input)) return false;

		// timestamp
		if(array_key_exists("timeformat", $input) and strtoupper($input["timeformat"]) != "UNIX") {
			$this->setInputError("This input is incorrect: 'timeformat' [string]. Default 'ISO 8601' format <YYYY-MM-DD hh:mm:ss>. Only alternative value: 'unix'. Your value = " . strval($input["columns"]));
			return false;
		}

		$this->setParams($input);
		
		return true;
	}


	public function checkColumnSettings(&$input) {
		$column_list = $this->obj->getColumnList($input["id"]);
		$paramsToCheck = array(
			"aggregate",
			"minthreshold",
			"maxthreshold",
			"gain",
			"offset"
		);
		for($i=0; $i<count($input["columns"]); $i++) {
			if (is_array($input["columns"][$i])) {
				if (!array_key_exists("name", $input["columns"][$i])) {
					$this->setInputError("The name of column $i is undefined");
					return false;
				}
				foreach($paramsToCheck as $paramName) {
					if(array_key_exists($paramName, $input["columns"][$i]) and isset($input["columns"][$i][$paramName])) {
						if ($paramName == "aggregate") {
							if(!in_array(strtoupper($input["columns"][$i][$paramName]), $this->aggregate_array)) {
								$this->setInputError("This input is incorrect for column with name '" . $input["columns"][$i]["name"] . "': 'aggregate' [string], must be a value in the following list: " . implode(", ", $this->aggregate_array) . ". Your value = " . strval($input["columns"][$i][$paramName]));
								return false;
							}
						} 
						else if (!is_numeric($input["columns"][$i][$paramName])) {
							$this->setInputError("This input is incorrect for column with name '" . $input["columns"][$i]["name"] . "': '$paramName' [numeric]. Your value = " . strval($input["columns"][$i][$paramName]));
							return false;
						}
					}
				}
			} else {
				// if not array the ith column, make it an array with 'name' key = value of ith column
				$input["columns"][$i] = array(
					"name" => strval($input["columns"][$i])
				);
			}
			// final check if columns name are in table
			if (isset($column_list)) {
				if (!in_array($input["columns"][$i]["name"], $column_list)) {
					$this->setInputError("The column with name '" . $input["columns"][$i]["name"] . "' does not exist. Available columns: [" . implode(", ", $column_list) . "]");
					return false;
				}
			} else {
				$this->setInputError("Unable to retrieve columns name for timeseries with id = '" . $input["id"] . "'.");
				return false;
			}
		}
		return true;
	}
}
?>