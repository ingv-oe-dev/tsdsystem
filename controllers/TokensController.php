<?php
require_once("RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."Tokens.php");

Class TokensController extends RESTController {
	
    // Token validity in days
    private $validity_days = 30;

    //CONSTRUCTOR
	function __construct() {
        parent::__construct();
        $this->route();
	}

    function route() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->setParams($_POST);
			$this->get();
		}
        $this->elaborateResponse();
    }

    /**
     * OVERRIDE RESTController 'get' function
    */
    public function get($jsonfields=null) {

        if (!$this->check_input()) {
            $this->setStatusCode(400);
            return;
        }

        $input = $this->getParams();
        $input["validity_days"] = $this->validity_days;

        $this->obj = new Tokens($input);
        
        if (!$this->obj->login_phase($input)) {
            $this->setStatusCode(401);
            return;
        }

        $myToken = $this->obj->generate();
        if (isset($myToken)) {
            $this->prepareResponse($myToken);
        }
        
    }

    public function check_input() {

        $input = $this->getParams();

        // check email
        if (!array_key_exists('email',$input)) {
            $this->setInputError("This required input is missing: 'email' [string]");
			return false;
        }

        // check password
        if (!array_key_exists('password',$input)) {
            $this->setInputError("This required input is missing: 'password' [string]");
			return false;
        }

        // check scope
        if (array_key_exists('scope',$input)) {
            if (!isset($input['scope']) or empty($input['scope']) or !in_array($input['scope'], $this->scopes)) {
                $this->setInputError("Choose 'scope' among the following: '" . implode("','", $this->scopes) . "'");
                return false;
            }
        }

        return true;
    }

    public function prepareResponse($myToken) {
        $this->response["token"] = $myToken;
        $this->setStatusCode(201);
    }

    public function elaborateResponse() {     
        unset($this->response["params"]);
        unset($this->response["data"]);
        parent::elaborateResponse();
    }
	
}