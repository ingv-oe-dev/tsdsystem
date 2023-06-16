<?php
require_once("QueryManager.php");

Class Nets extends QueryManager {
	
	protected $tablename = "tsd_pnet.nets";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, description, owner_id, additional_info, create_user) VALUES (
				'" . pg_escape_string($input["name"]) . "', " .
				(isset($input["description"]) ? ("'" . pg_escape_string($input["description"]) ."'") : "NULL") . ", " .  
				(isset($input["owner_id"]) ? $input["owner_id"] : "NULL") . ", " .
				(isset($input["additional_info"]) ? ("'" . pg_escape_string(json_encode($input["additional_info"], JSON_NUMERIC_CHECK)) . "'") : "NULL") . ", 
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
		
		$query = "SELECT n.id, n.name, n.description, n.owner_id, n.additional_info, count(s.id) as n_nodes FROM " . $this->tablename . " n left join tsd_pnet.stations s on n.id = s.net_id and s.remove_time is null WHERE n.remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "alias" => "n.id", "quoted" => false),
				"name" => array("quoted" => true, "alias" => "n.name"),
				"description" => array("quoted" => true, "alias" => "n.description"),
				"additional_info" => array("quoted" => true, "alias" => "n.additional_info"),
				"owner_id" => array("quoted" => false, "alias" => "n.owner_id")
			));
		}

		$query .= " GROUP BY n.id ";

		if (isset($input) and is_array($input)) { 
			if (isset($input["sort_by"])) {
				$cols = explode(",", $input["sort_by"]);
				$query .= $this->composeOrderBy($cols, array(
					"id" => array("alias" => "n.id"),
					"name" => array("alias" => "n.name")
				));
			}
		}
		
		//echo $query;
		return $this->getRecordSet($query);
	}

	public function update($input) {

		$updateFields = array(
			"name" => array("quoted" => true),
			"description" => array("quoted" => true),
			"owner_id" => array("quoted" => false),
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