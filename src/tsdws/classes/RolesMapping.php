<?php
require_once("QueryManager.php");

Class RolesMapping extends QueryManager {
	
	protected $tablename = "tsd_users.members_mapping_roles";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();
           
            // update mapping if exists
            $next_query = "UPDATE tsd_users.members_mapping_roles
                SET priority = " . $input["priority"] . ", update_time = timezone('utc'::text, now()), remove_time = NULL 
                WHERE member_id = " . $input["member_id"] . " and role_id = " . $input["role_id"]; 
            $stmt = $this->myConnection->prepare($next_query);
            $stmt->execute();
            $response["rows"] = $stmt->rowCount();

            // insert if mapping does not exist
            if ($response["rows"] == 0) { 
                $next_query = "INSERT INTO tsd_users.members_mapping_roles (member_id, role_id, priority, update_time) VALUES (
                    " . $input["member_id"] . ", 
                    " . $input["role_id"] . ", 
                    " . $input["priority"] . ",
                    timezone('utc'::text, now())
                )";
                $stmt = $this->myConnection->prepare($next_query);
                $stmt->execute();
                $response["rows"] = $stmt->rowCount();
            }
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
		
		$query = "select 
			m.id as member_id, m.email as member, r.id as role_id, r.name as role, mmr.priority as priority, mmr.update_time as update_time
		from
			tsd_users.members m
		inner join tsd_users.members_mapping_roles mmr on m.id = mmr.member_id and mmr.remove_time is null and m.deleted is null
		inner join tsd_users.roles r on r.id = mmr.role_id and r.remove_time is null";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"member_id" => array("id" => true, "quoted" => false, "alias" => "m.id"),
				"role_id" => array("id" => true, "quoted" => false, "alias" => "r.id"),
				"priority" => array("quoted" => false, "alias" => "mmr.priority"),
			));
		}

		$query .= " order by m.id, r.id, mmr.priority, mmr.update_time DESC";
		//echo $query;
		return $this->getRecordSet($query);
	}

	public function delete($input) {

		$updateFields = array(
			"remove_time" => array("quoted" => false)
		);

		$whereStmt = " WHERE remove_time IS NULL AND member_id = " . $input["member_id"] . " and role_id = " . $input["role_id"];
		
		return $this->genericUpdateRoutine($input, $updateFields, $whereStmt);
	}
}