<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."Timeseries.php");

// Timeseries class
Class TimeseriesController extends RESTController {
	
	public $column_types_array = array("smallint", "integer", "double precision"); // valid for all columns (except for the default "time" column, handled by the system)

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
				
				$input = $this->getParams();
				$ts_info = null;

				// if $input["id"] is defined
				if(array_key_exists("id", $input) and isset($input["id"])) {
					// get info about timeseries
					$ts_info = $this->obj->getInfo($input["id"]);

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
								"resource_id" => $input["id"]
							));
						}
					}

					$this->get();
					break;
				} 
				else {
					if (!array_key_exists("public", $input) or (array_key_exists("public", $input) and isset($input["public"]) and !$input["public"])) {
						// then check if authorized action
						$this->authorizedAction(array(
							"scope"=>"timeseries-read"
						));
					}
					$this->get();
					break;
				}				

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
				$this->getInput();
				if (!$this->check_input_delete()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"timeseries-edit",
					"resource_id"=>$this->getParams()["id"]
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
	// ****************** post - timeseries instance **********************//
	// ====================================================================//
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (1) $input["schema"]
		if (!array_key_exists("schema", $input) || empty($input["schema"])){
			$this->setInputError("This required input is missing: 'schema' [string]");
			return false;
		}
		if (!$this->verifySecureDBString($input["schema"])) {
			$this->setInputError("Uncorrect input: 'schema' [string]. Accept only lowercase letters followed by numbers and underscore. Regular expression: $this->SECURE_DB_STRING_REGEX");
			return false;
		}
		// force schema to lowercase
		$input["schema"] = strtolower($input["schema"]);

		// (2) $input["name"] 
		if (!array_key_exists("name", $input) || empty($input["name"])){
			$this->setInputError("This required input is missing: 'name' [string]");
			return false;
		}
		if (!$this->verifySecureDBString($input["name"])) {
			$this->setInputError("Uncorrect input: 'name' [string]. Accept only lowercase letters followed by numbers and underscore. Regular expression: $this->SECURE_DB_STRING_REGEX");
			return false;
		}
		// force name to lowercase
		$input["name"] = strtolower($input["name"]);
		
		// (3) $input["sampling"]
		if (array_key_exists("sampling", $input)) {
			if (!is_int($input["sampling"]) || $input["sampling"] <= 0) {
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
			} else {
				// check for columns format
				for($i=0; $i<count($input["columns"]); $i++) {
					// check if columns is an array
					if(!is_array($input["columns"][$i])) {
						$this->setInputError("Uncorrect input: element #$i of 'columns' is not an array");
						return false;
					}
					// check if 'name' is defined in the column item
					if(!array_key_exists("name", $input["columns"][$i])) {
						$this->setInputError("Uncorrect input: property 'name' for the element #$i of 'columns' is not defined");
						return false;
					}
					// check if 'name' is a secure string for db
					if (!$this->verifySecureDBString($input["columns"][$i]["name"])) {
						$this->setInputError("Uncorrect input: property 'name' for the element #$i of 'columns'. Accept only lowercase letters followed by numbers and underscore. Regular expression: $this->SECURE_DB_STRING_REGEX");
						return false;
					}
					// force columns name to lowercase
					$input["columns"][$i]["name"] = strtolower($input["columns"][$i]["name"]); 

					// check if 'type' is defined in the column item
					if(array_key_exists("type", $input["columns"][$i])) {
						if (!in_array(strtolower($input["columns"][$i]["type"]), $this->column_types_array)) {
							$this->setInputError("Uncorrect input: property 'type' for the element #$i of 'columns'. Must be a value in the following list: " . implode(", ", $this->column_types_array) . ". Your value = " . strval($input["columns"][$i]["type"]));
							return false;
						}
					} else {
						$input["columns"][$i]["type"] = "double precision";
					}
				}
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

		// $input["public"] 
		if (array_key_exists("public", $input)) {
			if (!is_bool($input["public"])) {
				$this->setInputError("Uncorrect input: 'public' [boolean]");
				return false;
			} else {
				$input["public"] = (intval($input["public"]) === 1 or $input["public"] === true or $input["public"] === "true");
			}
		} else {
			$input["public"] = true;
		}

		// $input["with_tz"] 
		if (array_key_exists("with_tz", $input)) {
			if (!is_bool($input["with_tz"])) {
				$this->setInputError("Uncorrect input: 'with_tz' [boolean]");
				return false;
			} else {
				$input["with_tz"] = (intval($input["with_tz"]) === 1 or $input["with_tz"] === true or $input["with_tz"] === "true");
			}
		} else {
			$input["with_tz"] = false;
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
	public function check_input_get() {

		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();

		// listCol
		if(!array_key_exists("listCol", $input)) {
			$input["listCol"] = false;
		} else {
			if (!isset($input["id"])) {
				$this->setInputError("'listCol' is only supported when 'id' is specified");
				return false;
			}
			$input["listCol"] = (intval($input["listCol"]) === 1 or $input["listCol"] === true or $input["listCol"] === "true");
		}

		// showColDefs
		if(!array_key_exists("showColDefs", $input)) {
			$input["showColDefs"] = false;
		} else {
			if (!isset($input["id"])) {
				$this->setInputError("'showColDefs' is only supported when 'id' is specified");
				return false;
			}
			$input["showColDefs"] = (intval($input["showColDefs"]) === 1 or $input["showColDefs"] === true or $input["showColDefs"] === "true");
		}

		// showFirstMapping
		if(!array_key_exists("showFirstMapping", $input)) {
			$input["showFirstMapping"] = false;
		} else {
			if (!isset($input["id"])) {
				$this->setInputError("'showFirstMapping' is only supported when 'id' is specified");
				return false;
			}
			$input["showFirstMapping"] = (intval($input["showFirstMapping"]) === 1 or $input["showFirstMapping"] === true or $input["showFirstMapping"] === "true");
		}

		// showMapping
		if(!array_key_exists("showMapping", $input)) {
			$input["showMapping"] = false;
		} else {
			if (!isset($input["id"])) {
				$this->setInputError("'showMapping' is only supported when 'id' is specified");
				return false;
			}
			$input["showMapping"] = (intval($input["showMapping"]) === 1 or $input["showMapping"] === true or $input["showMapping"] === "true");
		}

		// public
		if(array_key_exists("public", $input)) {
			$input["public"] = (intval($input["public"]) === 1 or $input["public"] === true or $input["public"] === "true");
		}

		// station_id
		if (array_key_exists("station_id", $input)) {
			$input["station_id"] = intval($input["station_id"]);
		}

		$this->setParams($input);
		
		return true;
	}
	
	public function get($jsonfields=array("metadata", "last_value")) {
	
		$params = $this->getParams();

		// Result by station_id
		if(array_key_exists("station_id", $params)) {

			$result = $this->obj->getListByStationID($params);
			if ($result["status"]) {
				// prepare response like the following:
				/*
				"channels": [{
						"channel_id": 934,
						"channel_name": "Body Temperature (K)",
						"timeseries": ["ffbb6271-4531-4ebd-ac78-7dda8f687b9d", ...]
				}]
				*/
				$response = array("station" => array());
				if (count($result["data"]) >= 0) {
					$response["station"]["name"] = $result["data"][0]["station_name"];
					$response["station"]["channels"] = array();
				}
				$current_channel_id = -1;
				$current_array_index = 0;
				for($i=0; $i<count($result["data"]); $i++) {
					$item = array_merge([], $result["data"][$i]);
					unset($item["station_name"]);
					if ($item["channel_id"] != $current_channel_id) {
						$item["timeseries"] = array(array("id" => $item["timeseries_id"], "schema" => $item["timeseries_schema"], "name" => $item["timeseries_name"], "metadata" => isset($item["timeseries_metadata"]) ? json_decode($item["timeseries_metadata"]) : NULL));
						unset($item["timeseries_id"]);
						unset($item["timeseries_schema"]);
						unset($item["timeseries_name"]);
						unset($item["timeseries_metadata"]);
						$current_array_index = array_push($response["station"]["channels"], $item);
						$current_channel_id = $item["channel_id"];
					} else {
						array_push($response["station"]["channels"][$current_array_index-1]["timeseries"], array(array("id" => $item["timeseries_id"], "schema" => $item["timeseries_schema"], "name" => $item["timeseries_name"], "metadata" => isset($item["timeseries_metadata"]) ? json_decode($item["timeseries_metadata"]) : NULL)));
					}
				}
				$this->setData($response);
			} else {
				$this->setStatusCode(404);
				$this->setError($result);
			}
			return;
		}

		// Default result
		$result = $this->obj->getList($params);

		if ($result["status"]) {

			$singleRowResult = false;
			if (count($result["data"]) == 1 and isset($params["id"])) $singleRowResult = true;

			for($i=0; $i<count($result["data"]); $i++) {
				foreach($jsonfields as $fieldname) {
					$result["data"][$i][$fieldname] = isset($result["data"][$i][$fieldname]) ? json_decode($result["data"][$i][$fieldname]) : NULL;
					
					// by id response
					if ($singleRowResult) {
						// add columns list on response if by id
						if (array_key_exists("listCol", $params) and $params["listCol"]) {
							$result["data"][$i]["columns"] = $this->obj->getColumnList($result["data"][$i]["id"], $addInfo=false);
						}
						// add columns list + types on response if by id
						if (array_key_exists("showColDefs", $params) and $params["showColDefs"]) {
							$result["data"][$i]["columns"] = $this->obj->getColumnList($result["data"][$i]["id"], $addInfo=true);
						}
						// add first mapping list on response if by id
						$dependencies = null;
						if (array_key_exists("showFirstMapping", $params) and $params["showFirstMapping"]) {
							$dependencies = $this->obj->getDependencies($result["data"][$i]["id"], $transpose=false);
							$result["data"][$i]["firstMapping"] = (isset($dependencies) and count($dependencies)) > 0 ? $dependencies[0] : array();
						}
						// add mapping (included channel_id) list on response if by id
						if (array_key_exists("showMapping", $params) and $params["showMapping"]) {
							/*
							$result["data"][$i]["mapping"] = array(
								"channel_id" => $this->obj->getIDChannelList($result["data"][$i]["id"])
							);
							*/
							if (isset($dependencies)) {
								// transpose result of firstMapping and make unique id(s) for nets, channels, sensors
								$array_one = $dependencies;
								$array_two = $this->transpose($array_one);
								foreach ($array_two as $key => $item) {
									$array_two[$key] = array_unique($array_two[$key]);
								}
								$dependencies = $array_two;
							} else {
								$dependencies = $this->obj->getDependencies($result["data"][$i]["id"]); // yet transposed
							}
							$result["data"][$i]["mapping"] = $dependencies;
						}
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
		}
		// $input["sampling"]
		if (array_key_exists("sampling", $input) and (!is_int($input["sampling"]) || $input["sampling"] <= 0)) {
			$this->setInputError("Uncorrect input: 'sampling'[integer > 0] <in seconds>");
			return false;
		}
		// check mapping values
		if (array_key_exists("mapping", $input) and !$this->check_mapping_values($input["mapping"])) {
			return false;
		}
		// $input["public"] 
		if (array_key_exists("public", $input) and !is_bool($input["public"])){
			$this->setInputError("Uncorrect input: 'public' [boolean]");
			return false;
		}

		$this->setParams($input);
		
		return true;
	}

	public function patch() {

		// insert default update info
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
			if ($result["rows"] == 0) {
				$this->setStatusCode(404);
			} else {
				$this->setStatusCode(409);
			}
			$this->setError($result);
		}
	}

	// ====================================================================//
	// ****************** delete - timeseries instance **********************//
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