<?php
require_once("QueryManager.php");

// Timeseries class
Class Timeseries extends QueryManager {
	
	private $TIME_COLUMN_NAME = "time";
	private $tablename = "public.timeseries";
	
	public function getTimeColumnName() {
		return $this->TIME_COLUMN_NAME;
	}
	// ====================================================================//
	// ******************* TIMESERIES REGISTRATION ************************//
	// ====================================================================//
	
	public function registration($input) {
		
		// create schema
		$sql_commands = array(
			"CREATE SCHEMA IF NOT EXISTS " . $input["schema"] . ";"
		);
		
		// create table
		$create_table_sql = "CREATE TABLE IF NOT EXISTS " . $input["schema"] . "." . $input["name"] . " (" . $this->TIME_COLUMN_NAME . " TIMESTAMP WITHOUT TIME ZONE UNIQUE NOT NULL, ";
			// make columns
		for($i=0; $i<count($input["columns"]); $i++) {
			$create_table_sql .= $input["columns"][$i]["name"] . " " . $input["columns"][$i]["type"] . " NULL, ";
		}
		$create_table_sql = rtrim($create_table_sql, ", ");
		$create_table_sql .= ");";
		array_push($sql_commands, $create_table_sql);
		
		// create hypertable (TimescaleDB)
			// calculate chunk_time_interval
		$chunk_time_interval = $this->getChunkTimeInterval($input);
		$chunk_time_interval_string = isset($chunk_time_interval) ? ", chunk_time_interval => $chunk_time_interval" : "";
		array_push($sql_commands, "SELECT create_hypertable('" . $input["schema"] . "." . $input["name"] . "','" . $this->TIME_COLUMN_NAME . "'" . $chunk_time_interval_string . ", if_not_exists => TRUE)");
		
		// insert into timeseries table
		$reg_sql = "INSERT INTO " . $this->tablename . " (schema, name, sampling, metadata) 
			VALUES ('" . $input["schema"] . "','" . $input["name"]. "'," . $input["sampling"] . ",'" . json_encode($input["columns"], JSON_NUMERIC_CHECK) . "')";
		array_push($sql_commands, $reg_sql);		
		
		// execute sql commands
		//var_dump($sql_commands);
		$executeSQLCommand = $this->executeSQLCommand($sql_commands);
		
		// return result
		if (end($executeSQLCommand)["status"]) {
			// get inserted id
			$query = "SELECT id FROM " . $this->tablename . " WHERE schema = '" . $input["schema"] . "' AND name = '" . $input["name"] . "'";
			$inserted_id = $this->getSingleField($query);
			return array(
				"status" => true,
				"id" => $inserted_id["status"] ? $inserted_id["data"] : null,
				"warning" => $inserted_id["status"] ? null : $inserted_id["error"],
			);
		} else {
			return end($executeSQLCommand);
		}
		
	}
	
	function getChunkTimeInterval($input) {
		
		$sampling = intval($input["sampling"]);
		//echo $sampling;
		
		$chunk_time_interval = "INTERVAL '1 day'";
		if ($sampling > 1) $chunk_time_interval = "INTERVAL '1 week'";
		if ($sampling > 60) $chunk_time_interval = "INTERVAL '1 month'";
		if ($sampling > 300) $chunk_time_interval = "INTERVAL '1 year'";
		
		return $chunk_time_interval;
	}
	
	// ====================================================================//
	// *********************** TIMESERIES GET LIST ************************//
	// ====================================================================//
	
	public function getList($input) {
		
		$query = "SELECT id, schema, name, sampling, metadata FROM " . $this->tablename . " WHERE remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => true),
				"name" => array("quoted" => true),
				"schema" => array("quoted" => true)
			));
		}
		
		//echo $query;
		return $this->getRecordSet($query);
	}
	
	// ====================================================================//
	// *********************** TIMESERIES UPDATE **************************//
	// ====================================================================//
	// update json metadata example
	// -- update public.timeseries set metadata = jsonb_set(metadata, '{columns,0,unit}', '"C"') WHERE id = '7c82e5bb-37c7-4195-9a64-cb389140f795';
}
?>