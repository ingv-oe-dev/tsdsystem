<?php
require_once("Timeseries.php");

// Timeseries class
Class TimeseriesValues extends Timeseries {
		
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
				$input["id"], 
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
				$output = $this->executeSQLCommand("CALL tsd_main.\"updateTimeseriesLastTime\"('".$input_params[0]."','".$input_params[1]."')");
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
	private function makeInsertSQL($id, $columns, $data, $insert_mode="IGNORE") {

		$TIME_COLUMN_INDEX = array_search($this->getTimeColumnName(), $columns);
		
		$separator=", ";
		
		$tablename = $this->getTablename($id);
		
		if (empty($tablename)) return array(
			"status"=> false, 
			"error" => "Timeseries with id=$id not found"
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
		
		$output_column_time = "timestamp";

		$id = $input["id"];

		$time_bucket = $this->getTimeColumnName();
		
		if (isset($input["time_bucket"])) {
			$time_bucket = ("time_bucket_gapfill('" . $input["time_bucket"] . "', " . $this->getTimeColumnName() . ")");
			
			if (isset($input["starttime"]) and isset($input["endtime"])){
				$time_bucket = ("time_bucket_gapfill('" . $input["time_bucket"] . "', " . $this->getTimeColumnName() . ", '" . $input["starttime"] . "', '" . $input["endtime"] . "')");
			}
		}

		$columns = array();
		foreach($input["columns"] as $column) {
			$selector = "";
			// aggregate
			if (isset($input["time_bucket"])) {
				$aggregate = isset($column["aggregate"]) ? $column["aggregate"] : $input["aggregate"];
				if (strtoupper($aggregate) == "MEDIAN") {
					$selector .= "percentile_cont(0.5) within group (order by " . $column["name"] . ")";
				} else {
					$selector .= $aggregate . "(" . $column["name"] . ")";
				}
			} else {
				$selector .= $column["name"];
			}
			// gain
			$gain = isset($column["gain"]) ? $column["gain"] : (isset($input["gain"]) ? $input["gain"] : NULL);
			if (isset($gain)) {
				$selector .= " * $gain";
			}
			// offset
			$offset = isset($column["offset"]) ? $column["offset"] : (isset($input["offset"]) ? $input["offset"] : NULL);
			if (isset($offset)) {
				$selector .= " + $offset";
			}
			// alias for selected column
			$selector .= " AS " . $column["name"];
			array_push($columns, $selector);
		}
		
		$separator=", ";
		$fields = "$time_bucket AS $output_column_time, " . implode($separator, $columns);
		
		// get ts info()
		$ts_info = $this->getInfo($id);
		$tablename = $ts_info["schema"] . "." . $ts_info["name"];
		$hasTimeZone = $ts_info["with_tz"];
		$utc_string = $hasTimeZone ? " at time zone 'utc' " : "";
		
		// string containing the final query
		$query = "SELECT " . $fields . " FROM " . $tablename . " WHERE true ";

		// starttime
		if (isset($input["starttime"])){
			$query .= " AND " . $this->getTimeColumnName() . " $utc_string >= '" . $input["starttime"] . "'";
		}
		
		// endtime
		if (isset($input["endtime"])){
			$query .= " AND " . $this->getTimeColumnName() . " $utc_string <= '" . $input["endtime"] . "'";
		}

		// thresholds
		foreach($input["columns"] as $column) {
			// minthreshold
			$minthreshold = isset($column["minthreshold"]) ? $column["minthreshold"] : (isset($input["minthreshold"]) ? $input["minthreshold"] : NULL);
			if (isset($minthreshold)) {
				$query .= " AND (" . $column["name"] . " >= $minthreshold OR " . $column["name"] . " IS NULL)";
			}
			// maxthreshold
			$maxthreshold = isset($column["maxthreshold"]) ? $column["maxthreshold"] : (isset($input["maxthreshold"]) ? $input["maxthreshold"] : NULL);
			if (isset($maxthreshold)) {
				$query .= " AND (" . $column["name"] . " <= $maxthreshold OR " . $column["name"] . " IS NULL)";
			}
		}

		// group by
		if (isset($input["time_bucket"])){
			$query .= " GROUP BY $output_column_time";
		}

		$query .= " ORDER BY $output_column_time";

		// UNIX format
		if (isset($input["timeformat"]) and strtoupper($input["timeformat"]) == "UNIX") {
			$sup_query = "SELECT EXTRACT(EPOCH from t.$output_column_time) as $output_column_time";
			foreach($input["columns"] as $column) {
				$sup_query .= $separator . $column["name"];
			}
			$sup_query .= " FROM ( " . $query . ") t ";
			// assign to final query
			$query = $sup_query;
		} 
		// ISO8601 format
		else {
			$sup_query = "SELECT TO_CHAR(t.$output_column_time, '$this->OUTPUT_PSQL_ISO8601_FORMAT') as $output_column_time";
			foreach($input["columns"] as $column) {
				$sup_query .= $separator . $column["name"];
			}
			$sup_query .= " FROM ( " . $query . ") t ";
			// assign to final query
			$query = $sup_query;
		}

		//echo $query;
		return $query;
	}
	
	// ====================================================================//
	// ******************* delete - timeseries values *********************//
	// ====================================================================//
	public function delete_values($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {

			// get ts info()
			$ts_info = $this->getInfo($input["id"]);
			$tablename = $ts_info["schema"] . "." . $ts_info["name"];
			$hasTimeZone = $ts_info["with_tz"];
			$utc_string = $hasTimeZone ? " at time zone 'utc' " : "";

			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "DELETE FROM $tablename WHERE true ";

			// newer_than (starttime)
			if (isset($input["newer_than"])){
				$next_query .= " AND " . $this->getTimeColumnName() . " $utc_string >= '" . $input["newer_than"] . "'";
			}
			
			// older_than (endtime)
			if (isset($input["older_than"])){
				$next_query .= " AND " . $this->getTimeColumnName() . " $utc_string <= '" . $input["older_than"] . "'";
			}
			
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();		
			$response["rows"] = $stmt->rowCount();

			// update timeseries stats
			$next_query = "CALL tsd_main.\"updateTimeseriesLastTime\"('".$ts_info["schema"]."','".$ts_info["name"]."')";
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();

			$response["updatedTimeseriesTable"] = array(
				"status" => true,
				"query" => $next_query,
				"rows" => $stmt->rowCount()
			);

			// drop chunks
			$next_query = "SELECT drop_chunks('".$tablename."'";
				
			// older_than
			if (isset($input["older_than"])){
				$next_query .= ", older_than => '".$input["older_than"]."'";
			}

			// newer_than
			if (isset($input["newer_than"])){
				$next_query .= ", newer_than => '".$input["newer_than"]."'";
			}

			$next_query .= ")";

			// delete all rows
			if (!isset($input["older_than"]) and !isset($input["newer_than"])){
				$next_query = "SELECT drop_chunks('".$tablename."', interval '0 milliseconds')";

				// update timeseries stats
				$update_stats_query = "UPDATE tsd_main.timeseries SET last_time = NULL, last_value = NULL, n_samples = 0 WHERE schema = '".$ts_info["schema"]."' AND name = '".$ts_info["name"]."'";
				$stmt = $this->myConnection->prepare($update_stats_query);
				$stmt->execute();

				$response["updatedTimeseriesTable"] = array(
					"status" => true,
					"query" => $update_stats_query,
					"rows" => $stmt->rowCount()
				);
			}

			$response["drop_chunks"] = $this->getRecordSet($next_query);

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
}
?>