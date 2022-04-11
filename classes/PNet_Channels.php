<?php
require_once("QueryManager.php");

Class Channels extends QueryManager {
	
	private $tablename = "tsd_pnet.channels";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, sensor_id, info) VALUES (
				'" . $input["name"] . "',
				" . $input["sensor_id"]. ", " .
				(isset($input["info"]) ? ("'" . json_encode($input["info"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ") 
				ON CONFLICT (LOWER(name), sensor_id) DO NOTHING";
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();
			$response["rows"] = $stmt->rowCount();

			// select inserted id
			$next_query = "SELECT id FROM " . $this->tablename . " WHERE LOWER(name) = LOWER('" . $input["name"] . "') AND sensor_id = " . $input["sensor_id"];
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
		
		$query = "SELECT id, name, sensor_id, info FROM " . $this->tablename . " WHERE remove_time IS NULL ";
		
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