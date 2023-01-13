<?php
require_once("QueryManager.php");

Class Digitizers extends QueryManager {
	
	protected $tablename = "tsd_pnet.digitizers";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, serial_number, digitizertype_id, additional_info, create_user) VALUES (".
				"'" . $input["name"] . "', " . 
				(isset($input["serial_number"]) ? ("'" .$input["serial_number"] . "'") : "NULL") . ", " .
				(isset($input["digitizertype_id"]) ? $input["digitizertype_id"] : "NULL") . ", " .
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

		$query = "SELECT d.id, d.name, d.serial_number, d.digitizertype_id, d.additional_info, dt.name AS digitizertype_name FROM " . $this->tablename . " d left join tsd_pnet.digitizertypes dt on d.digitizertype_id = dt.id WHERE d.remove_time IS NULL";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "alias" => "d.id", "quoted" => false),
				"name" => array("alias" => "d.name", "quoted" => true),
				"serial_number" => array("alias" => "d.serial_number", "quoted" => true),
				"digitizertype_id" => array("alias" => "d.digitizertype_id", "quoted" => false),
				"digitizertype_name" => array("alias" => "dt.name", "quoted" => true),
				"additional_info" => array("quoted" => true, "alias" => "d.additional_info")
			));
		}

		if (isset($input) and is_array($input)) { 
			if (isset($input["sort_by"])) {
				$cols = explode(",", $input["sort_by"]);
				$query .= $this->composeOrderBy($cols, array(
					"id" => array("alias" => "d.id"),
					"name" => array("alias" => "d.name"),
					"digitizertype_id" => array("alias" => "d.digitizertype_id")
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
			"digitizertype_id" => array("quoted" => false)
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