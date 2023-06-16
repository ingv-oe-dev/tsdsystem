<?php
require_once("QueryManager.php");

Class Stations extends QueryManager {
	
	protected $tablename = "tsd_pnet.stations";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, coords, quote, net_id, site_id, additional_info, create_user) VALUES (".
				"'" . pg_escape_string($input["name"]) . "', " . 
				((isset($input["lon"]) and isset($input["lat"])) ? ("'POINT(" . $input["lon"] . " " . $input["lat"] . ")'::geometry") : "NULL") . ", " .
				(isset($input["quote"]) ? $input["quote"] : "NULL") . ", " .
				(isset($input["net_id"]) ? $input["net_id"] : "NULL") . ", " .
				(isset($input["site_id"]) ? $input["site_id"] : "NULL") . ", " .
				(isset($input["additional_info"]) ? ("'" . pg_escape_string(json_encode((object)$input["additional_info"], JSON_NUMERIC_CHECK)) . "'") : "NULL") . ",
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
			" . $this->tablename . ".id, 
			" . $this->tablename . ".name, 
			ST_AsGeoJSON(" . $this->tablename . ".coords) AS coords, 
			" . $this->tablename . ".quote, 
			" . $this->tablename . ".net_id, 
			" . $this->tablename . ".site_id, 
			" . $this->tablename . ".additional_info, 
			count(c.id) as n_channels, 
			TO_CHAR(stc.start_datetime,'$this->OUTPUT_PSQL_ISO8601_FORMAT') as start_datetime, 
			TO_CHAR(stc.end_datetime,'$this->OUTPUT_PSQL_ISO8601_FORMAT') as end_datetime, 
			s.sensortype_id, 
			st.name AS sensortype_name, 
			n.name AS net_name, 
			ss.name AS site_name, 
			NULLIF(n.remove_time, NULL) AS deprecated,
			(NOT stc.end_datetime IS NULL AND stc.end_datetime < now() at time zone 'utc') AS old_station
		FROM " . $this->tablename . "
		LEFT JOIN (
			SELECT DISTINCT ON (station_id) id, station_id, start_datetime, end_datetime, sensor_id 
			FROM tsd_pnet.station_configs 
			WHERE remove_time is null ";
			if (isset($input) and is_array($input)) { 
				if (array_key_exists("start_datetime", $input) and isset($input["start_datetime"])){
					$query .= " AND start_datetime >= '" . $input["start_datetime"] . "'";
				}
				if (array_key_exists("end_datetime", $input) and isset($input["end_datetime"])){
					$query .= " AND end_datetime <= '" . $input["end_datetime"] . "'";
				}
			}	
		$query .= " ORDER BY station_id, start_datetime DESC
		) stc on " . $this->tablename . ".id = stc.station_id 
		LEFT JOIN tsd_pnet.sensors s on stc.sensor_id = s.id
		LEFT JOIN tsd_pnet.sensortypes st on s.sensortype_id = st.id 
		LEFT JOIN tsd_pnet.channels c on stc.id = c.station_config_id and c.remove_time is null
		LEFT JOIN tsd_pnet.nets n ON " . $this->tablename . ".net_id = n.id 
		LEFT JOIN tsd_pnet.sites ss ON " . $this->tablename . ".site_id = ss.id 
		WHERE " . $this->tablename . ".remove_time IS NULL";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "alias" => $this->tablename . ".id", "quoted" => false),
				"name" => array("alias" => $this->tablename . ".name", "quoted" => true),
				"sensortype_id" => array("alias" => "s.sensortype_id", "quoted" => false),
				"sensortype_name" => array("alias" => "st.name", "quoted" => true),
				"net_id" => array("alias" => $this->tablename . ".net_id", "quoted" => false),
				"net_name" => array("alias" => "n.name", "quoted" => true),
				"site_id" => array("alias" => $this->tablename . ".site_id", "quoted" => false),
				"site_name" => array("alias" => "ss.name", "quoted" => true),
				"additional_info" => array("quoted" => true, "alias" => $this->tablename . ".additional_info")
			));
			if (array_key_exists("start_datetime", $input) and isset($input["start_datetime"])){
				$query .= " AND stc.start_datetime >= '" . $input["start_datetime"] . "'";
			}
			if (array_key_exists("end_datetime", $input) and isset($input["end_datetime"])){
				$query .= " AND stc.end_datetime <= '" . $input["end_datetime"] . "'";
			}
			$query .= $this->extendSpatialQuery($input, $this->tablename . ".coords");
		}

		$query .= " group by " . $this->tablename . ".id, st.name, n.name, ss.name, n.remove_time, stc.start_datetime, stc.end_datetime, s.sensortype_id  ";

		if (isset($input) and is_array($input)) { 
			if (isset($input["sort_by"])) {
				$cols = explode(",", $input["sort_by"]);
				$query .= $this->composeOrderBy($cols, array(
					"id" => array("alias" => $this->tablename . ".id"),
					"name" => array("alias" => $this->tablename . ".name"),
					"end_datetime" => array("alias" => "stc.end_datetime"),
					"start_datetime" => array("alias" => "stc.start_datetime")
				));
			}
		}
		
		//echo $query;
		$response = $this->getRecordSet($query);
		
		return $response;
	}

	public function update($input) {

		$updateFields = array(
			"name" => array("quoted" => true),
			"quote" => array("quoted" => false),
			"additional_info" => array("json" => true),
			"net_id" => array("quoted" => false),
			"site_id" => array("quoted" => false),
			"update_time" => array("quoted" => false),
			"update_user" => array("quoted" => false)
		);

		$input["coords"] = ((isset($input["lon"]) and isset($input["lat"])) ? ("'POINT(" . $input["lon"] . " " . $input["lat"] . ")'::geometry") : "NULL");
		if ($input["coords"] != "NULL") {
			$updateFields["coords"] = array("quoted" => false);
		}

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