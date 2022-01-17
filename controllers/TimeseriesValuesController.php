<?php

require_once("..\classes\SimpleREST.php");
require_once("..\classes\TimeseriesValues.php");

// Timeseries class
Class TimeseriesValuesController extends SimpleREST {
	
	private $ts;
	
	public function __construct() {
		$this->ts = new TimeseriesValues();
		$this->readInput();
		$this->route();
	}
	
	public function route() {
		
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$checkToken = $this->checkJWTToken();
			if ($checkToken["status"]) {
				$this->post();
			} else {
				$this->setStatusCode(401);
				$this->setError($checkToken["error"]);
			}
		}
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$this->get();
		}
		$this->elaborateResponse();
	}
	
	// ====================================================================//
	// ******************* post - timeseries values **********************//
	// ====================================================================//

	public function post() {

		if ($this->check_input_post()) {

			$result = $this->ts->insert_values($this->getParams());
		
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
		
		// (1) $input["guid"]
		if (!array_key_exists("guid", $input)){
			$this->setInputError("This required input is missing: 'guid' [string]");
			return false;
		}
		// (2) $input["columns"] 
		if (!array_key_exists("columns", $input) || !is_array($input["columns"])){
			$this->setInputError("This required input is missing: 'columns' [array of string]");
			return false;
		}
		// (2.1) $input["columns"][$this->ts->getTimeColumnName()] 
		if (!in_array($this->ts->getTimeColumnName(), $input["columns"])){
			$this->setInputError("This required column is missing: '" . $this->$ts->getTimeColumnName() . "'. Your columns:" . implode(",", $input["columns"]));
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
			
			$result = $this->ts->select_values($this->getParams());
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
		
		// guid
		if(!array_key_exists("guid", $input)) {
			$this->setInputError("This required input is missing: 'guid' [string]");
			return false;
		}
		
		// columns
		if (array_key_exists("columns", $input) and (!is_array($input["columns"]) || empty($input["columns"]))){
			$this->setInputError("This input is incorrect: 'columns'[array]. Your value = " . strval($input["columns"]));
			return false;
		}
		
		// starttime
		if(array_key_exists("starttime", $input) and !$this->verifyDate($input["starttime"])) {
			$this->setInputError("This input is incorrect: 'starttime' [string] <format: " . $this->DATE_ISO_FORMAT . ">. Your value = " . strval($input["starttime"]));
			return false;
		}
		
		// endtime
		if(array_key_exists("endtime", $input) and !$this->verifyDate($input["endtime"])) {
			$this->setInputError("This input is incorrect: 'endtime' [string] <format: " . $this->DATE_ISO_FORMAT . ">. Your value = " . strval($input["endtime"]));
			return false;
		}
		/*
		// groupbyseconds
		if(isset($_GET["groupbyseconds"])) {
			if (is_numeric($_GET["groupbyseconds"])) {
				$this->responseData["params"]["groupbyseconds"] = $_GET["groupbyseconds"];
			} else {
				$this->responseData["error"]["groupbyseconds"] = "must be a integer value from 60 (your value=" . $_GET["groupbyseconds"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["groupbyseconds"] = 600;
		}
		
		// aggregatefunctioncriteria
		if(isset($_GET["aggregatefunctioncriteria"])) {
			if (in_array($_GET["aggregatefunctioncriteria"], $this->aggregate_array)) {
				$this->responseData["params"]["aggregatefunctioncriteria"] = $_GET["aggregatefunctioncriteria"];
			} else {
				$this->responseData["error"]["aggregatefunctioncriteria"] = "must be a value in the following list: " . implode(", ", $this->aggregate_array) . " (your value=" . $_GET["aggregatefunctioncriteria"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["aggregatefunctioncriteria"] = $defaultChartOptions["defaultChartOptions"][0]["criteria"];
		}
		
		// minthreshold
		if(isset($_GET["minthreshold"]) && ($_GET["minthreshold"] != "")) {
			if (is_numeric($_GET["minthreshold"])) {
				$this->responseData["params"]["minthreshold"] = $_GET["minthreshold"];
			} else {
				$this->responseData["error"]["minthreshold"] = "must be a number (your value=" . $_GET["minthreshold"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["minthreshold"] = $defaultChartOptions["defaultChartOptions"][0]["minY"];
		}
		
		// maxthreshold
		if(isset($_GET["maxthreshold"]) && ($_GET["maxthreshold"] != "")) {
			if (is_numeric($_GET["maxthreshold"])) {
				$this->responseData["params"]["maxthreshold"] = $_GET["maxthreshold"];
			} else {
				$this->responseData["error"]["maxthreshold"] = "must be a number (your value=" . $_GET["maxthreshold"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["maxthreshold"] = $defaultChartOptions["defaultChartOptions"][0]["maxY"];
		}
		
		// gainvalue
		if(isset($_GET["gainvalue"])) {
			if (is_numeric($_GET["gainvalue"])) {
				$this->responseData["params"]["gainvalue"] = $_GET["gainvalue"];
			} else {
				$this->responseData["error"]["gainvalue"] = "must be a number (your value=" . $_GET["gainvalue"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["gainvalue"] = $defaultChartOptions["defaultChartOptions"][0]["gain"];
		}
		
		// offsetvalue
		if(isset($_GET["offsetvalue"])) {
			if (is_numeric($_GET["offsetvalue"])) {
				$this->responseData["params"]["offsetvalue"] = $_GET["offsetvalue"];
			} else {
				$this->responseData["error"]["offsetvalue"] = "must be a number (your value=" . $_GET["offset"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["offsetvalue"] = $defaultChartOptions["defaultChartOptions"][0]["offsetvalue"];
		}
		
		// returndatetype
		if(isset($_GET["returndatetype"])) {
			if (in_array($_GET["returndatetype"], $this->datetype_array)) {
				$this->responseData["params"]["returndatetype"] = $_GET["returndatetype"];
			} else {
				$this->responseData["error"]["returndatetype"] = "must be a value in the following list: " . implode(", ", $this->datetype_array) . " (your value=" . $_GET["returndatetype"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["returndatetype"] = '';
		}
		
		// band
		if(isset($_GET["band"])) {
			if (is_numeric($_GET["band"]) || $_GET["band"] == "null") {
				$this->responseData["params"]["band"] = $_GET["band"];
			} else {
				$this->responseData["error"]["band"] = "must be a integer value or 'null' (your value=" . $_GET["band"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["band"] = "null";
		}
		
		// raw data
		if(isset($_GET["rawdata"])) {
			//var_dump($user->user["groups"]);
			if ($_GET["rawdata"] == "true" || $_GET["rawdata"] == "false") {
				$this->responseData["params"]["rawdata"] = $_GET["rawdata"];
				
				if($_GET["rawdata"] == "true") {
					if ($user->allowActionToGroups(array("Master", "DownloadRawData"))) {
						// continue
					} else {
						$this->responseData["error"]["rawdata"] = "You have not rights to download raw data";
						$this->responseData["statusCode"] = 400;
					}
				} else {
					// continue
				}
				
			} else {
				$this->responseData["error"]["rawdata"] = "must be a boolean value [true|false] (your value=" . $_GET["rawdata"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["rawdata"] = "false";
		}
		
		// allowThreshold
		if(isset($_GET["allowThreshold"])) {
			if ($_GET["allowThreshold"] == "true" || $_GET["allowThreshold"] == "false") {
				$this->responseData["params"]["allowThreshold"] = $_GET["allowThreshold"];
			} else {
				$this->responseData["error"]["allowThreshold"] = "must be a boolean value [true|false] (your value=" . $_GET["allowThreshold"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["allowThreshold"] = "false";
		}
		
		// allowLines
		if(isset($_GET["allowLines"])) {
			if ($_GET["allowLines"] == "true" || $_GET["allowLines"] == "false") {
				$this->responseData["params"]["allowLines"] = $_GET["allowLines"];
			} else {
				$this->responseData["error"]["allowLines"] = "must be a boolean value [true|false] (your value=" . $_GET["allowLines"] . ")";
				$this->responseData["statusCode"] = 400;
			}
		} else {
			$this->responseData["params"]["allowLines"] = "false";
		}
		
		$this->responseData["otherParams"] = $_GET;
		*/
		
		$this->setParams($input);
		
		return true;
	}
}
?>