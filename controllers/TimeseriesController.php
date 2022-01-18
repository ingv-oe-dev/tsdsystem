<?php

require_once("..\classes\SimpleREST.php");
require_once("..\classes\Timeseries.php");

// Timeseries class
Class TimeseriesController extends SimpleREST {
	
	private $ts;
	
	public function __construct() {
		$this->ts = new Timeseries();
		$this->route();
	}
	
	public function route() {

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->readInput();
			$this->post();
		}
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$this->getInput();
			$this->get();
		}
		/*
		if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
			$this->update();
		}
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$this->remove();
		}
		*/
		$this->elaborateResponse();
	}
	
	// ====================================================================//
	// ****************** post - timeseries instance **********************//
	// ====================================================================//
	
	public function post() {

		if ($this->check_input_post()) {

			$result = $this->ts->registration($this->getParams());
		
			if ($result["status"]) {
				$this->setData($result);
				if (isset($result["id"])) {
					$this->setStatusCode(201);
				} else {
					// Registrazione effettuata ma non sono riuscito a ritornare l'id della serie (id)
					$this->setStatusCode(206);
				}
			} else {
				$this->setStatusCode(409);
				$this->setError($result);
			}
		}
	}
	
	public function check_input_post() {
		
		if ($this->isEmptyInput()) {
			$this->setInputError("Empty input or malformed JSON");
			return false;
		}
		
		$input = $this->getParams();
		
		// (1) $input["schema"]
		if (!array_key_exists("schema", $input)){
			$this->setInputError("This required input is missing: 'schema' [string]");
			return false;
		}
		// (2) $input["name"] 
		if (!array_key_exists("name", $input)){
			$this->setInputError("This required input is missing: 'name' [string]");
			return false;
		}
		// (3) $input["columns"] 
		if (!array_key_exists("columns", $input) || !is_array($input["columns"])){
			$this->setInputError("This required input is missing: 'columns'[array]");
			return false;
		}
		// (4) $input["metadata"] is json
		if (array_key_exists("metadata", $input)){
			try {
				json_decode($input["metadata"]);
			}
			catch (Exception $e) {
				$this->setInputError("Error on decoding 'metadata' JSON input");
				return false;
			}
		}
		// default sampling value is null
		if (!array_key_exists("sampling", $input) || !is_int($input["sampling"]) || $input["sampling"] < 0) {
			$this->setInputError("This required input is missing: 'sampling'[integer > 0] <in seconds>");
			return false;
		}
		
		return true;
	}
	
	// ====================================================================//
	// ****************** get - timeseries instance(s) ********************//
	// ====================================================================//
	
	public function get() {
	
		$result = $this->ts->getList($this->getParams());
	
		if ($result["status"]) {
			for($i=0; $i<count($result["data"]); $i++) {
				$result["data"][$i]["metadata"] = json_decode($result["data"][$i]["metadata"]);
			}
			$this->setData($result["data"]);
		} else {
			$this->setStatusCode(404);
			$this->setError($result);
		}
	}
	
}
?>