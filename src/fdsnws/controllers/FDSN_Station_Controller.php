<?php

require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."FDSN_Station.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."FDSN_Station_Encoder.php");

// FDSN Station Controller class
Class FDSN_Station_Controller extends RESTController {
	
	public function __construct() {
		$this->obj = new FDSN_Station();
		$this->route();
	}

	public function route() {

		switch ($_SERVER["REQUEST_METHOD"]) {
			
			case 'GET':
				$this->getInput();
				if (!$this->check_input_get()) break;
				$this->get();
				break;

			default:
				# code...
				break;
		}

		$this->elaborateResponse();
	}

	// ====================================================================//
	// ************* OVERRIDE SimpleREST->elaborateResponse() ************ //
	// ====================================================================//
	public function elaborateResponse() {
		
		// set header
		$this->setHttpHeaders($this->response["statusCode"]);

		// instantiation of Encoder class
		$encoder = new FDSN_Station_Encoder();

		// compress the response before send response
		ob_start("ob_gzhandler"); // start compression
		echo $encoder->encodeResponse($this->response);
		ob_end_flush(); // end compression
	}
	
	// ====================================================================//
	// ****************** get  ********************//
	// ====================================================================//
	public function check_input_get() {

		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();

		// $input["level"] 
		if (array_key_exists("level", $input)){
			$input["level"] = strtolower($input["level"]);
			if (!in_array($input["level"], $this->obj->levels)) {
				$this->setInputError("Uncorrect input: 'level' [available: " . implode(",", $this->obj->levels) . "]");
				return false;
			}
		} else {
			$input["level"] = "station"; // default
		}

		// $input["format"] 
		if (array_key_exists("format", $input)){
			if (!in_array(strtolower($input["format"]), array_keys($this->contentTypesArray))) {
				$this->setInputError("Uncorrect input: 'format' [available: " . implode(",", array_keys($this->contentTypesArray)) . "]. Your value: " . $input["format"]);
				return false;
			}
			switch ($input["format"]) {
				case "text":
					if ($input["level"] == "response") {
						$this->setInputError("format = text is only supported when level is network, station or channel");
						return false;
					}
					break;
				case "geojson":
					if ($input["level"] != "station") {
						$this->setInputError("format =  geojson is only supported when level is station");
						return false;
					}
					break;
				default:
					break;
			}
		} else {
			$input["format"] = "xml"; // default
		}

		// if here, 'format' input is set
		$input["contentType"] = $this->contentTypesArray[$input["format"]];

		// $input["network"] 
		if (array_key_exists("network", $input)){
			$input["network"] = explode(",", str_replace(' ', '', strtoupper($input["network"])));
		}
		// $input["station"] 
		if (array_key_exists("station", $input)){
			$input["station"] = explode(",", str_replace(' ', '', strtoupper($input["station"])));
		}
		// $input["channel"] 
		if (array_key_exists("channel", $input)){
			$input["channel"] = explode(",", str_replace(' ', '', strtoupper($input["channel"])));
		}

		// $input["includerestricted"]
		if(!array_key_exists("includerestricted", $input)) {
			$input["includerestricted"] = true;
		} else {
			$input["includerestricted"] = (intval($input["includerestricted"]) === 1 or $input["includerestricted"] === true or $input["includerestricted"] === "true");
		}

		$this->setParams($input);

		// check only if spatial inputs are defined and numerical
		return $this->check_spatial_input();
	}
	
	public function get($jsonfields=array("station_geojson_coords", "response_parameters", "channel_additional_info", "station_additional_info", "net_additional_info")) {
		parent::get($jsonfields);
	}
}
?>