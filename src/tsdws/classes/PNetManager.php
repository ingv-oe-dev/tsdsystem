<?php
require_once("QueryManager.php");

Class PNetManager extends QueryManager {

    public function getCreateUser($id) {
        $query = "SELECT create_user FROM " . $this->tablename . " WHERE id = '" . $id . "'";
		$result = $this->getSingleRecord($query);
		return (
			is_array($result) and 
			$result["status"] and
			array_key_exists("data", $result) and 
			is_array($result["data"]) and 
			array_key_exists("create_user", $result["data"])
		) ? 
		$result["data"]["create_user"] : null;
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