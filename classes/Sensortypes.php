<?php
require_once("QueryManager.php");

Class Sensortypes extends QueryManager {
	
	private $tablename = "pnet.sensortypes";
	
	public function insert($input) {
		$sql = "INSERT INTO " . $this->tablename . " (name, default_props) 
			VALUES ('" . $input["name"] . "', '" . json_encode($input["default_props"], JSON_NUMERIC_CHECK) . "')";
		
		$executeSQLCommand = $this->executeSQLCommand($sql);
		
		// return result
		if (end($executeSQLCommand)["status"]) {
			// get inserted id
			$query = "SELECT id FROM " . $this->tablename . " WHERE name = '" . $input["name"] . "'";
			$inserted_id = $this->getSingleField($query);
			return array(
				"status" => true,
				"id" => $inserted_id["status"] ? $inserted_id["data"] : null,
				"warning" => $inserted_id["status"] ? null : $inserted_id["error"],
			);
		} else {
			return end($executeSQLCommand);
		}
	}
	
	public function getList($input) {
		
		$query = "SELECT id, name, default_props FROM " . $this->tablename . " WHERE remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false),
				"name" => array("quoted" => true)
			));
		}
		
		//echo $query;
		return $this->getRecordSet($query);
	}
}