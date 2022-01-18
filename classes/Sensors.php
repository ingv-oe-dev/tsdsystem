<?php
require_once("QueryManager.php");

Class Sensors extends QueryManager {
	
	private $tablename = "pnet.sensors";
	
	public function insert($input) {

		$sql = "INSERT INTO " . $this->tablename . " (name, coords, sensortype_id, net_id, metadata, custom_props) VALUES (".
			"'" . $input["name"] . "', " . 
			((isset($input["lon"]) and isset($input["lat"])) ? ("'POINT(" . $input["lon"] . " " . $input["lat"] . ")'::geometry") : "NULL") . ", " .
			(isset($input["sensortype_id"]) ? $input["sensortype_id"] : "NULL") . ", " .
			(isset($input["net_id"]) ? $input["net_id"] : "NULL") . ", " .
			(isset($input["metadata"]) ? ("'" . json_encode($input["metadata"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ", " .
			(isset($input["custom_props"]) ? ("'" . json_encode($input["custom_props"], JSON_NUMERIC_CHECK) . "'") : "NULL") .
		")";
		
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
		
		$query = "SELECT id, name, ST_AsGeoJSON(coords) AS coords, sensortype_id, net_id, metadata, custom_props FROM " . $this->tablename . " WHERE remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false),
				"name" => array("quoted" => true),
				"sensortype_id" => array("quoted" => false),
				"net_id" => array("quoted" => false),
			));
		}
		
		//echo $query;
		return $this->getRecordSet($query);
	}
}