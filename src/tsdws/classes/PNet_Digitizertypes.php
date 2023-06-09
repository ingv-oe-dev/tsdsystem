<?php
require_once("QueryManager.php");

Class Digitizertypes extends QueryManager {
	
	protected $tablename = "tsd_pnet.digitizertypes";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, model, final_sample_rate, final_sample_rate_measure_unit, sensitivity, sensitivity_measure_unit, dynamical_range, dynamical_range_measure_unit, additional_info, create_user) VALUES (" . 
				"'" .pg_escape_string($input["name"]) . "', " .
				(isset($input["model"]) ? ("'" .pg_escape_string($input["model"]) . "'") : "NULL") . ", " .
				(isset($input["final_sample_rate"]) ? $input["final_sample_rate"] : "NULL") . ", " .
				(isset($input["final_sample_rate_measure_unit"]) ? ("'" .pg_escape_string($input["final_sample_rate_measure_unit"]) . "'") : "NULL") . ", " .
				(isset($input["sensitivity"]) ? $input["sensitivity"] : "NULL") . ", " .
				(isset($input["sensitivity_measure_unit"]) ? ("'" .pg_escape_string($input["sensitivity_measure_unit"]) . "'") : "NULL") . ", " .
				(isset($input["dynamical_range"]) ? $input["dynamical_range"] : "NULL") . ", " .
				(isset($input["dynamical_range_measure_unit"]) ? ("'" .pg_escape_string($input["dynamical_range_measure_unit"]) . "'") : "NULL") . ", " .
				(isset($input["additional_info"]) ? ("'" . json_encode((object) $input["additional_info"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ", " .
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
		
		$query = "SELECT id, name, model, final_sample_rate, final_sample_rate_measure_unit, sensitivity, sensitivity_measure_unit, dynamical_range, dynamical_range_measure_unit, additional_info FROM " . $this->tablename . " WHERE remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false),
				"name" => array("quoted" => true),
				"model" => array("quoted" => true),
				"additional_info" => array("quoted" => true)
			));

			if (isset($input["sort_by"])) {
				$cols = explode(",", $input["sort_by"]);
				$query .= $this->composeOrderBy($cols, array(
					"id" => array("alias" => "id"),
					"name" => array("alias" => "name"),
					"model" => array("alias" => "model")
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
			"final_sample_rate" => array("quoted" => false),
			"final_sample_rate_measure_unit" => array("quoted" => true),
			"sensitivity" => array("quoted" => false),
			"sensitivity_measure_unit" => array("quoted" => true),
			"dynamical_range" => array("quoted" => false),
			"dynamical_range_measure_unit" => array("quoted" => true),
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