<?php
require_once("QueryManager.php");

Class Sensortypes extends QueryManager {
	
	protected $tablename = "tsd_pnet.sensortypes";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, model, components, sensortype_category_id, response_parameters, additional_info, create_user) VALUES (" . 
				"'" .pg_escape_string($input["name"]) . "', " .
				(isset($input["model"]) ? ("'" .pg_escape_string($input["model"]) . "'") : "NULL") . ", " .
				(isset($input["components"]) ? ("'" . pg_escape_string(json_encode($input["components"], JSON_NUMERIC_CHECK)) . "'") : "NULL") . ", " .
				(isset($input["sensortype_category_id"]) ? $input["sensortype_category_id"] : "NULL") . ", " .
				(isset($input["response_parameters"]) ? ("'" . pg_escape_string(json_encode((object) $input["response_parameters"], JSON_NUMERIC_CHECK)) . "'") : "NULL") . ", " .
				(isset($input["additional_info"]) ? ("'" . pg_escape_string(json_encode((object) $input["additional_info"], JSON_NUMERIC_CHECK)) . "'") : "NULL") . ", " .
				((array_key_exists("create_user", $input) and isset($input["create_user"]) and is_int($input["create_user"])) ? $input["create_user"] : "NULL") . 
			")";
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
		
		$query = "SELECT id, name, model, components, sensortype_category_id, response_parameters, additional_info FROM " . $this->tablename . " WHERE remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false),
				"name" => array("quoted" => true),
				"model" => array("quoted" => true),
				"components" => array("quoted" => true),
				"sensortype_category_id" => array("quoted" => false),
				"response_parameters" => array("quoted" => true),
				"additional_info" => array("quoted" => true)
			));

			if (isset($input["sort_by"])) {
				$cols = explode(",", $input["sort_by"]);
				$query .= $this->composeOrderBy($cols, array(
					"id" => array("alias" => "id"),
					"name" => array("alias" => "name"),
					"model" => array("alias" => "model"),
					"sensortype_category_id" => array("alias" => "sensortype_category_id")
				));
			}
		}
		
		//echo $query;
		return $this->getRecordSet($query);
	}

	public function update($input) {

		$updateFields = array(
			"name" => array("quoted" => true),
			"model" => array("quoted" => true),
			"components" => array("json" => true, "associative" => false),
			"sensotype_category_id" => array("quoted", false),
			"response_parameters" => array("json" => true),
			"additional_info" => array("json" => true),
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