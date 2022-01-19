<?php
require_once("QueryManager.php");

Class Sensors extends QueryManager {
	
	private $tablename = "pnet.sensors";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, coords, sensortype_id, net_id, metadata, custom_props) VALUES (".
				"'" . $input["name"] . "', " . 
				((isset($input["lon"]) and isset($input["lat"])) ? ("'POINT(" . $input["lon"] . " " . $input["lat"] . ")'::geometry") : "NULL") . ", " .
				(isset($input["sensortype_id"]) ? $input["sensortype_id"] : "NULL") . ", " .
				(isset($input["net_id"]) ? $input["net_id"] : "NULL") . ", " .
				(isset($input["metadata"]) ? ("'" . json_encode($input["metadata"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ", " .
				(isset($input["custom_props"]) ? ("'" . json_encode($input["custom_props"], JSON_NUMERIC_CHECK) . "'") : "NULL") .
			") ON CONFLICT (LOWER(name)) DO NOTHING";

			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();
			$response["rows"] = $stmt->rowCount();
		
			// select inserted id
			$next_query = "SELECT id FROM " . $this->tablename . " WHERE LOWER(name) = LOWER('" . $input["name"] . "')";
			$sqlResult = $this->myConnection->query($next_query);
			$inserted_id = $sqlResult->fetchColumn();
			$response["id"] = $inserted_id;

			// commit
			$this->myConnection->commit();

			$response["status"] = true;

			// return result
			return $response;
		}
		catch (Exception $e){
			
			// rollback
			$this->myConnection->rollback();

			return array(
				"status" => false,
				"failed_query" => $next_query,
				"error" => $e->getMessage()
			);
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