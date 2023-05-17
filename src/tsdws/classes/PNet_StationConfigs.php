<?php
require_once("QueryManager.php");

Class StationConfigs extends QueryManager {
	
	protected $tablename = "tsd_pnet.station_configs";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (station_id, sensor_id, digitizer_id, start_datetime, end_datetime, additional_info, create_user) VALUES (".
				(isset($input["station_id"]) ? $input["station_id"] : "NULL") . ", " .
				(isset($input["sensor_id"]) ? $input["sensor_id"] : "NULL") . ", " .
				(isset($input["digitizer_id"]) ? $input["digitizer_id"] : "NULL") . ", " .
				(isset($input["start_datetime"]) ? ("'" . $input["start_datetime"] . "'") : "NULL") . ", " .
				(isset($input["end_datetime"]) ? ("'" . $input["end_datetime"] . "'") : "NULL") . ", " .
				(isset($input["additional_info"]) ? ("'" . json_encode((object)$input["additional_info"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ",
				" . ((array_key_exists("create_user", $input) and isset($input["create_user"]) and is_int($input["create_user"])) ? $input["create_user"] : "NULL") . " 
			)";
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();
			$response["rows"] = $stmt->rowCount();
			$response["id"] = $this->myConnection->lastInsertId();
/*
			// automatically create channels for this new station config
			$next_query = "SELECT st.components FROM tsd_pnet.sensortypes st INNER JOIN tsd_pnet.sensors s ON st.id = s.sensortype_id 
			WHERE s.id = " . (isset($input["sensor_id"]) ? $input["sensor_id"] : "NULL");
			$sqlResult = $this->myConnection->query($next_query);
			
			$components = [];
			try {
				$fetchColumn = $sqlResult->fetchColumn();
				if (isset($fetchColumn)) {
					$components = json_decode($fetchColumn, true);
				}
			} catch (Exception $e) {
				$response["warning"] = "Unable to retrieve the number of components of the related sensortype. The number of the automatically created channels will be zero.";
			}
			$response["components"] = $components;

			if (is_array($components) and count($components)> 0) {
				$next_query = "INSERT INTO tsd_pnet.channels (name, station_config_id, create_user) VALUES ";
				foreach($components as $index => $name) {
					$next_query .= "(".
						"'" . ((isset($name) and !empty($name)) ? $name : ("ch".$index)) . "', " .
						$response["id"] . ", " .
						((array_key_exists("create_user", $input) and isset($input["create_user"]) and is_int($input["create_user"])) ? $input["create_user"] : "NULL") . " 
					), ";
				}
				$next_query = rtrim($next_query, ", ");
				$stmt = $this->myConnection->prepare($next_query);
				$stmt->execute();
				$response["created_channels"] = $stmt->rowCount();
			} else {
				$response["warning"] = "Unable to retrieve the number of components of the related sensortype. The number of the automatically created channels will be zero.";
			}
*/			
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

		$query = "SELECT 
			" . $this->tablename . ".id, 
			" . $this->tablename . ".station_id, 
			" . $this->tablename . ".sensor_id, 
			" . $this->tablename . ".digitizer_id, 
			TO_CHAR(" . $this->tablename . ".start_datetime,'$this->OUTPUT_PSQL_ISO8601_FORMAT') as start_datetime, 
			TO_CHAR(" . $this->tablename . ".end_datetime,'$this->OUTPUT_PSQL_ISO8601_FORMAT') as end_datetime, 
			" . $this->tablename . ".additional_info,
			stat.name as station_name, 
			s.name as sensor_name,
			st.id as sensortype_id,
			st.name as sensortype_name,
			d.name as digitizer_name,
			NULLIF(stat.remove_time, NULL) AS deprecated,
			(NOT " . $this->tablename . ".end_datetime IS NULL AND " . $this->tablename . ".end_datetime < now() at time zone 'utc') AS old_config,
			count(c.id) as n_channels
		FROM " . $this->tablename . " 
		LEFT JOIN tsd_pnet.stations stat on " . $this->tablename . ".station_id = stat.id
		LEFT JOIN tsd_pnet.sensors s on " . $this->tablename . ".sensor_id = s.id
		LEFT JOIN tsd_pnet.sensortypes st on s.sensortype_id = st.id
		LEFT JOIN tsd_pnet.digitizers d on " . $this->tablename . ".digitizer_id = d.id
		LEFT JOIN tsd_pnet.channels c on " . $this->tablename . ".id = c.station_config_id and c.remove_time is null
		WHERE " . $this->tablename . ".remove_time IS NULL";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "alias" => $this->tablename . ".id", "quoted" => false),
				"station_id" => array("alias" => $this->tablename . ".station_id", "quoted" => false),
				"station_name" => array("alias" => "stat.name", "quoted" => true),
				"additional_info" => array("quoted" => true, "alias" => $this->tablename . ".additional_info"),
				"sensor_id" => array("alias" => $this->tablename . ".sensor_id", "quoted" => false),
				"sensor_name" => array("alias" => "s.name", "quoted" => true),
				"digitizer_id" => array("alias" => $this->tablename . ".digitizer_id", "quoted" => false),
				"digitizer_name" => array("alias" => "d.name", "quoted" => true),
				"additional_info" => array("quoted" => true, "alias" => $this->tablename . ".additional_info")
			));
			if (array_key_exists("start_datetime", $input) and isset($input["start_datetime"])){
				$query .= " AND start_datetime >= '" . $input["start_datetime"] . "'";
			}
			if (array_key_exists("end_datetime", $input) and isset($input["end_datetime"])){
				$query .= " AND end_datetime <= '" . $input["end_datetime"] . "'";
			}
		}

		$query .= " group by " . $this->tablename . ".id, stat.name, s.name, st.id, st.name, d.name, stat.remove_time";

		if (isset($input) and is_array($input)) { 
			if (isset($input["sort_by"])) {
				$cols = explode(",", $input["sort_by"]);
				$query .= $this->composeOrderBy($cols, array(
					"id" => array("alias" => $this->tablename . ".id"),
					"station_id" => array("alias" => $this->tablename . ".station_id"),
					"sensor_id" => array("alias" => $this->tablename . ".sensor_id"),
					"digitizer_id" => array("alias" => $this->tablename . ".digitizer_id"),
					"start_datetime" => array("alias" => $this->tablename . ".start_datetime"),
					"end_datetime" => array("alias" => $this->tablename . ".end_datetime"),
					"station_name" => array("alias" => "stat.name"),
					"sensor_name" => array("alias" => "s.name"),
					"digitizer_name" => array("alias" => "d.name"),
				));
			}
		}
		
		//echo $query;
		$response = $this->getRecordSet($query);
		
		return $response;
	}

	public function update($input) {

		$updateFields = array(
			"sensor_id" => array("quoted" => false),
			"digitizer_id" => array("quoted" => false),
			"start_datetime" => array("quoted" => true),
			"end_datetime" => array("quoted" => true),
			"update_time" => array("quoted" => false),
			"update_user" => array("quoted" => false),
			"additional_info" => array("json" => true)
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

	public function generateChannels($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			// create channels for the new station config
			$next_query = "SELECT st.components 
				FROM tsd_pnet.sensortypes st 
				INNER JOIN tsd_pnet.sensors s ON st.id = s.sensortype_id  
				INNER JOIN " . $this->tablename . " sc ON sc.sensor_id = s.id 
				WHERE sc.id = " . $input["id"];
			$sqlResult = $this->myConnection->query($next_query);
			
			$components = [];
			try {
				$fetchColumn = $sqlResult->fetchColumn();
				if (isset($fetchColumn)) {
					$components = json_decode($fetchColumn, true);
				}
			} catch (Exception $e) {
				$response["warning"] = "Unable to retrieve the number of components of the related sensortype.";
			}
			$response["components"] = $components;

			if (is_array($components) and count($components)> 0) {

				// delete old channels
				$next_query = "UPDATE tsd_pnet.channels SET remove_time = " . $input["remove_time"] . ", remove_user = " . $input["remove_user"] . " WHERE station_config_id = " . $input["id"];
				$stmt = $this->myConnection->prepare($next_query);
				$stmt->execute();
				$response["removed_channels"] = $stmt->rowCount();
				
				// create new channels
				$next_query = "INSERT INTO tsd_pnet.channels (name, station_config_id, create_user) VALUES ";
				foreach($components as $index => $name) {
					$next_query .= "(".
						"'" . ((isset($name) and !empty($name)) ? $name : ("ch".$index)) . "', " .
						$input["id"] . ", " .
						((array_key_exists("create_user", $input) and isset($input["create_user"]) and is_int($input["create_user"])) ? $input["create_user"] : "NULL") . " 
					), ";
				}
				$next_query = rtrim($next_query, ", ");
				$stmt = $this->myConnection->prepare($next_query);
				$stmt->execute();
				$response["created_channels"] = $stmt->rowCount();

				// commit
				$this->myConnection->commit();

				$response["rows"] = $response["removed_channels"] + $response["created_channels"];
				$response["status"] = true;

			} else {

				// rollback
				$this->myConnection->rollback();

				$response["status"] = false;
			}			

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