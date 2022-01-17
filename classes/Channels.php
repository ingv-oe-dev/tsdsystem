<?php
require_once("QueryManager.php");

Class Channels extends QueryManager {
	
	private $tablename = "pnet.channels";
	
	public function insert($input) {
		$sql = "INSERT INTO " . $this->tablename . " (name, sensor_id) 
			VALUES ('" . $input["name"] . "'," . $input["sensor_id"]. ")";
		
		$executeSQLCommand = $this->executeSQLCommand($sql);
		
		// return result
		if (end($executeSQLCommand)["status"]) {
			// get inserted id
			$query = "SELECT id FROM " . $this->tablename . " WHERE name = '" . $input["name"] . "' AND sensor_id = " . $input["sensor_id"];
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
		
		$query = "SELECT id, name, sensor_id FROM " . $this->tablename . " WHERE remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false),
				"name" => array("quoted" => true),
				"sensor_id" => array("quoted" => false)
			));
		}
		
		//echo $query;
		return $this->getRecordSet($query);
	}
}