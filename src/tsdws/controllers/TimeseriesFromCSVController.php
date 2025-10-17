<?php
ini_set('memory_limit', '512M');
// Set the maximum execution time to 600 seconds (10 minutes)
ini_set('max_execution_time', '600');

require_once("..".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."TimeseriesController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."TimeseriesValues.php");

// Timeseries class
Class TimeseriesFromCSVController extends TimeseriesController {
	
	// Allowed delimiters
	public $allowed_delimiters = array(",", ";", "|");

	// Allowed mime types
	public $fileMimes = array(
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'text/plain'
    );

	public $insert_mode_array = array("IGNORE", "UPDATE");
	public $chunk_size = 10000;

	public function __construct() {
		
		// instantiate the object model
		$this->TimeseriesInstanceObject = new Timeseries();
		$this->TimeseriesValuesObject = new TimeseriesValues();
		
		// handle the request
		$this->route();
	}

	/* OVERRIDE */
	public function route() {
		
		switch ($_SERVER["REQUEST_METHOD"]) {

			case 'POST':
				$this->readInput();
				if (!$this->check_input_post()) break;
				// check if authorized action
				$this->authorizedAction(array(
					"scope"=>"timeseries-edit"
				));
				$this->post();
				break;
		}
		
		$this->elaborateResponse();
	}
	
	/* OVERRIDE */
	public function readInput() {
		$this->setParams(array_merge($_POST, $_FILES));
	}

	/* OVERRIDE */
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();

		// (0) $input["id"]
		if (array_key_exists("id", $input) and !$this->isValidUUID($input["id"])) {
			$this->setInputError("Uncorrect input: 'id' [uuid string]");
			return false;
		}
		// (1) $input["insert"] 
		if (array_key_exists("insert", $input)){
			if (!in_array(strtoupper($input["insert"]), $this->insert_mode_array)) {
				$this->setInputError("Uncorrect input: 'insert' [available: " . implode(",", $this->insert_mode_array) . "]");
				return false;
			}
		} else {
			$input["insert"] = "IGNORE";
		}
		
		if (!array_key_exists("id", $input)) {
			// (2) $input["schema"]
			if (!array_key_exists("schema", $input) or empty($input["schema"])){
				$this->setInputError("Uncorrect input: 'schema' [string]");
				return false;
				if (!$this->verifySecureDBString($input["schema"])) {
					$this->setInputError("Uncorrect input: 'schema' [string]. Accept only lowercase letters followed by numbers and underscore. Regular expression: $this->SECURE_DB_STRING_REGEX");
					return false;
				}
				// force schema to lowercase
				$input["schema"] = strtolower($input["schema"]);
			}
			
			// (3) $input["name"] 
			if (!array_key_exists("name", $input) or empty($input["name"])){
				$this->setInputError("Uncorrect input: 'name' [string]");
				return false;
				if (!$this->verifySecureDBString($input["name"])) {
					$this->setInputError("Uncorrect input: 'name' [string]. Accept only lowercase letters followed by numbers and underscore. Regular expression: $this->SECURE_DB_STRING_REGEX");
					return false;
				}
				// force name to lowercase
				$input["name"] = strtolower($input["name"]);
			}
			
			// (4) $input["sampling"]
			if (array_key_exists("sampling", $input)) {
				if (!is_numeric($input["sampling"]) || intval($input["sampling"]) <= 0) {
					$this->setInputError("Uncorrect input: 'sampling'[integer > 0] <in seconds>");
					return false;
				}
				$input["sampling"] = intval($input["sampling"]);
			} else {
				// set default sampling value to 60 seconds
				$input["sampling"] = 60;
			}
			
			// (5) $input["public"] 
			if (array_key_exists("public", $input)) {
				if (!(
					intval($input["public"]) === 1 or 
					$input["public"] === true or 
					$input["public"] === "true" or
					intval($input["public"]) === 0 or 
					$input["public"] === false or 
					$input["public"] === "false"
				)) {
					$this->setInputError("Uncorrect input: 'public' [boolean]");
					return false;
				} else {
					$input["public"] = (intval($input["public"]) === 1 or $input["public"] === true or $input["public"] === "true");
				}
			} else {
				$input["public"] = true;
			}

			// (5) $input["with_tz"] 
			if (array_key_exists("with_tz", $input)) {
				if (!(
					intval($input["with_tz"]) === 1 or 
					$input["with_tz"] === true or 
					$input["with_tz"] === "true" or
					intval($input["with_tz"]) === 0 or 
					$input["with_tz"] === false or 
					$input["with_tz"] === "false"
				)) {
					$this->setInputError("Uncorrect input: 'with_tz' [boolean]");
					return false;
				} else {
					$input["with_tz"] = (intval($input["with_tz"]) === 1 or $input["with_tz"] === true or $input["with_tz"] === "true");
				}
			} else {
				$input["with_tz"] = false;
			}
		}
		
		// (6) Validate uploaded file
		if (!$this->validateUploadFile($input)) return false;

		// (7) $input["delimiter"]
		if (array_key_exists("delimiter", $input)) {
			if (!isset($input["delimiter"]) || !in_array($input['delimiter'], $this->allowed_delimiters)) {
				$this->setInputError("Uncorrect input 'delimiter'. Must be a value in the following list: " . implode(", ", $this->allowed_delimiters) . ". Your value = " . strval($input["delimiter"]));
				return false;
			}
		} else {
			// set default delimiter to comma
			$input["delimiter"] = ",";
		}

		// (8) $input["columns"] 
		$input = array_merge($input, $this->retrieveColumns($input)); // from CSV file
		$timeColumnName = $this->TimeseriesInstanceObject->getTimeColumnName();
		if (!in_array($timeColumnName, $input["colnames"])){
			$this->setInputError("This required column is missing: '" . $timeColumnName . "'. Your columns:" . implode(",", $input["colnames"]));
			return false;
		}

		// (9) $input["metadata"]
		$input["metadata"] = array("columns" => $input["columns"]);
		
		$this->setParams($input);

		return true;
	}

	public function validateUploadFile($input) {
		
		$max_size = $this->file_upload_max_size();
		
		if (!isset($input["file"])) {
			$this->setInputError("There is no file to upload.");
			return false;
		}
		if (empty($input['file']['name'])) {
			$this->setInputError("Uncorrect upload file. Empty name found.");
			return false;
		}	
		if (empty($input['file']['tmp_name']) || $input['file']['size'] == 0 || $input['file']['error'] == 1) {
			$this->setInputError("Error on upload file. It may be larger than " . strval($max_size) . " bytes");
			return false;
		}	
		if ($input['file']['size'] > $max_size) {
			$this->setInputError("File size exceeds max size: " . strval($this->file_upload_max_size()) . " bytes");
			return false;
		}
		if (!in_array($input['file']['type'], $this->fileMimes)) {
			$this->setInputError("Uncorrect upload file.  Must be a value in the following list: " . implode(", ", $this->fileMimes) . ". Your value: " . $input['file']['type']);
			return false;
		}

		$filepath = $input['file']['tmp_name'];
		$fileSize = filesize($filepath);
		
		if ($fileSize === 0) {
			$this->setInputError("The file is empty.");
			return false;
		}

		return true;
	}

	// Retrieve columns from CSV file
	public function retrieveColumns($input) {
		
		$timeIdx = 0;
		$columns = array();
		$colnames = array();

		try {
			// Open uploaded CSV file with read-only mode
			$csvFile = fopen($input['file']['tmp_name'], 'r');

			// Read first line (header columns)
			$header = fgetcsv($csvFile, null, $input["delimiter"]);

			foreach($header as $key=>$val) {
				if (!in_array($val, array("time"))) {
					array_push($columns, array(
						"name" => preg_replace("/\'\"/", "", trim($val)),
						"type" => "double precision"
					));
				} else {
					$timeIdx = $key;
				}
				array_push($colnames, $val);
			}
		}
		finally {
			// Close opened CSV file
			fclose($csvFile);

			// set input
			return array(
				"timeIdx" => $timeIdx,
				"columns" => $columns,
				"colnames" => $colnames
			);
		}
	}

	// Read data from CSV file
	public function readData($input, $offset=0) {
		
		$data = array();

		try {
			// Open uploaded CSV file with read-only mode
			$csvFile = fopen($input['file']['tmp_name'], 'r');

			// Read first line (header columns)
			$header = fgetcsv($csvFile, null, $input["delimiter"]);

			// Read chunk by offset
			$counter = 0;
			while ($counter < ($offset + $this->chunk_size) and ($row = fgetcsv($csvFile, null, $input["delimiter"])) !== FALSE) {
				
				if ($counter < $offset) {
					$counter++;
					continue;
				}

				$arr = array();
				foreach($row as $idx=>$val) {
					if ($idx != $input["timeIdx"]) {
						$numval = preg_replace("/[^0-9.\-\+]/", "", $val);
						$val =  $numval == "" ? null : floatval($numval);
					}
					array_push($arr, $val);
				}
				array_push($data, $arr);
				$counter++;
			}
		}
		finally {
			// Close opened CSV file
			fclose($csvFile);
		}

		return array(
			"data" => $data,
			"offset" => $counter
		);
	}

	/* OVERRIDE */
	public function post() {

		$input = $this->getParams();
		$auth_data = $this->_get_auth_data();
		$input["create_user"] = isset($auth_data) ? $auth_data["userId"] : NULL;

		if (!isset($input["id"])) {
			// register timeseries
			$reg_result = $this->TimeseriesInstanceObject->insert($input);
		
			if ($reg_result["status"]) {
				$this->response["data"]["registration"] = $reg_result;
				$input["id"] = $reg_result["id"];
				if ($reg_result["rows"] == 0) {
					$this->setStatusCode(400);
					$this->setError("There is a timeseries registered with this name [schema.name]. Use the timeseries id instead.");
					return;
				}
			} else {
				$this->setStatusCode(410);
				$this->setError($reg_result);
				return;
			}
		}

		// prepare response
		$this->response["data"]["insertion"] = array(
			"inserted_rows" => 0,
			"chunks" => array()
		);

		// Data Insertion
		$offset = 0;
		do {
			$read_data = $this->readData($input, $offset);
			
			// insert data by chunks
			$input["data"] = $read_data["data"];
			$offset = $read_data["offset"];
			$read_lines = count($input["data"]);

			$input["columns"] = $input["colnames"]; // passo le colonne come previsto dalla classe TimeseriesValues

			$insert_result = $this->TimeseriesValuesObject->insert_values($input);
			$insert_result["chunk_idx"] = $offset;
			$insert_result["chunk_size"] = $read_lines;
			
			// evito di aggiungere l'output della query fallita per intero (solo i primi 100 caratteri)
			unset($insert_result["failed_query"]);
			unset($insert_result["next_queries"]);

			array_push($this->response["data"]["insertion"]["chunks"], $insert_result);

			if ($insert_result["status"]) {
				$this->setStatusCode(201);
				$this->response["data"]["insertion"]["inserted_rows"] += $insert_result["rows"];
				$this->response["data"]["insertion"]["n_chunks"] = count($this->response["data"]["insertion"]["chunks"]);
				// In questa modalita non vengono aggiornate le statistiche sulla serie temporale
				/*
				if (array_key_exists("updateTimeseriesLastTime", $insert_result) and array_key_exists("status", $insert_result["updateTimeseriesLastTime"]) and !$insert_result["updateTimeseriesLastTime"]["status"]) {
					// Non sono state aggiornate le statistiche sulla serie temporale
					$this->setStatusCode(202);
				}
				*/
				if ($this->response["data"]["insertion"]["inserted_rows"] == 0) {
					$this->setStatusCode(207);
				}
			} else {
				if (array_key_exists("make_sql_error", $insert_result) and $insert_result["make_sql_error"]) {
					$this->setError($insert_result["error"]);
					$this->setStatusCode(400);
				} else {
					$this->setError($insert_result["error"]);
					$this->setStatusCode(500);
				}
				return;
			}
		}
		while ($read_lines == $this->chunk_size);
	}
}
?>