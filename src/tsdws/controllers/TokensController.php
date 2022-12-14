<?php
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
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
            if (isset($input['scope']) and !empty($input['scope'])) {
                if (!in_array($input['scope'], $this->scopes)) {
                    $this->setInputError("Choose 'scope' among the following: '" . implode("','", $this->scopes) . "'");
                    return false;
                }
            } else {
                unset($input["scope"]);
            }
        }

        // validity days
        if (array_key_exists('validity_days',$input)) {
            if (isset($input['validity_days']) and !empty($input['validity_days'])) {
                if (!is_numeric($input['validity_days'])) {
                    $this->setInputError("Uncorrect input 'validity_days' [integer]. Your value: " . $input['validity_days']);
                    return false;
                }
                $input["validity_days"] = intval($input["validity_days"]);
            } else {
                $input["validity_days"] = $this->validity_days;
            }
        } else {
            $input["validity_days"] = $this->validity_days;
        }

        $this->setParams($input);

        return true;
    }

    public function prepareResponse($myToken) {

        if ($myToken == -1) {
            $this->setError("No permissions found for the selected scope");
            $this->setStatusCode(401);
        } 
        else {
            $this->response["token"] = $myToken;
            $this->setStatusCode(201);
        }
    }

    public function elaborateResponse() {     
        unset($this->response["params"]);
        unset($this->response["data"]);
        parent::elaborateResponse();
    }
	
}