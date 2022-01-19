<?php
require_once("QueryManager.php");

// Timeseries class
Class Timeseries extends QueryManager {
	
	private $TIME_COLUMN_NAME = "time";
	private $tablename = "tsd_main.timeseries";
	private $mapping_tables = array(
		"sensor_id" => "tsd_main.timeseries_mapping_sensors",
		"channel_id" => "tsd_main.timeseries_mapping_channels"
	);
	
	public function getTimeColumnName() {
		return $this->TIME_COLUMN_NAME;
	}
	// ====================================================================//
	// ******************* TIMESERIES REGISTRATION ************************//
	// ====================================================================//
	
	public function insert($input) {
		
		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			// create schema
			$next_query = "CREATE SCHEMA IF NOT EXISTS " . $input["schema"];
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();
			
			// create table
			$next_query = "CREATE TABLE IF NOT EXISTS " . $input["schema"] . "." . $input["name"] . " (" . $this->TIME_COLUMN_NAME . " TIMESTAMP WITHOUT TIME ZONE UNIQUE NOT NULL, ";
				// make columns
			for($i=0; $i<count($input["columns"]); $i++) {
				$next_query .= $input["columns"][$i]["name"] . " " . $input["columns"][$i]["type"] . " NULL, ";
			}
			$next_query = rtrim($next_query, ", ");
			$next_query .= ");";
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();
			
			// create hypertable (TimescaleDB)
				// calculate chunk_time_interval
			$chunk_time_interval = $this->getChunkTimeInterval($input);
			$chunk_time_interval_string = isset($chunk_time_interval) ? ", chunk_time_interval => $chunk_time_interval" : "";
			$next_query = "SELECT create_hypertable('" . $input["schema"] . "." . $input["name"] . "','" . $this->TIME_COLUMN_NAME . "'" . $chunk_time_interval_string . ", if_not_exists => TRUE)";
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();
			
			// insert into timeseries table
			$next_query = "INSERT INTO " . $this->tablename . " (schema, name, sampling, metadata) VALUES (
				'" . $input["schema"] . "',
				'" . $input["name"]. "',
				" . $input["sampling"] . ",
				" . (isset($input["metadata"]) ? ("'" . json_encode($input["metadata"], JSON_NUMERIC_CHECK) . "'") : "NULL") . "
			) ON CONFLICT (LOWER(schema), LOWER(name)) DO NOTHING";
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();	
			
			$response["rows"] = $stmt->rowCount();
			
			// select inserted id
			$next_query = "SELECT id FROM " . $this->tablename . " WHERE LOWER(schema) = LOWER('" . $input["schema"] . "') AND LOWER(name) = LOWER('" . $input["name"] . "')";
			$sqlResult = $this->myConnection->query($next_query);
			$inserted_id = $sqlResult->fetchColumn();
			$response["id"] = $inserted_id;
			$response["status"] = true;
			
			// insert mappings
			$input["timeseries_id"] = $inserted_id;
			if ($response["rows"] > 0) {
				$mapping_result = $this->insertMappings($input);
				$response["mapping_result"] = $mapping_result;
				$response["status"] = $mapping_result["status"];
			}

			// commit
			$this->myConnection->commit();

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

	function insertMappings($input) {
		$response = array("status" => false);
		try {
			if (isset($input["mapping"])) {
				foreach($this->mapping_tables as $key => $value) {
					if (isset($input["mapping"][$key])) {
						// delete old mappings, if forced
						if (isset($input["mapping"]["force"]) and $input["mapping"]["force"] === true) {
							$next_query = "DELETE FROM " . $value . " WHERE timeseries_id = '" . $input["timeseries_id"] . "'"; 	
							//echo $next_query;
							$stmt = $this->myConnection->prepare($next_query);
							$stmt->execute();
							$response[$key]["deleted_rows"] = $stmt->rowCount();
						}
						$next_query = "INSERT INTO " . $value . " VALUES "; 
						foreach($input["mapping"][$key] as $index => $id) {
							$next_query .= " ('" . $input["timeseries_id"] . "', " . strval($id) . "), "; 	
						}
						$next_query = rtrim($next_query, ", ");
						$next_query .= " ON CONFLICT (timeseries_id, " . $key . ") DO NOTHING";
						//echo $next_query;
						$stmt = $this->myConnection->prepare($next_query);
						$stmt->execute();
						$response[$key]["rows"] = $stmt->rowCount();
					}
				}
			}
			$response["status"] = true;
		} catch (Exception $e) {}
		return $response;
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

	public function update($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			// update into timeseries table
			if (isset($input["metadata"])) {
				$next_query = "UPDATE " . $this->tablename . " SET metadata =
					'" . json_encode($input["metadata"], JSON_NUMERIC_CHECK) . "'  
					WHERE id = '" . $input["timeseries_id"] . "'";
				$stmt = $this->myConnection->prepare($next_query);
				$stmt->execute();	
				$response["rows"] = $stmt->rowCount();
			}
					
			// insert mappings
			$mapping_result = $this->insertMappings($input);
			
			// commit
			$this->myConnection->commit();

			$response["mapping_result"] = $mapping_result;
			$response["status"] = $mapping_result["status"];

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
	// update json metadata example
	// -- update public.timeseries set metadata = jsonb_set(metadata, '{columns,0,unit}', '"C"') WHERE id = '7c82e5bb-37c7-4195-9a64-cb389140f795';
}
?>