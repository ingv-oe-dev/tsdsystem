<?php
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."RESTController.php");
require_once("..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."Tokens.php");

Class TokensController extends RESTController {
	
    // Token validity in seconds
    private $validity_seconds = 600; // 10 minutes, narrower than Tokens class default (86400: 1 day)

    //CONSTRUCTOR
	function __construct() {
        parent::__construct();
        $this->route();
	}

    function route() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->setParams($_POST);
			$this->get();
            $this->elaborateResponse();
		}
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $this->authorizedAction(array(
                "scope"=>"admin"
            ));
			$this->flush();
            parent::elaborateResponse();
		}
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

        // validity
        if (array_key_exists('validity_seconds',$input)) {
            if (isset($input['validity_seconds']) and !empty($input['validity_seconds'])) {
                if (!is_numeric($input['validity_seconds'])) {
                    $this->setInputError("Uncorrect input 'validity_seconds' [integer]. Your value: " . $input['validity_seconds']);
                    return false;
                }
                $input["validity_seconds"] = intval($input["validity_seconds"]);
            } else {
                $input["validity_seconds"] = $this->validity_seconds;
            }
        } else {
            $input["validity_seconds"] = $this->validity_seconds;
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

    public function flush() {
        
        $this->obj = new Tokens();

        $result = $this->obj->flushInvalidTokens();

        if ($result["status"]) {
			unset($result["query"]);
            $this->setData($result);
			if(isset($result["rows"]) and $result["rows"] > 0) {
				$this->setStatusCode(202);
			} else {
				$this->setStatusCode(207);
			}
		} else {
			$this->setStatusCode(409);
			$this->setError($result);
		}
    }

}