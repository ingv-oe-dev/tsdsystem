<?php

require_once("..\classes\SimpleREST.php");

// Generic REST Controller class
Class RESTController extends SimpleREST {
	
	public $obj;
	
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
	// ****************** post - channel **********************//
	// ====================================================================//
	
	public function post() {

		if ($this->check_input_post()) {

			$result = $this->obj->insert($this->getParams());
		
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
	
	// ====================================================================//
	// ****************** get - channel(s) ********************//
	// ====================================================================//
	
	public function get($jsonfields=array()) {
	
		$result = $this->obj->getList($this->getParams());
	
		if ($result["status"]) {
			for($i=0; $i<count($result["data"]); $i++) {
				foreach($jsonfields as $fieldname) {
					$result["data"][$i][$fieldname] = isset($result["data"][$i][$fieldname]) ? json_decode($result["data"][$i][$fieldname]) : NULL;
				}
			}
			$this->setData($result["data"]);
		} else {
			$this->setStatusCode(404);
			$this->setError($result);
		}
	}
	
}
?>