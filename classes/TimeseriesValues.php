<?php
require_once("Timeseries.php");

// Timeseries class
Class TimeseriesValues extends Timeseries {
	
	// ============== Retrieve tablename by guid ======================
	private function getTablename($ts_guid) {
		$response = $this->getList(array(
			"id" => $ts_guid
		));
		if ($response["status"] and count($response["data"]) > 0) {
			return $response["data"][0]["schema"] . "." . $response["data"][0]["name"];
		}
		return null;
	}
	
	// ====================================================================//
	// ******************* insert - timeseries values ***********************//
	// ====================================================================//
	
	// ============== Handles inserting values process ======================
	public function insert_values($input) {
		
		$response = null;
		
		$insert_sql = array(
			"status"=> false
		);
		try {
			$insert_sql = $this->makeInsertSQL(
				$input["guid"], 
				$input["columns"], 
				$input["data"], 
				$input["insert"]
			);
		} catch (Exception $e) {
			$insert_sql["error"] = $e->getMessage();
		}
		
		if ($insert_sql["status"]) {
			$sql_response = $this->executeSQLCommand($insert_sql["sql"]);
			$response = end($sql_response);
			if ($response["status"]) {
				$input_params = explode(".", $insert_sql["tablename"]);
				$output = $this->executeSQLCommand("CALL public.\"updateTimeseriesLastTime\"('".$input_params[0]."','".$input_params[1]."')");
				$response["updatedTimeseriesTable"] = end($output)["status"];
			}
			// hide sql query into response
			unset($response["query"]);
		} else {
			$response = $insert_sql;
			$response["make_sql_error"] = true;
		}
		return $response;
	}
	
	// ============== Make insert SQL for postgresql ======================
	private function makeInsertSQL($guid, $columns, $data, $insert_mode="IGNORE") {

		$TIME_COLUMN_INDEX = array_search($this->getTimeColumnName(), $columns);
		
		$separator=", ";
		
		$tablename = $this->getTablename($guid);
		
		if (empty($tablename)) return array(
			"status"=> false, 
			"error" => "Timeseries with id=$guid not found"
		);
		
		$sql = "INSERT INTO " . $tablename . " (" . implode($separator, $columns) . ") VALUES ";
		
		for($i=0; $i<count($data); $i++) {
			
			// open row string
			$sql .= "(";
			
			// loop through columns
			for($j=0; $j<count($data[$i]); $j++) {
				
				$str_value = $data[$i][$j];
				
				if ($j == $TIME_COLUMN_INDEX) {
					// check datetime format
					if (!$this->verifyDate($str_value)) return $this->get_error("Invalid time format at row=" . strval($i) . ". Your value: '" . $str_value . "'");
					$str_value = "'" . $str_value . "'"; 
				} else {
					// check numeric format
					if (isset($str_value) && !is_numeric($str_value)) return $this->get_error("Not a number for field '" . $columns[$j] . "' at row=" . strval($i) . ". Your value: " . $str_value. "'");
				}
				// handle null values
				$value = (!isset($str_value) ? "NULL" : $str_value);
				
				$sql .= $value . $separator;
			}
			
			// trim last $separator on column
			$sql = rtrim($sql, $separator);
			
			// close row string
			$sql .= ")" . $separator;
		}
		// trim last $separator on row
		$sql = rtrim($sql, $separator);
		
		// handling on conflict case
		$sql .= " ON CONFLICT (" . $this->getTimeColumnName() . ") DO ";
		if(strtoupper($insert_mode) == "UPDATE") {
			$sql .= " UPDATE SET ";
			
			for($c=0; $c<count($columns); $c++) {
				if ($c != $TIME_COLUMN_INDEX) {
					$sql .= $columns[$c] . " = EXCLUDED." . $columns[$c] . $separator;
				}
			}
			
			// trim last $separator on column
			$sql = rtrim($sql, $separator);
			
		} else {
			$sql .= " NOTHING ";
		}
		//echo $sql;
		return array(
			"status"=> true, 
			"tablename" => $tablename, 
			"sql"=> $sql
		);
	}
	
	// ====================================================================//
	// ******************* select - timeseries values *********************//
	// ====================================================================//
	
	// ============== Handles selecting values process ======================
	public function select_values($input) {
		
		$query = $this->makeQuerySQL($input);
		
		$rs = $this->getRecordSet($query);
		
		if (array_key_exists("transpose", $input) and !$input["transpose"] and isset($rs["data"])){
			$rs["data"] = $this->transpose($rs["data"]);
		}
		
		return $rs;
	}
	
	// ============== Make select SQL for postgresql ======================
	private function makeQuerySQL($input) {
		
		$guid = $input["guid"];
		$columns = isset($input["columns"]) ? $input["columns"] : null;
		
		$separator=", ";
		
		$fields = is_array($columns) ? $this->getTimeColumnName() . ", " . implode($separator, $columns) : "*";
		
		$tablename = $this->getTablename($guid);
		
		$query = "SELECT " . $fields . " FROM " . $tablename . " WHERE true ";
		
		// starttime
		if (array_key_exists("starttime", $input)){
			$query .= " AND time >= '" . $input["starttime"] . "'";
		}
		
		// endtime
		if (array_key_exists("endtime", $input)){
			$query .= " AND time <= '" . $input["endtime"] . "'";
		}

		//echo $query;
		return $query;
	}
	
	
	
}
?>