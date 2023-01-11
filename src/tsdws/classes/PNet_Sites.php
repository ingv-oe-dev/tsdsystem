<?php
require_once("QueryManager.php");

Class Sites extends QueryManager {
	
	protected $tablename = "tsd_pnet.sites";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, coords, quote, additional_info, create_user) VALUES (".
				"'" . $input["name"] . "', " . 
				(isset($input["coords"]) ? ("ST_GeomFromGeoJSON('" . json_encode((object) $input["coords"], JSON_NUMERIC_CHECK) . "')") : "NULL") . ", " .
				(isset($input["quote"]) ? $input["quote"] : "NULL") . ", " .
				(isset($input["additional_info"]) ? ("'" . json_encode((object) $input["additional_info"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ",
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
		
		$query = "SELECT id, name, ST_AsGeoJSON(coords) AS coords, ST_AsGeoJSON(ST_Centroid(coords)) AS centroid, additional_info FROM " . $this->tablename . " WHERE remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false),
				"name" => array("quoted" => true),
				"additional_info" => array("quoted" => true)
			));

			$query .= $this->extendSpatialQuery($input, "coords");

			if (isset($input["sort_by"])) {
				$cols = explode(",", $input["sort_by"]);
				$query .= $this->composeOrderBy($cols, array(
					"id" => array("alias" => "id"),
					"name" => array("alias" => "name")
				));
			}
		}
		
		//echo $query;
		return $this->getRecordSet($query);
	}

	public function update($input) {

		$updateFields = array(
			"name" => array("quoted" => true),
			"quote" => array("quoted" => false),
			"additional_info" => array("json" => true),
			"update_time" => array("quoted" => false),
			"update_user" => array("quoted" => false)
		);

		$input["coords"] = isset($input["coords"]) ? ("ST_GeomFromGeoJSON('" . json_encode((object) $input["coords"], JSON_NUMERIC_CHECK) . "')") : "NULL";
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