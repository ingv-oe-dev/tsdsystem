<?php

require_once("..\classes\SimpleREST.php");
require_once("..\classes\TimeseriesValues.php");

// Timeseries class
Class TimeseriesValuesController extends SimpleREST {
	
	private $time_interval_regex = '/([0-9]+)\s((\bsecond[s]{0,1}\b)|(\bminute[s]{0,1}\b)|(\bhour[s]{0,1}\b)|(\bday[s]{0,1}\b)|(\bweek[s]{0,1}\b)|(\bmonth[s]{0,1}\b)|(\byear[s]{0,1}\b))/';
	private $aggregate_array = array("AVG","MEDIAN","COUNT","MAX","MIN","SUM");

	public function __construct() {
		$this->obj = new TimeseriesValues();
		$this->route();
	}
	
	public function route() {
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$checkToken = $this->checkJWTToken();
			if ($checkToken["status"]) {
				$this->readInput();
				$this->post();
			} else {
				$this->setStatusCode(401);
				$this->setError($checkToken["error"]);
			}
		}
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$this->getInput();
			$this->get();
		}
		$this->elaborateResponse();
	}
	
	public function getInput() {
		$this->setParams(array_key_exists("request", $_GET) ? json_decode($_GET["request"], true) : NULL);
	}

	// ====================================================================//
	// ******************* post - timeseries values **********************//
	// ====================================================================//

	public function post() {

		if ($this->check_input_post()) {

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
	}
	
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (1) $input["timeseries_id"]
		if (!array_key_exists("timeseries_id", $input)){
			$this->setInputError("This required input is missing: 'timeseries_id' [string]");
			return false;
		}
		// (2) $input["columns"] 
		if (!array_key_exists("columns", $input) || !is_array($input["columns"])){
			$this->setInputError("This required input is missing: 'columns' [array of string]");
			return false;
		}
		// (2.1) $input["columns"][$this->obj->getTimeColumnName()] 
		if (!in_array($this->obj->getTimeColumnName(), $input["columns"])){
			$this->setInputError("This required column is missing: '" . $this->$obj->getTimeColumnName() . "'. Your columns:" . implode(",", $input["columns"]));
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
	
	public function get() {
		
		if ($this->check_input_get()) {
			
			$result = $this->obj->select_values($this->getParams());
			if (isset($result) and $result["status"]) {
				$this->setData($result["data"]);
			} else {
				$this->setStatusCode(500);
				$this->setError($result);
			}
			
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
		
		// timeseries_id
		if(!array_key_exists("timeseries_id", $input)) {
			$this->setInputError("This required input is missing: 'timeseries_id' [string]");
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
					$input["columns"] = $this->obj->getColumnList($input["timeseries_id"]); // select all columns
				}
			} else {
				if (empty($input["columns"])) {
					$input["columns"] = $this->obj->getColumnList($input["timeseries_id"]); // select all columns
				}
			}
		} else {
			$input["columns"] = $this->obj->getColumnList($input["timeseries_id"]); // select all columns
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
		$column_list = $this->obj->getColumnList($input["timeseries_id"]);
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
			if (!in_array($input["columns"][$i]["name"], $column_list)) {
				$this->setInputError("The column with name '" . $input["columns"][$i]["name"] . "' does not exist. Available columns: [" . implode(", ", $column_list) . "]");
				return false;
			}
		}
		return true;
	}
}
?>