<?php
require_once("QueryManager.php");

Class FDSN_Station extends QueryManager {
	
	protected $tablename = "public.fdsn_station";

	public $levels = array("network", "station", "channel", "response");

	public function getList($input) {
		//var_dump($input);
		$filter_query = "";

		if (isset($input) and is_array($input)) { 
			
			// Limit to channels that are active on or after the specified start time.
			if (array_key_exists("starttime", $input) and isset($input["starttime"])){
				$filter_query .= " AND fdsn_station.channel_startdate >= '" . $input["starttime"] . "'";
			}
			// Limit to channels that are active on or before the specified end time.
			if (array_key_exists("endtime", $input) and isset($input["endtime"])){
				$filter_query .= " AND fdsn_station.channel_enddate <= '" . $input["endtime"] . "'";
			}
			// Limit to stations starting before the specified time.
			if (array_key_exists("startbefore", $input) and isset($input["startbefore"])){
				$filter_query .= " AND fdsn_station.station_startdate <= '" . $input["startbefore"] . "'";
			}
			// Limit to stations starting after the specified time.
			if (array_key_exists("startafter", $input) and isset($input["startafter"])){
				$filter_query .= " AND fdsn_station.station_startdate >= '" . $input["startafter"] . "'";
			}
			// Limit to stations ending before the specified time.
			if (array_key_exists("endbefore", $input) and isset($input["endbefore"])){
				$filter_query .= " AND fdsn_station.station_enddate <= '" . $input["endbefore"] . "'";
			}
			// Limit to stations ending after the specified time.
			if (array_key_exists("endafter", $input) and isset($input["endafter"])){
				$filter_query .= " AND fdsn_station.station_enddate >= '" . $input["endafter"] . "'";
			}
			// Select one or more network or virtual network codes. Lists and wildcards are accepted.
			if (array_key_exists("network", $input) and is_array($input["network"]) and count($input["network"]) > 0) {
				if (count($input["network"]) > 1) {
					$filter_query .= " AND UPPER(fdsn_station.net_name) IN ('" . implode("','", $input["network"]) . "')";
				} else {
					$filter_query .= " AND UPPER(fdsn_station.net_name) = '" . $input["network"][0] . "'";
				}
			}
			// Select one or more SEED station codes. Lists and wildcards are accepted.
			if (array_key_exists("station", $input) and is_array($input["station"]) and count($input["station"]) > 0) {
				if (count($input["station"]) > 1) {
					$filter_query .= " AND UPPER(fdsn_station.station_name) IN ('" . implode("','", $input["station"]) . "')";
				} else {
					$filter_query .= " AND UPPER(fdsn_station.station_name) = '" . $input["station"][0] . "'";
				}
			}
			// Select one or more SEED channel codes. Lists and wildcards are accepted.
			if (array_key_exists("channel", $input) and is_array($input["channel"]) and count($input["channel"]) > 0) {
				if (count($input["channel"]) > 1) {
					$filter_query .= " AND UPPER(fdsn_station.channel_name) IN ('" . implode("','", $input["channel"]) . "')";
				} else {
					$filter_query .= " AND UPPER(fdsn_station.channel_name) = '" . $input["channel"][0] . "'";
				}
			}
			// Spatial query
			$filter_query .= $this->extendSpatialQuery($input, "fdsn_station.station_coords");
		}
		
		$query = "select 
			fdsn_station.net_id, 
			fdsn_station.net_name,
			fdsn_station.net_startdate,
			fdsn_station.totalnumberstations,
			t1.selectednumberstations, 
			fdsn_station.station_id,
			fdsn_station.station_name,
			ST_AsGeoJSON(fdsn_station.station_coords) as station_geojson_coords,
			ST_X(fdsn_station.station_coords) as station_longitude, 
			ST_Y(fdsn_station.station_coords) as station_latitude, 
			fdsn_station.station_elevation,
			fdsn_station.station_site_id,
			fdsn_station.station_sitename,
			fdsn_station.station_startdate,
			fdsn_station.station_enddate,
			fdsn_station.totalnumberchannels,
			t2.selectednumberchannels,
			fdsn_station.channel_id,
			fdsn_station.channel_name,
			fdsn_station.channel_metadata,
			fdsn_station.channel_startdate,
			fdsn_station.channel_enddate,
			fdsn_station.channel_additionalinfo
		from $this->tablename fdsn_station 
		inner join (
			select net_id, count(net_id) as selectednumberstations from (
				select net_id, station_id, count(station_id) as selectednumberchannels
				from $this->tablename fdsn_station 
				where true $filter_query
				group by station_id, net_id
			) t 
			group by t.net_id
		) t1 on fdsn_station.net_id = t1.net_id 
		inner join (
			select station_id, count(station_id) as selectednumberchannels
			from $this->tablename fdsn_station 
			where true $filter_query
			group by station_id
		) t2 on fdsn_station.station_id = t2.station_id
		where true $filter_query";

		$query .= " ORDER BY fdsn_station.net_name, fdsn_station.station_name, fdsn_station.channel_startdate, fdsn_station.channel_name";

		//echo $query;
		return $this->getRecordSet($query);
	}
}