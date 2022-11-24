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

	public function getDependencies($id, $transpose=true) {
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
		where t.id ='$id'";

		$result = $this->getRecordSet($query);
		
		if ($result["status"] and isset($result["data"]) and (count($result["data"]) > 0)) {

			if (!$transpose) return $result["data"];

			// transpose result and make unique id(s) for nets, channels, sensors
			$array_one = $result["data"];
			$array_two = $this->transpose($array_one);
			foreach ($array_two as $key => $item) {
				$array_two[$key] = array_unique($array_two[$key]);
			}
			return $array_two;
		}

		return null;
	}

	public function getColumnList($id, $addInfo=false) {

		$query = "with info as (
			select schema, name from " . $this->tablename . " where id = '$id'
		)
		SELECT column_name AS name, data_type AS type
		  FROM information_schema.columns
		 WHERE table_schema = (select schema from info)
		   AND table_name   = (select name from info)
		   and column_name <> '" . $this->getTimeColumnName() . "'
			 ;";

		$result = $this->getRecordSet($query);
		if ($result["status"]) {
			if ($addInfo) return $result["data"];
			$response = $this->transpose($result["data"]);
			if (array_key_exists("name", $response)) return $response["name"];
			return null;
		}
		return null;
	}

	public function getIDChannelList($id) {

		$query = "SELECT c.id, c.name, s.id AS sensor_id, s.name AS sensor_name, n.id AS net_id, n.name AS net_name " .
			" FROM " . $this->tablename . " t " .
			" INNER JOIN tsd_main.timeseries_mapping_channels tmc ON t.id = tmc.timeseries_id " . 
			" INNER JOIN tsd_pnet.channels c ON c.id = tmc.channel_id " . 
			" INNER JOIN tsd_pnet.sensors s ON s.id = c.sensor_id " . 
			" INNER JOIN tsd_pnet.nets n ON n.id = s.net_id " . 
			" WHERE t.id = '" . $id . "' AND t.remove_time IS NULL ";

		$result = $this->getRecordSet($query);
		if ($result["status"]) {
			$idList = array();
			foreach($result["data"] as $row) {
				array_push($idList, $row["id"]);
			}
			return $idList;
		}
		return null;
	}

	// ============== Retrieve tablename by timeseries id ======================
	protected function getTablename($id) {
		$response = $this->getList(array(
			"id" => $id
		));
		if ($response["status"] and count($response["data"]) > 0) {
			return $response["data"][0]["schema"] . "." . $response["data"][0]["name"];
		}
		return null;
	}

	// ============== Get info by timeseries id ======================
	public function getInfo($id) {
		$response = $this->getList(array(
			"id" => $id
		));
		if ($response["status"] and count($response["data"]) > 0) {
			unset($response["data"][0]["schema"]);
			unset($response["data"][0]["name"]);
			return $response["data"][0];
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
			$next_query = "INSERT INTO " . $this->tablename . " (schema, name, sampling, metadata, public, create_user) VALUES (
				'" . $input["schema"] . "',
				'" . $input["name"]. "',
				" . $input["sampling"] . ",
				" . (isset($input["metadata"]) ? ("'" . json_encode($input["metadata"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ",
				" . ((array_key_exists("public", $input) and isset($input["public"]) and $input["public"]) ? "true" : "false") . ",
				" . ((array_key_exists("create_user", $input) and isset($input["create_user"]) and is_numeric($input["create_user"])) ? strval($input["create_user"]) : "NULL") . "
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
			$input["id"] = $inserted_id;
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
					if (isset($input["mapping"]["force"]) and ($input["mapping"]["force"] === true or $input["mapping"]["force"] === "true" or intval($input["mapping"]["force"]) === 1)) {
						$next_query = "DELETE FROM " . $this->mapping_table . " WHERE timeseries_id = '" . $input["id"] . "'"; 	
						//echo $next_query;
						$stmt = $this->myConnection->prepare($next_query);
						$stmt->execute();
						$response["channel_id"]["deleted_rows"] = $stmt->rowCount();
					}
					if(is_array($input["mapping"]["channel_id"]) and count($input["mapping"]["channel_id"]) > 0) {
						$next_query = "INSERT INTO " . $this->mapping_table . " VALUES "; 
						foreach($input["mapping"]["channel_id"] as $index => $id) {
							$next_query .= " ('" . $input["id"] . "', " . strval($id) . "), "; 	
						}
						$next_query = rtrim($next_query, ", ");
						$next_query .= " ON CONFLICT (timeseries_id, channel_id) DO NOTHING";
						//echo $next_query;
						$stmt = $this->myConnection->prepare($next_query);
						$stmt->execute();
						$response["channel_id"]["rows"] = $stmt->rowCount();
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
		
		$query = "SELECT t.id, t.schema, t.name, t.sampling, t.public, TO_CHAR(t.last_time, '$this->OUTPUT_PSQL_ISO8601_FORMAT') AS last_time, t.metadata " .
			" FROM " . $this->tablename . " t " .
			" LEFT JOIN tsd_main.timeseries_mapping_channels tmc ON t.id = tmc.timeseries_id " . 
			" WHERE t.remove_time IS NULL ";

		if (isset($input) and is_array($input)) { 

			if (array_key_exists("public", $input) and isset($input["public"])) {
				$query .= " AND t.public = " . ($input["public"] ? 'true' : 'false');
			}

			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => true, "alias" => "t.id"),
				"name" => array("quoted" => true, "alias" => "t.name"),
				"metadata" => array("quoted" => true, "alias" => "t.metadata"),
				"schema" => array("quoted" => true, "alias" => "t.schema"),
				"channel_id" => array("id" => true, "quoted" => false, "alias" => "tmc.channel_id")
			));
		}

		$query .= " GROUP BY t.id";

		if (isset($input["sort_by"])) {
			$cols = explode(",", $input["sort_by"]);
			$query .= $this->composeOrderBy($cols, array(
				"id" => array("alias" => "t.id"),
				"schema" => array("alias" => "t.schema"),
				"name" => array("alias" => "t.name"),
				"sampling" => array("alias" => "t.sampling")
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
				"id" => $input["id"]
			));
			if (!$requested["status"] or count($requested["data"]) == 0) {
				$response["status"] = false;
				$response["rows"] = 0;
				return $response;
			}

			// start transaction
			$this->myConnection->beginTransaction();

			// update into timeseries table
			$next_query = "UPDATE " . $this->tablename . " SET ";
			if (isset($input["metadata"])) {
				$next_query .= " metadata = '" . json_encode($input["metadata"], JSON_NUMERIC_CHECK) . "', ";
			}
			if (isset($input["public"])) {
				$next_query .= " public = " . ($input["public"] ? "true" : "false") . ", ";
			}  
			if (array_key_exists("update_user", $input) and isset($input["update_user"]) and is_numeric($input["update_user"])) {
				$next_query .= " update_user = " . strval($input["update_user"]) . ", ";
			}
			if (array_key_exists("update_time", $input) and isset($input["update_time"])) {
				$next_query .= " update_time = " . strval($input["update_time"]) . ", ";
			}
			$next_query = rtrim($next_query, ", ");
			$next_query .= " WHERE id = '" . $input["id"] . "'";
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();	
				$stmt->execute();	
			$stmt->execute();	
			$response["rows"] = $stmt->rowCount();

			if (isset($input["sampling"])) {
				//update into timeseries table
				$next_query = "UPDATE " . $this->tablename . " SET sampling = 
					" . $input["sampling"] . " 
					WHERE id = '" . $input["id"] . "'";
				$stmt = $this->myConnection->prepare($next_query);
				$stmt->execute();	
				$response["rows"] = isset($response["rows"]) ? ($response["rows"] + $stmt->rowCount()) : $stmt->rowCount();

				// select schema and name from timeseries id
				$next_query = "SELECT schema, name FROM " . $this->tablename . " WHERE id = '" . $input["id"] . "'";
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