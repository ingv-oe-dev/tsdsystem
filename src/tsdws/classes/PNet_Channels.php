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

			$next_query = "INSERT INTO " . $this->tablename . " (name, sensor_id, sensortype_id, metadata, start_datetime, end_datetime, info, create_user) VALUES (
				'" . $input["name"] . "',
				" . $input["sensor_id"]. ", " .
				(isset($input["sensortype_id"]) ? $input["sensortype_id"] : "NULL") . ", " .
				(isset($input["metadata"]) ? ("'" . json_encode($input["metadata"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ", " .
				(isset($input["start_datetime"]) ? ("'" . $input["start_datetime"] . "'") : "NULL") . ", " .
				(isset($input["end_datetime"]) ? ("'" . $input["end_datetime"] . "'") : "NULL") . ", " .
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

		$query = "SELECT c.id, c.name, c.sensor_id, c.sensortype_id, c.metadata, TO_CHAR(c.start_datetime,'$this->OUTPUT_PSQL_ISO8601_FORMAT') as start_datetime, TO_CHAR(c.end_datetime,'$this->OUTPUT_PSQL_ISO8601_FORMAT') as end_datetime, c.info, s.name AS sensor_name, st.name AS sensortype_name, n.id AS net_id, n.name AS net_name, (NOT c.end_datetime IS NULL AND c.end_datetime < now() at time zone 'utc') AS old_channel, NULLIF(s.remove_time, NULL) AS deprecated" . 
		" FROM " . $this->tablename . " c " . 
		" LEFT JOIN tsd_pnet.sensors s ON s.id = c.sensor_id " .
		" LEFT JOIN tsd_pnet.sensortypes st ON st.id = c.sensortype_id " .
		" LEFT JOIN tsd_pnet.nets n ON n.id = s.net_id " .
		" WHERE c.remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false, "alias" => "c.id"),
				"name" => array("quoted" => true, "alias" => "c.name"),
				"sensor_id" => array("quoted" => false, "alias" => "c.sensor_id"),
				"sensortype_id" => array("quoted" => false, "alias" => "c.sensortype_id"),
				"metadata" => array("quoted" => true, "alias" => "c.metadata"),
				"info" => array("quoted" => true, "alias" => "c.info"),
				"net_id" => array("quoted" => false, "alias" => "n.id"),
				"net_name" => array("quoted" => true, "alias" => "n.name"),
				"sensor_name" => array("quoted" => true, "alias" => "s.name"),
				"sensortype_name" => array("quoted" => true, "alias" => "st.name")
			));
			if (array_key_exists("start_datetime", $input) and isset($input["start_datetime"])){
				$query .= " AND c.start_datetime >= '" . $input["start_datetime"];
			}
			if (array_key_exists("end_datetime", $input) and isset($input["end_datetime"])){
				$query .= " AND c.end_datetime <= '" . $input["end_datetime"] . "'";
			}
		}
		
		$query .= " ORDER BY n.name, s.name, c.end_datetime DESC, c.name";
		//echo $query;
		return $this->getRecordSet($query);
	}

	public function update($input) {

		$updateFields = array(
			"name" => array("quoted" => true),
			"sensor_id" => array("quoted" => false),
			"metadata" => array("json" => true),
			"sensortype_id" => array("quoted" => false),
			"start_datetime" => array("quoted" => true),
			"end_datetime" => array("quoted" => true),
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