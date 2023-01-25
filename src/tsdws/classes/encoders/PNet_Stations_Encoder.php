<?php 
/*
An encoder for station webservices
*/
class PNet_Stations_Encoder {

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
				case 'json':
					return $this->encodeJson($response);
				case 'geojson':
					return $this->encodeGeoJson($response);
			}
		}

		return json_encode($response, JSON_NUMERIC_CHECK);
	}

	public function encodeJson($response) {
		return json_encode($response, JSON_NUMERIC_CHECK);			
	}

	public function encodeGeoJson($response) {

		if ($this->okStatusCode($response["statusCode"])) {
			
			// init geojson structure
			$geojson = json_decode('{
				"type": "FeatureCollection",
				"features": []
			}', true);
			
			// loop result data
			foreach($response["data"] as $item) {
			
				// init geojson record
				$rec = json_decode('{
					"type": "Feature",
					"properties": {
					},
					"geometry": {
						"type": "Point",
						"coordinates": []
					}
				}', true);

				$rec["geometry"] = $item["coords"];
				unset($item["coords"]);
				$rec["properties"] = $item;

				array_push($geojson["features"], $rec);
			}	
			
			return json_encode($geojson, JSON_NUMERIC_CHECK);
			
		} else {
			return json_encode($response, JSON_NUMERIC_CHECK);
		}		
	}

	public function isSetArrayVal($arr, $key) {
		return array_key_exists($key, $arr) and isset($arr[$key]);
	}
}

?>