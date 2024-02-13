<?php
require_once("QueryManager.php");

Class Channels extends QueryManager {
	
	protected $tablename = "tsd_pnet.channels";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, station_config_id, additional_info, create_user) VALUES (
				'" . pg_escape_string($input["name"]) . "',
				" . $input["station_config_id"]. ", " .
				(isset($input["additional_info"]) ? ("'" . pg_escape_string(json_encode($input["additional_info"], JSON_NUMERIC_CHECK)) . "'") : "NULL") . ", 
				" . ((array_key_exists("create_user", $input) and isset($input["create_user"]) and is_int($input["create_user"])) ? $input["create_user"] : "NULL") . " 
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

		$query = "SELECT 
			c.id, 
			c.name, 
			c.station_config_id, 
			c.additional_info, 
			TO_CHAR(sc.start_datetime,'$this->OUTPUT_PSQL_ISO8601_FORMAT') as start_datetime, 
			TO_CHAR(sc.end_datetime,'$this->OUTPUT_PSQL_ISO8601_FORMAT') as end_datetime, 
			stat.id AS station_id, 
			stat.name AS station_name, 
			s.id AS sensor_id, 
			s.name AS sensor_name, 
			st.id AS sensortype_id,
			st.name AS sensortype_name, 
			d.id AS digitizer_id, 
			d.name AS digitizer_name, 
			dt.id AS digitizertype_id, 
			dt.name AS digitizertype_name, 
			dt.final_sample_rate,
			dt.final_sample_rate_measure_unit,
			dt.sensitivity,
			dt.sensitivity_measure_unit,
			dt.dynamical_range,
			dt.dynamical_range_measure_unit,
			n.id AS net_id, 
			n.name AS net_name, 
			(NOT sc.end_datetime IS NULL AND sc.end_datetime < now() at time zone 'utc') AS old_channel, 
			NULLIF(sc.remove_time, NULL) AS deprecated" . 
		" FROM " . $this->tablename . " c " . 
		" LEFT JOIN tsd_pnet.station_configs sc ON sc.id = c.station_config_id " .
		" LEFT JOIN tsd_pnet.sensors s ON s.id = sc.sensor_id " .
		" LEFT JOIN tsd_pnet.stations stat ON stat.id = sc.station_id " .
		" LEFT JOIN tsd_pnet.digitizers d ON d.id = sc.digitizer_id " .
		" LEFT JOIN tsd_pnet.sensortypes st ON st.id = s.sensortype_id " .
		" LEFT JOIN tsd_pnet.digitizertypes dt ON dt.id = d.digitizertype_id " .
		" LEFT JOIN tsd_pnet.nets n ON n.id = stat.net_id " .
		" WHERE c.remove_time IS NULL ";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "quoted" => false, "alias" => "c.id"),
				"name" => array("quoted" => true, "alias" => "c.name"),
				"station_config_id" => array("quoted" => false, "alias" => "c.station_config_id"),
				"sensor_id" => array("quoted" => false, "alias" => "sc.sensor_id"),
				"sensortype_id" => array("quoted" => false, "alias" => "s.sensortype_id"),
				"station_id" => array("quoted" => false, "alias" => "sc.station_id"),
				"digitizer_id" => array("quoted" => false, "alias" => "sc.digitizer_id"),
				"additional_info" => array("quoted" => true, "alias" => "c.additional_info"),
				"net_id" => array("quoted" => false, "alias" => "n.id"),
				"net_name" => array("quoted" => true, "alias" => "n.name"),
				"sensor_name" => array("quoted" => true, "alias" => "s.name"),
				"sensortype_name" => array("quoted" => true, "alias" => "st.name"),
				"station_name" => array("quoted" => true, "alias" => "stat.name"),
				"digitizer_name" => array("quoted" => true, "alias" => "d.name"),
				"digitizertype_name" => array("quoted" => true, "alias" => "dt.name")
			));
			// both start_datetime and end_datetime set
			if (
				array_key_exists("start_datetime", $input) and 
				isset($input["start_datetime"]) and 
				array_key_exists("end_datetime", $input) and 
				isset($input["end_datetime"])
			){
				$query .= " AND ((sc.start_datetime BETWEEN '" . $input["start_datetime"] . "' AND '" . $input["end_datetime"] . "') OR (sc.end_datetime BETWEEN '" . $input["start_datetime"] . "' AND '" . $input["end_datetime"] . "') OR (sc.start_datetime <= '" . $input["start_datetime"] . "' AND sc.end_datetime IS NULL) OR (sc.end_datetime >= '" . $input["end_datetime"] . "' AND sc.start_datetime IS NULL) OR (sc.start_datetime IS NULL AND sc.end_datetime IS NULL) OR (sc.start_datetime <= '" . $input["start_datetime"] . "' AND sc.end_datetime >= '" . $input["end_datetime"] . "'))";
			}
			// only start_datetime set
			else if (array_key_exists("start_datetime", $input) and isset($input["start_datetime"])){
				$query .= " AND (sc.end_datetime >= '" . $input["start_datetime"] . "' OR sc.end_datetime IS NULL)";
			}
			// only end_datetime set
			else if (array_key_exists("end_datetime", $input) and isset($input["end_datetime"])){
				$query .= " AND (sc.start_datetime <= '" . $input["end_datetime"] . "' OR sc.start_datetime IS NULL)";
			}
		}
		
		$query .= " ORDER BY n.name, stat.name, sc.end_datetime DESC, c.name";
		//echo $query;
		return $this->getRecordSet($query);
	}

	public function update($input) {

		$updateFields = array(
			"name" => array("quoted" => true),
			"station_config_id" => array("quoted" => false),
			"additional_info" => array("json" => true),
			"update_time" => array("quoted" => false),
			"update_user" => array("quoted" => false)
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
}