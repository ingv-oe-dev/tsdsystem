<?php
require_once("QueryManager.php");

// Timeseries class
Class Timeseries extends QueryManager {
	
	private $TIME_COLUMN_NAME = "time";
	private $tablename = "tsd_main.timeseries";
	private $mapping_table = "tsd_main.timeseries_mapping_channels";
	
	public function getTimeColumnName() {
		return $this->TIME_COLUMN_NAME;
	}

	public function getDependencies($timeseries_id) {
		// mapping dependencies from timeseries to nets
		$query = "select
			tmc.timeseries_id, tmc.channel_id, c.sensor_id, s.net_id 
		from
			" . $this->tablename . " t
		left join tsd_main.timeseries_mapping_channels tmc on
			t.id = tmc.timeseries_id
		left join tsd_pnet.channels c on
			tmc.channel_id  = c.id
		left join tsd_pnet.sensors s on
			c.sensor_id = s.id
		left join tsd_pnet.nets n on
			s.net_id = n.id
		where t.id ='$timeseries_id'";

		$result = $this->getRecordSet($query);
		
		if ($result["status"] and isset($result["data"]) and (count($result["data"]) > 0)) {
			$array_one = $result["data"];
			$array_two = $this->transpose($array_one);
			foreach ($array_two as $key => $item) {
				$array_two[$key] = array_unique($array_two[$key]);
			}
			return $array_two;
		}

		return null;
	}

	public function getColumnList($timeseries_id) {

		$query = "with info as (
			select schema, name from " . $this->tablename . " where id = '$timeseries_id'
		)
		SELECT column_name 
		  FROM information_schema.columns
		 WHERE table_schema = (select schema from info)
		   AND table_name   = (select name from info)
		   and column_name <> '" . $this->getTimeColumnName() . "'
			 ;";

		$result = $this->getRecordSet($query);
		if ($result["status"]) {
			$response = $this->transpose($result["data"]);
			if (array_key_exists("column_name", $response)) return $response["column_name"];
			return null;
		}
		return null;
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
				if (isset($input["mapping"]["channel_id"])) {
					// delete old mappings, if forced
					if (isset($input["mapping"]["force"]) and $input["mapping"]["force"] === true) {
						$next_query = "DELETE FROM " . $this->mapping_table . " WHERE timeseries_id = '" . $input["timeseries_id"] . "'"; 	
						//echo $next_query;
						$stmt = $this->myConnection->prepare($next_query);
						$stmt->execute();
						$response["channel_id"]["deleted_rows"] = $stmt->rowCount();
					}
					$next_query = "INSERT INTO " . $this->mapping_table . " VALUES "; 
					foreach($input["mapping"]["channel_id"] as $index => $id) {
						$next_query .= " ('" . $input["timeseries_id"] . "', " . strval($id) . "), "; 	
					}
					$next_query = rtrim($next_query, ", ");
					$next_query .= " ON CONFLICT (timeseries_id, channel_id) DO NOTHING";
					//echo $next_query;
					$stmt = $this->myConnection->prepare($next_query);
					$stmt->execute();
					$response["channel_id"]["rows"] = $stmt->rowCount();
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

			// check if timeseries with input id exists
			$requested = $this->getList(array(
				"id" => $input["timeseries_id"]
			));
			if (!$requested["status"] or count($requested["data"]) == 0) {
				$response["status"] = false;
				$response["rows"] = 0;
				return $response;
			}

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

			if (isset($input["sampling"])) {
				//update into timeseries table
				$next_query = "UPDATE " . $this->tablename . " SET sampling = 
					" . $input["sampling"] . " 
					WHERE id = '" . $input["timeseries_id"] . "'";
				$stmt = $this->myConnection->prepare($next_query);
				$stmt->execute();	
				$response["rows"] = isset($response["rows"]) ? ($response["rows"] + $stmt->rowCount()) : $stmt->rowCount();

				// select schema and name from timeseries_id
				$next_query = "SELECT schema, name FROM " . $this->tablename . " WHERE id = '" . $input["timeseries_id"] . "'";
				$sqlResult = $this->myConnection->query($next_query);
				$record = $sqlResult->fetch(PDO::FETCH_ASSOC);	

				// calculate chunk_time_interval
				$chunk_time_interval = $this->getChunkTimeInterval($input);
				$next_query = "SELECT set_chunk_time_interval('" . $record["schema"] . "." . $record["name"] . "', " . $chunk_time_interval . ");";
				//echo $next_query;
				$stmt = $this->myConnection->prepare($next_query);
				$stmt->execute();
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