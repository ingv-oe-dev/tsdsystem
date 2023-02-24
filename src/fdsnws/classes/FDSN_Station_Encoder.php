<?php 
require_once("Utils.php");
/*
An encoder for FDSN station webservices
*/
class FDSN_Station_Encoder extends FDSN_Station {

	// values from fieldnames of table/view defined in parent class => $this->tablename
	public $text_response_headers = array(
		"network" => array(
			"Network" => "net_name", 
			"Description" => "net_description", 
			"StartTime" => "net_startdate", 
			"EndTime" => "net_enddate", 
			"TotalStations" => "totalnumberstations"
		),
		"station" => array(
			"Network" => "net_name", 
			"Station" => "station_name", 
			"Latitude" => "station_latitude", 
			"Longitude" => "station_longitude", 
			"Elevation" => "station_elevation", 
			"SiteName" => "station_sitename", 
			"StartTime" => "station_startdate", 
			"EndTime" => "station_enddate"
		),
		"channel" => array(
			"Network" => "net_name", 
			"Station" => "station_name", 
			"location" => "station_sitename", 
			"Channel" => "channel_name", 
			"Latitude" => "station_latitude", 
			"Longitude" => "station_longitude", 
			"Elevation" => "station_elevation", 
			"Depth" => "channel_additional_info->Depth",
			"Azimuth" => "channel_additional_info->Azimuth", 
			"Dip" => "channel_additional_info->Dip", 
			"SensorDescription" => "sensortype_model", 
			"Scale" => "sensitivity", 
			"ScaleFreq" => "dynamical_range", 
			"ScaleUnits" => "sensitivity_measure_unit", 
			"SampleRate" => "final_sample_rate", 
			"StartTime" => "channel_startdate", 
			"EndTime" => "channel_enddate"
		)
	);

	public function okStatusCode($code) {
		return (intval($code) >= 200 and intval($code) < 300);
	}
	
	public function encodeResponse($response) {
		if (
			isset($response) and 
			is_array($response) and 
			array_key_exists("params", $response) and 
			is_array($response["params"]) and 
			array_key_exists("format", $response["params"])
		) {
			switch ($response["params"]["format"]) {
				case 'xml':
					return $this->encodeXml($response);
				case 'text':
					return $this->encodeText($response);
				case 'json':
					return $this->encodeJson($response);
				case 'geojson':
					return $this->encodeGeoJson($response);
			}
		}

		return json_encode($response, JSON_NUMERIC_CHECK);
	}

	public function encodeJson($response) {

		// creating object of SimpleXMLElement
		if ($this->okStatusCode($response["statusCode"])) {

			$xml = $this->create_FDSN_Station_XML_Body($response);

			// convert xml to json 
			return json_encode($xml, JSON_NUMERIC_CHECK);
		} 

		return json_encode($response, JSON_NUMERIC_CHECK);			
	}

	public function encodeGeoJson($response) {

		if ($this->okStatusCode($response["statusCode"])) {
			
			// init geojson structure
			$geojson = json_decode('{
				"type": "FeatureCollection",
				"features": []
			}', true);
			
			// setting initial vars
			$current = array(
				"station_id" => null
			);
			
			// loop result data
			foreach($response["data"] as $item) {
			
				if ($item["station_id"] == $current["station_id"]) continue;
				
				// update currents id
				$current["station_id"] = $item["station_id"];	

				// init geojson record
				$rec = json_decode('{
					"type": "Feature",
					"properties": {
						"code": "",
						"Name": "",
						"network": "",
						"startDate": "--",
						"endDate": "--",
						"Latitude": null,
						"Longitude": null,
						"Elevation": null
					},
					"geometry": {
						"type": "Point",
						"coordinates": []
					}
				}', true);

				if ($this->isSetArrayVal($item, "station_id")) $rec["properties"]["code"] = $item["station_id"];
				if ($this->isSetArrayVal($item, "station_name")) $rec["properties"]["Name"] = $item["station_name"];
				if ($this->isSetArrayVal($item, "net_name")) $rec["properties"]["network"] = $item["net_name"];
				if ($this->isSetArrayVal($item, "station_startdate")) $rec["properties"]["startDate"] = $item["station_startdate"];
				if ($this->isSetArrayVal($item, "station_enddate")) $rec["properties"]["endDate"] = $item["station_enddate"];
				if ($this->isSetArrayVal($item, "station_longitude")) $rec["properties"]["Longitude"] = $item["station_longitude"];
				if ($this->isSetArrayVal($item, "station_latitude")) $rec["properties"]["Latitude"] = $item["station_latitude"];
				if ($this->isSetArrayVal($item, "station_elevation")) $rec["properties"]["Elevation"] = $item["station_elevation"];

				if ($this->isSetArrayVal($item, "station_geojson_coords")) $rec["geometry"] = $item["station_geojson_coords"];

				array_push($geojson["features"], $rec);
			}	
			
			return json_encode($geojson, JSON_NUMERIC_CHECK);
			
		} else {
			return json_encode($response, JSON_NUMERIC_CHECK);
		}		
	}
	
	public function encodeText($response) {
		
		if (!$this->okStatusCode($response["statusCode"])) {
			$text = "Error " . $response["statusCode"] . "\r\n\r\n";
			$text .= "Bad request:\r\n\r\n";
			$text .= json_encode($response["error"]) . "\r\n";
			return $text;		
		} 
		
		// if here, correct request
		if (count($response["data"]) < 1) return "";
		
		// if here, not empty response
		$header = $this->text_response_headers[$response["params"]["level"]];
		$text = "#";
		$text .= implode(" | ",array_keys($header)) . "\r\n";

		// setting initial vars
		$current = array(
			"net_id" => null,
			"station_id" => null
		);

		foreach($response["data"] as $item) {
			
			// => [0] => Network level
			if ($item["net_id"] == $current["net_id"] and $response["params"]["level"] == "network") continue; 

			// => [1st level] => Station level
			if ($item["station_id"] == $current["station_id"] and $response["params"]["level"] == "station") continue;

			// update currents id
			$current["net_id"] = $item["net_id"];
			$current["station_id"] = $item["station_id"];	

			for($i=0; $i<count($header); $i++) {
				//var_dump($i);
				$fieldname_str = $header[array_keys($header)[$i]];
				// from json field
				if (str_contains($fieldname_str, "->")) {
					$pieces = explode("->", $fieldname_str);
					$fieldname = $pieces[0];
					$path = array_splice($pieces, 1);
					if ($this->isSetArrayVal($item, $fieldname)) {
						$value = $this->objectPath($item[$fieldname], $path);
						$text .= $this->sanitize($value);
					} else {
						$text .= "";
					}
				} else {
					if ($this->isSetArrayVal($item, $fieldname_str)) {
						$text .= $this->sanitize($item[$fieldname_str]);
					} else {
						$text .= "";
					}
				}
				if ($i<count($header)-1) $text .= "|";
			}
			$text .= "\r\n";
		}	
		return $text;

	}
	
	public function encodeXml($response) {
		//var_dump($response);

		// default response
		$xml = $this->defaultXML();

		// creating object of SimpleXMLElement
		if ($this->okStatusCode($response["statusCode"])) {
			$xml = $this->create_FDSN_Station_XML_Body($response);
		} 

		// on bad request
		if (isset($response["error"])) {
			$xml = $this->errorXML($response["error"]);
		} 

		return $xml->asXML();
	}

	public function create_FDSN_Station_XML_Body($response) {
		
		$xml = $this->defaultXML();

		// setting initial vars
		$current = array(
			"net_id" => null,
			"station_id" => null
		);
		$isChanged = array(
			"net_id" => null,
			"station_id" => null
		);
		
		// Loop result set
		if (in_array($response["params"]["level"], $this->levels)) {
			
			foreach($response["data"] as $item) {

				// => [0] => Network level
				if ($item["net_id"] != $current["net_id"]) {
					$netItem = $this->create_Network_XML_section($xml, $item);
					// update current net_id
					$current["net_id"] = $item["net_id"];
					$isChanged["net_id"] = true;
				} else {
					$isChanged["net_id"] = false;
				}

				// => [1st level] => Station level
				if (in_array($response["params"]["level"], array_slice($this->levels, 1))) {
					
					if ($isChanged["net_id"]) {
						$netItem->addChild("TotalNumberStations", $item["totalnumberstations"]);
						$netItem->addChild("SelectedNumberStations", $item["selectednumberstations"]);
					}

					if ($item["station_id"] != $current["station_id"]) {
						$stationItem = $this->create_Station_XML_section($netItem, $item);
						// update current station_id
						$current["station_id"] = $item["station_id"];
						$isChanged["station_id"] = true;
					} else {
						$isChanged["station_id"] = false;
					}
					
					// => [2nd level] => Channel level 
					if (in_array($response["params"]["level"], array_slice($this->levels, 2))) {
						
						if ($isChanged["station_id"]) {
							$stationItem->addChild("TotalNumberChannels", $item["totalnumberchannels"]);
							$stationItem->addChild("SelectedNumberChannels", $item["selectednumberchannels"]);
						}
						
						$channelItem = $this->create_Channel_XML_section($stationItem, $item);

						// => [3rd level] => Response level
						if (in_array($response["params"]["level"], array_slice($this->levels, 3))) {
							
							// retrieve Response section
							$responseItem = $channelItem->children()->Response;
							
							// to do
							$responseItem->addChild("todo"); // for testing
							// ...
							// ...
						}
					}
				}
			}
		}

		return $xml;
	}
	
	public function FDSN_Station_XML_Header() {
		return '
		<FDSNStationXML xmlns="http://www.fdsn.org/xml/station/1" schemaVersion="1.0" xsi:schemaLocation="http://www.fdsn.org/xml/station/1 http://www.fdsn.org/xml/station/fdsn-station-1.0.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"> 
			<Source>SeisNet-mysql</Source>
			<Sender>INGV-OE</Sender>
			<Module>INGV-OE WEB SERVICE: fdsnws-station | version: 1.0.0</Module>
			<ModuleURI>' . htmlspecialchars($this->getURI()) . '</ModuleURI>
			<Created>' . gmdate('Y-m-d\TH:i:s') . '</Created>
		</FDSNStationXML>';
	}

	public function sanitize($val) {
		return isset($val) ? $val : '';
	}

	public function isSetArrayVal($arr, $key) {
		return array_key_exists($key, $arr) and isset($arr[$key]);
	}

	public function defaultXML() {
		$root = '<?xml version="1.0" encoding="UTF-8"?>' . $this->FDSN_Station_XML_Header();
		$xml = new SimpleXMLElement($root);
		return $xml;
	}

	public function errorXML($error) {
		$root = '<?xml version="1.0" encoding="UTF-8"?><Bad_request></Bad_request>';
		$xml = new SimpleXMLElement($root);
		$xml->addChild("error", json_encode($error));
		return $xml;
	}

	public function create_Network_XML_section($xml, $item) {

		$netItem = $xml->addChild("Network");
		$netItem->addAttribute("code", $item["net_name"]);
		$netItem->addAttribute("alternateCode", $item["net_id"]);
		$netItem->addAttribute("startDate", $this->sanitize($item["net_startdate"]));
		if (isset($item["net_enddate"]))
			$netItem->addAttribute("endDate", $item["net_enddate"]);
		$netItem->addAttribute("restrictedStatus", "open");
		$netItem->addChild("Description", $this->sanitize($item["net_description"]));

		if (isset($item["net_additional_info"])) {
			$addInfo = $this->object_to_array(json_decode($item["net_additional_info"])); // convert to an associative array
			if ($this->isSetArrayVal($addInfo, "doi")) {
				$netItem->addChild("Identifier", strval($addInfo["doi"]));
			}
		}
		
		return $netItem;
	}

	public function create_Station_XML_section($netItem, $item) {

		$stationItem = $netItem->addChild("Station");
		$stationItem->addAttribute("code", $item["station_name"] );
		$stationItem->addAttribute("alternateCode", $item["station_id"] );
		$stationItem->addAttribute("startDate", $this->sanitize($item["station_startdate"]));
		if (isset($item["station_enddate"]))
			$stationItem->addAttribute("endDate", $item["station_enddate"]);
		$stationItem->addAttribute("restrictedStatus", "open");
		//$stationItem->addChild("Description", $this->sanitize($item["station_name"]));
		//$stationItem->addChild("Identifier", $this->sanitize($item["station_id"]));
		$stationItem->addChild("Latitude", $this->sanitize($item["station_latitude"]));
		$stationItem->addChild("Longitude", $this->sanitize($item["station_longitude"]));
		$stationItem->addChild("Elevation", $this->sanitize($item["station_elevation"]));
		$siteItem = $stationItem->addChild("Site");
		$siteItem->addAttribute("id", $this->sanitize($item["station_site_id"]));
		$siteItem->addChild("Name", $this->sanitize($item["station_sitename"]));
		
		return $stationItem;
	}

	public function create_Channel_XML_section($stationItem, $item) {

		$channelItem = $stationItem->addChild("Channel");
		$channelItem->addAttribute("code", $item["channel_name"] );
		$channelItem->addAttribute("alternateCode", $item["channel_id"] );
		$channelItem->addAttribute("startDate", $this->sanitize($item["channel_startdate"]));
		if (isset($item["channel_enddate"]))
			$channelItem->addAttribute("endDate", $item["channel_enddate"]);
		$channelItem->addAttribute("restrictedStatus", "open");
		$channelItem->addAttribute("locationCode", "");
		$channelItem->addChild("Description", $this->sanitize($item["channel_name"]));
		$channelItem->addChild("Identifier", $this->sanitize($item["channel_id"]));
		$channelItem->addChild("Latitude", $this->sanitize($item["station_latitude"]));
		$channelItem->addChild("Longitude", $this->sanitize($item["station_longitude"]));
		$channelItem->addChild("Elevation", $this->sanitize($item["station_elevation"]));

		// append sensor and digitizer info
		$channelItem->addChild("DataLogger")->addChild("Description", $item["digitizertype_model"]);
		$channelItem->addChild("Sensor")->addChild("Description", $item["sensortype_model"]);

		// append other properties
		$this->append_Channel_additionalInfo($channelItem, $item); // additional info
		
		$this->create_Response_XML_section($channelItem, $item); // response section

		return $channelItem;
	}

	public function create_Response_XML_section(&$channelItem, $item) {

		$responseItem = $channelItem->addChild("Response");
		$instrumentSensivityItem = $responseItem->addChild("InstrumentSensitivity");

		if (isset($item["response_parameters"])) {
			$addInfo = $this->object_to_array($item["response_parameters"]); // convert to an associative array
			
			if ($this->isSetArrayVal($addInfo, "K")) {
				$instrumentSensivityItem->addChild("Value", $this->sanitize($addInfo["K"]));
			}
			if ($this->isSetArrayVal($addInfo, "S")) {
				$instrumentSensivityItem->addChild("Frequency", $this->sanitize($addInfo["S"]));
			}
			if ($this->isSetArrayVal($addInfo, "PZ")) {
				$instrumentSensivityItem->addChild("InputUnits")->addChild("Name", $this->sanitize($addInfo["PZ"]));
			}
			if ($this->isSetArrayVal($addInfo, "fn")) {
				$instrumentSensivityItem->addChild("OutputUnits")->addChild("Name", $this->sanitize($addInfo["fn"]));
			}
		}
	}

	public function append_Channel_additionalInfo(&$channelItem, $item) {

		if (isset($item["channel_additional_info"])) {
			$addInfo = $this->object_to_array($item["channel_additional_info"]); // convert to an associative array

			if ($this->isSetArrayVal($addInfo, "Depth")) $channelItem->addChild("Depth", strval($addInfo["Depth"]));
			if ($this->isSetArrayVal($addInfo, "Azimuth")) $channelItem->addChild("Azimuth", strval($addInfo["Azimuth"]));
			if ($this->isSetArrayVal($addInfo, "Dip")) $channelItem->addChild("Dip", strval($addInfo["Dip"]));
			if ($this->isSetArrayVal($addInfo, "SampleRate")) $channelItem->addChild("SampleRate", strval($addInfo["SampleRate"]));
			if ($this->isSetArrayVal($addInfo, "ClockDrift")) $channelItem->addChild("ClockDrift", strval($addInfo["ClockDrift"]));
		}
	}
}

?>