<?php
require_once("QueryManager.php");

Class Sensors extends QueryManager {
	
	protected $tablename = "tsd_pnet.sensors";
	
	public function insert($input) {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		try {
			// start transaction
			$this->myConnection->beginTransaction();

			$next_query = "INSERT INTO " . $this->tablename . " (name, coords, quote, net_id, site_id, custom_props, create_user) VALUES (".
				"'" . $input["name"] . "', " . 
				((isset($input["lon"]) and isset($input["lat"])) ? ("'POINT(" . $input["lon"] . " " . $input["lat"] . ")'::geometry") : "NULL") . ", " .
				(isset($input["quote"]) ? $input["quote"] : "NULL") . ", " .
				(isset($input["net_id"]) ? $input["net_id"] : "NULL") . ", " .
				(isset($input["site_id"]) ? $input["site_id"] : "NULL") . ", " .
				(isset($input["custom_props"]) ? ("'" . json_encode((object)$input["custom_props"], JSON_NUMERIC_CHECK) . "'") : "NULL") . ",
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

		$query = "SELECT s.id, s.name, ST_AsGeoJSON(s.coords) AS coords, s.quote, s.net_id, s.site_id, s.custom_props, case when (c.end_datetime is null or c.end_datetime > now() at time zone 'utc') then c.sensortype_id else null end as sensortype_id, count(c.id) as n_channels, TO_CHAR(c.start_datetime,'$this->OUTPUT_PSQL_ISO8601_FORMAT') as start_datetime, TO_CHAR(c.end_datetime,'$this->OUTPUT_PSQL_ISO8601_FORMAT') as end_datetime, st.name AS sensortype_name, n.name AS net_name, ss.name AS site_name, NULLIF(n.remove_time, NULL) AS deprecated FROM " . $this->tablename . " s left join tsd_pnet.channels c on
		s.id = c.sensor_id and c.remove_time is null and (c.end_datetime is null or c.end_datetime > now() at time zone 'utc') left join tsd_pnet.sensortypes st on c.sensortype_id = st.id LEFT JOIN tsd_pnet.nets n ON s.net_id = n.id LEFT JOIN tsd_pnet.sites ss ON s.site_id = ss.id WHERE s.remove_time IS NULL";
		
		if (isset($input) and is_array($input)) { 
			$query .= $this->composeWhereFilter($input, array(
				"id" => array("id" => true, "alias" => "s.id", "quoted" => false),
				"name" => array("alias" => "s.name", "quoted" => true),
				"sensortype_id" => array("alias" => "c.sensortype_id", "quoted" => false),
				"sensortype_name" => array("alias" => "st.name", "quoted" => true),
				"net_id" => array("alias" => "s.net_id", "quoted" => false),
				"net_name" => array("alias" => "n.name", "quoted" => true),
				"site_id" => array("alias" => "s.site_id", "quoted" => false),
				"site_name" => array("alias" => "ss.name", "quoted" => true),
				"custom_props" => array("quoted" => true, "alias" => "s.custom_props")
			));
			if (array_key_exists("start_datetime", $input) and isset($input["start_datetime"])){
				$query .= " AND c.start_datetime >= '" . $input["start_datetime"] . "'";
			}
			if (array_key_exists("end_datetime", $input) and isset($input["end_datetime"])){
				$query .= " AND c.end_datetime <= '" . $input["end_datetime"] . "'";
			}
			$query .= $this->extendSpatialQuery($input);
		}

		$query .= " group by s.id, st.name, n.name, ss.name, n.remove_time, c.start_datetime, c.end_datetime, c.sensortype_id  ";

		if (isset($input) and is_array($input)) { 
			if (isset($input["sort_by"])) {
				$cols = explode(",", $input["sort_by"]);
				$query .= $this->composeOrderBy($cols, array(
					"id" => array("alias" => "s.id"),
					"name" => array("alias" => "s.name"),
					"end_datetime" => array("alias" => "c.end_datetime")
				));
			}
		}
		
		//echo $query;
		$result = $this->getRecordSet($query);
		
		// check for invalid resultset (duplicated sensors id from channels with different sensortypes and/or start/end_datetime)
		$response = $this->sanitizeResult($result);

		return $response;
	}

	public function extendSpatialQuery($input) {
		return "";
	}
	
	public function sanitizeResult($result) {

		if (!$result["status"]) return $result;

		$duplicated_indexes = array();

		try {
			// prepare duplicated_indexes data structure
			foreach($result["data"] as $i => $current_item) {
				if (isset($duplicated_indexes[$current_item["id"]])) {
					array_push($duplicated_indexes[$current_item["id"]]["idx"], $i);
					if ($current_item["sensortype_id"] != $duplicated_indexes[$current_item["id"]]["sensortype_id"]) {
						$duplicated_indexes[$current_item["id"]]["sensortype_id"] = null;
						$duplicated_indexes[$current_item["id"]]["sensortype_name"] = "! Uncorrect mix of sensortypes - check its current Channels settings";
					}
					$duplicated_indexes[$current_item["id"]]["n_channels"] += $current_item["n_channels"];
				} else {
					$duplicated_indexes[$current_item["id"]] = array(
						"idx" => array($i),
						"sensortype_id" => $current_item["sensortype_id"],
						"sensortype_name" => $current_item["sensortype_name"],
						"n_channels" => $current_item["n_channels"]
					);
				}
			}

			// update $result using duplicated_indexes data structure
			foreach($duplicated_indexes as $i => $item) {
				if (count($item["idx"]) > 1) {
					$result["data"][$item["idx"][0]]["sensortype_id"] = $item["sensortype_id"];
					$result["data"][$item["idx"][0]]["sensortype_name"] = $item["sensortype_name"];
					$result["data"][$item["idx"][0]]["n_channels"] = $item["n_channels"];
					$result["data"][$item["idx"][0]]["start_datetime"] = null;
					$result["data"][$item["idx"][0]]["end_datetime"] = null;
					for($j=1; $j<count($item["idx"]); $j++) {
						$result["data"][$item["idx"][$j]] = null;
					}
				}
			}

			// prepare response with not null items of result
			$data = array();
			foreach($result["data"] as $item) {
				if (isset($item)) {
					array_push($data, $item);
				}
			}
			return array(
				"status" => true,
				"data" => $data
			);
			
		} catch(Exception $e) {
			return array(
				"status" => false,
				"error" => "Something gone wrong on sanitizing result: " . $e->getMessage()
			);
		}
	}

	public function update($input) {

		$updateFields = array(
			"name" => array("quoted" => true),
			"quote" => array("quoted" => false),
			"custom_props" => array("json" => true),
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