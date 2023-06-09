<?php
require_once("QueryManager.php");

Class Sensors extends QueryManager {
	
	protected $tablename = "tsd_pnet.sensors";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, serial_number, sensortype_id, additional_info, create_user) VALUES (".
				"'" . pg_escape_string($input["name"]) . "', " . 
				(isset($input["serial_number"]) ? ("'" .pg_escape_string($input["serial_number"]) . "'") : "NULL") . ", " .
				(isset($input["sensortype_id"]) ? $input["sensortype_id"] : "NULL") . ", " .
				(isset($input["additional_info"]) ? ("'" . json_encode((object)$input["additional_info"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ",
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

		$query = "SELECT s.id, s.name, s.serial_number, s.sensortype_id, s.additional_info, st.name AS sensortype_name, st.model AS sensortype_model, st.components AS sensortype_components FROM " . $this->tablename . " s left join tsd_pnet.sensortypes st on s.sensortype_id = st.id WHERE s.remove_time IS NULL";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "alias" => "s.id", "quoted" => false),
				"name" => array("alias" => "s.name", "quoted" => true),
				"serial_number" => array("alias" => "s.serial_number", "quoted" => true),
				"sensortype_id" => array("alias" => "s.sensortype_id", "quoted" => false),
				"sensortype_name" => array("alias" => "st.name", "quoted" => true),
				"sensortype_model" => array("alias" => "st.model", "quoted" => true),
				"sensortype_components" => array("alias" => "st.components", "quoted" => true),
				"additional_info" => array("quoted" => true, "alias" => "s.additional_info")
			));
		}

		if (isset($input) and is_array($input)) { 
			if (isset($input["sort_by"])) {
				$cols = explode(",", $input["sort_by"]);
				$query .= $this->composeOrderBy($cols, array(
					"id" => array("alias" => "s.id"),
					"name" => array("alias" => "s.name"),
					"sensortype_id" => array("alias" => "s.sensortype_id"),
					"sensortype_name" => array("alias" => "s.sensortype_name"),
					"sensortype_model" => array("alias" => "s.sensortype_model")
				));
			}
		}
		
		//echo $query;
		$response = $this->getRecordSet($query);

		return $response;
	}
	
	public function update($input) {

		$updateFields = array(
			"name" => array("quoted" => true),
			"serial_number" => array("quoted" => true),
			"additional_info" => array("json" => true),
			"sensortype_id" => array("quoted" => false)
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