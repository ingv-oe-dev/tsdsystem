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

			$next_query = "INSERT INTO " . $this->tablename . " (name, coords, quote, sensortype_id, net_id, site_id, metadata, custom_props, create_user) VALUES (".
				"'" . $input["name"] . "', " . 
				((isset($input["lon"]) and isset($input["lat"])) ? ("'POINT(" . $input["lon"] . " " . $input["lat"] . ")'::geometry") : "NULL") . ", " .
				(isset($input["quote"]) ? $input["quote"] : "NULL") . ", " .
				(isset($input["sensortype_id"]) ? $input["sensortype_id"] : "NULL") . ", " .
				(isset($input["net_id"]) ? $input["net_id"] : "NULL") . ", " .
				(isset($input["site_id"]) ? $input["site_id"] : "NULL") . ", " .
				(isset($input["metadata"]) ? ("'" . json_encode($input["metadata"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ", " .
				(isset($input["custom_props"]) ? ("'" . json_encode($input["custom_props"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ",
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
		
		$query = "SELECT id, name, ST_AsGeoJSON(coords) AS coords, quote, sensortype_id, net_id, site_id, metadata, custom_props FROM " . $this->tablename . " WHERE remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false),
				"name" => array("quoted" => true),
				"sensortype_id" => array("quoted" => false),
				"net_id" => array("quoted" => false),
				"site_id" => array("quoted" => false)
			));
		}
		
		//echo $query;
		return $this->getRecordSet($query);
	}

	public function update($input) {

		$updateFields = array(
			"name" => array("quoted" => true),
			"quote" => array("quoted" => false),
			"metadata" => array("quoted" => true),
			"custom_props" => array("quoted" => true),
			"sensortype_id" => array("quoted" => false),
			"net_id" => array("quoted" => false),
			"site_id" => array("quoted" => false),
			"update_time" => array("quoted" => false),
			"update_user" => array("quoted" => false)
		);

		$input["coords"] = ((isset($input["lon"]) and isset($input["lat"])) ? ("'POINT(" . $input["lon"] . " " . $input["lat"] . ")'::geometry") : "NULL");
		if ($input["coords"] != "NULL") {
			$updateFields["coords"] = array("quoted" => false);
		}

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