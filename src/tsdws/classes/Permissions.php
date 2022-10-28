<?php
require_once("QueryManager.php");

Class Permissions extends QueryManager {

    protected $members_tablename = "tsd_users.members_permissions";
    protected $member_id_fieldname = "member_id";
    protected $roles_tablename = "tsd_users.roles_permissions";
    protected $role_id_fieldname = "role_id";

    public $tablename;
    public $id_fieldname;

    public const MEMBER_TYPE = 'member';
    public const ROLE_TYPE = 'role';

    //CONSTRUCTOR
	function __construct($role_type) {
        
        parent::__construct();

        $this->tablename = ($role_type == self::ROLE_TYPE) ? $this->roles_tablename : $this->members_tablename;
        $this->id_fieldname = ($role_type == self::ROLE_TYPE) ? $this->role_id_fieldname : $this->member_id_fieldname;
	}

    public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

            $next_query = "UPDATE " . $this->tablename . " 
                SET remove_time = timezone('utc'::text, now()) 
                WHERE " . $this->id_fieldname . " = " . $input["role_id"]; 
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();

			$next_query = "INSERT INTO " . $this->tablename . " (" . $this->id_fieldname . ", settings, active) VALUES (
				'" . $input["role_id"] . "', " . 
				(isset($input["settings"]) ? ("'" . json_encode($input["settings"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ",
				" . ((array_key_exists("active", $input) and isset($input["active"]) and $input["active"]) ? "true" : "false") . "
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
		
		$query = "SELECT id, " . $this->id_fieldname . " AS role_id, settings, active 
            FROM " . $this->tablename . " WHERE remove_time IS NULL ";
		
		if (isset($input) and is_array($input) and array_key_exists("role_id", $input) and is_numeric($input["role_id"])) { 
			$query .= (" AND " . $this->id_fieldname . " = " . $input["role_id"]);
		}
		
		//echo $query;
		return $this->getRecordSet($query);
	}
	
	public function delete($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			$next_query = "UPDATE " . $this->tablename . " SET remove_time = timezone('utc'::text, now()) ";

			if (array_key_exists("role_id", $input)) {
                $next_query .= " WHERE " . $this->id_fieldname . " = " . $input["role_id"]; 
			}

			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();
			$response["rows"] = $stmt->rowCount();
			$response["status"] = true;

			// return result
			return $response;
		}
		catch (Exception $e){
			return array(
				"status" => false,
				"error" => $e->getMessage()
			);
		}
	}
}