<?php
require_once("QueryManager.php");

Class Channels extends QueryManager {
	
	protected $tablename = "tsd_pnet.channels";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, sensor_id, info, create_user) VALUES (
				'" . $input["name"] . "',
				" . $input["sensor_id"]. ", " .
				(isset($input["info"]) ? ("'" . json_encode($input["info"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ", 
				" . ((array_key_exists("create_user", $input) and isset($input["create_user"]) and is_int($input["create_user"])) ? $input["create_user"] : "NULL") . " 
				)";
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();
			$response["rows"] = $stmt->rowCount();
			$response["id"] = $this->myConnection->lastInsertId();

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
		
		$query = "SELECT c.id, c.name, c.sensor_id, c.info, s.name AS sensor_name, n.id AS net_id, n.name AS net_name " . 
		" FROM " . $this->tablename . " c " . 
		" LEFT JOIN tsd_pnet.sensors s ON s.id = c.sensor_id " .
		" LEFT JOIN tsd_pnet.nets n ON n.id = s.net_id " .
		" WHERE c.remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false, "alias" => "c.id"),
				"name" => array("quoted" => true),
				"sensor_id" => array("quoted" => false)
			));
		}
		
		$query .= " ORDER BY n.name, s.name, c.name";
		//echo $query;
		return $this->getRecordSet($query);
	}

	public function update($input) {

		$updateFields = array(
			"name" => array("quoted" => true),
			"sensor_id" => array("quoted" => false),
			"info" => array("json" => true),
			"update_time" => array("quoted" => false),
			"update_user" => array("quoted" => false)
		);

		$whereStmt = " WHERE remove_time IS NULL AND id = " . $input["id"];

		return $this->genericUpdateRoutine($input, $updateFields, $whereStmt);
	}

	public function delete($input) {

		$updateFields = array(
			"remove_time" => array("quoted" => false),
			"remove_user" => array("quoted" => false)
		);

		$whereStmt = " WHERE remove_time IS NULL AND id = " . $input["id"];
		
		return $this->genericUpdateRoutine($input, $updateFields, $whereStmt);
	}
}