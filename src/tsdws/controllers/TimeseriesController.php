<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."Timeseries.php");

// Timeseries class
Class TimeseriesController extends RESTController {
	
	public function __construct() {
		
		// instantiate the object model
		$this->obj = new Timeseries();
		
		// handle the request
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'POST':
				$this->readInput();
				if (!$this->check_input_post()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"timeseries-edit"
				));
				$this->post();
				break;

			case 'GET':
				$this->getInput();
				if (!$this->check_input_get()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"timeseries-read"
				));
				$this->get();
				break;

			case 'PATCH':
				$this->readInput();
				if (!$this->check_input_patch()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"timeseries-edit",
					"resource_id" => $this->getParams()["id"]
				));
				$this->patch();
				break;

			case 'DELETE':
				/* DO NOT IMPLEMENT IF UNNECESSARY!
				* Update of 'remove_time' field for a timeseries records have to be followed by
				* a schema+table(s - partitions) deleting, which can be dangerous operations.
				* If we allow only the update of the remove_time for a record of timeseries table,
				* the database will refuse all the following POST requests for timeseries having
				* the same name of deleted ones, whose names can be not visible to normal users.
				*/
				break;

			default:
				# code...
				break;
		}

		$this->elaborateResponse();
	}
	
	// ====================================================================//
	// ****************** post - timeseries instance **********************//
	// ====================================================================//
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (1) $input["schema"]
		if (!array_key_exists("schema", $input)){
			$this->setInputError("This required input is missing: 'schema' [string]");
			return false;
		}
		// (2) $input["name"] 
		if (!array_key_exists("name", $input) || empty($input["name"])){
			$this->setInputError("This required input is missing: 'name' [string]");
			return false;
		}
		// (3) $input["sampling"]
		if (array_key_exists("sampling", $input)) {
			if (!is_int($input["sampling"]) || $input["sampling"] < 0) {
				$this->setInputError("Uncorrect input: 'sampling'[integer > 0] <in seconds>");
				return false;
			}
		} else {
			// set default sampling value to 60 seconds
			$input["sampling"] = 60;
		}
		// (3) $input["columns"] 
		if (array_key_exists("columns", $input)){
			if(!is_array($input["columns"])) {
				$this->setInputError("Uncorrect input: 'columns'[array]");
				return false;
			}
		} else {
			// default columns
			$input["columns"] = array(array("name"=>"value", "type"=>"double precision"));
		}
		// (4) $input["metadata"] is json
		if (array_key_exists("metadata", $input)) {
			if (!$this->validate_json($input["metadata"])){
				$this->setInputError("Error on decoding 'metadata' JSON input");
				return false;
			} 
			$input["metadata"]["columns"] = $input["columns"];
		} else {
			$input["metadata"] = array("columns" => $input["columns"]);
		}
		
		// check mapping values
		if (array_key_exists("mapping", $input) and !$this->check_mapping_values($input["mapping"])) {
			return false;
		}

		$this->setParams($input);

		return true;
	}
	
	public function check_mapping_values($input) {
		if (!$this->validate_json($input)){
			$this->setInputError("Error on decoding 'mapping' JSON input");
			return false;
		}
		if (array_key_exists("channel_id", $input)){
			if(is_array($input["channel_id"])) {
				foreach($input["channel_id"] as $index => $id) {
					if (!is_int($id) or $id < 1) {
						$this->setInputError("Error on index $index into 'mapping->channel_id' [array of int]: NOT A POSITIVE INTEGER VALUE. Your value = " . strval($id));
						return false;
					}
				}
			} else {
				$this->setInputError("Error on input 'mapping->channel_id' [array of int]");
				return false;
			}
		}
		return true;
	}

	// ====================================================================//
	// ****************** get - timeseries instance(s) ********************//
	// ====================================================================//
	public function get($jsonfields=array("metadata")) {
	
		$params = $this->getParams();

		$result = $this->obj->getList($params);

		if ($result["status"]) {
			for($i=0; $i<count($result["data"]); $i++) {
				foreach($jsonfields as $fieldname) {
					$result["data"][$i][$fieldname] = isset($result["data"][$i][$fieldname]) ? json_decode($result["data"][$i][$fieldname]) : NULL;
					// add columns list on response if by id
					if (array_key_exists("listCol", $params) and $params["listCol"]) {
						$result["data"][$i]["columns"] = $this->obj->getColumnList($result["data"][$i]["id"], $addInfo=false);
					}
					// add columns list + types on response if by id
					if (array_key_exists("showColDefs", $params) and $params["showColDefs"]) {
						$result["data"][$i]["columns"] = $this->obj->getColumnList($result["data"][$i]["id"], $addInfo=true);
					}
					// add channel list on response if by id
					if (array_key_exists("showMapping", $params) and $params["showMapping"]) {
						$result["data"][$i]["mapping"] = array(
							"channel_id" => $this->obj->getIDChannelList($result["data"][$i]["id"])
						);
					}
				}
			}
			$this->setData($result["data"]);
		} else {
			$this->setStatusCode(404);
			$this->setError($result);
		}
	}
	
	// ====================================================================//
	// ****************** patch - timeseries instance **********************//
	// ====================================================================//
	public function check_input_patch() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		// (0) $input["id"]
		if (!array_key_exists("id", $input) or !$this->isValidUUID($input["id"])) {
			$this->setInputError("This required input is missing: 'id' [uuid string]");
			return false;
		}
		// $input["metadata"] is json
		if (array_key_exists("metadata", $input)) {
			if (!$this->validate_json($input["metadata"])) {
				$this->setInputError("Error on decoding 'metadata' JSON input");
				return false;
			}
			$input["metadata"]["columns"] = $input["columns"];
		}
		// $input["sampling"]
		if (array_key_exists("sampling", $input) and (!is_int($input["sampling"]) || $input["sampling"] < 0)) {
			$this->setInputError("Uncorrect input: 'sampling'[integer > 0] <in seconds>");
			return false;
		}
		// check mapping values
		if (array_key_exists("mapping", $input) and !$this->check_mapping_values($input["mapping"])) {
			return false;
		}
		
		$this->setParams($input);
		
		return true;
	}

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
}
?>