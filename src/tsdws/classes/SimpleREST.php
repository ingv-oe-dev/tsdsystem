<?php
require_once("Utils.php");
/*
A simple RESTful webservices base class
Use this as a template and build upon it
*/
class SimpleREST extends Utils{
	
	private $contentType = 'application/json';
	private $httpVersion = "HTTP/1.1";
	public $JWT_payload = null;
	public $response = array(
		"params" => null,
		"data" => null,
		"error" => null,
		"statusCode" => 200
	);	
	public $httpStatus = array(
		100 => 'Continue',  
		101 => 'Switching Protocols',  
		200 => 'OK',
		201 => 'Created',  
		202 => 'Accepted',  
		203 => 'Non-Authoritative Information',  
		204 => 'No Content',  
		205 => 'Reset Content',  
		206 => 'Partial Content',
		207 => 'No Rows inserted', 
		300 => 'Multiple Choices',  
		301 => 'Moved Permanently',  
		302 => 'Found',  
		303 => 'See Other',  
		304 => 'Not Modified',  
		305 => 'Use Proxy',  
		306 => '(Unused)',  
		307 => 'Temporary Redirect',  
		400 => 'Bad Request',  
		401 => 'Unauthorized',  
		402 => 'Payment Required',  
		403 => 'Forbidden',  
		404 => 'Not Found',  
		405 => 'Method Not Allowed',  
		406 => 'Not Acceptable',  
		407 => 'Proxy Authentication Required',  
		408 => 'Request Timeout',  
		409 => 'Conflict',  
		410 => 'Gone',  
		411 => 'Length Required',  
		412 => 'Precondition Failed',  
		413 => 'Request Entity Too Large',  
		414 => 'Request-URI Too Long',  
		415 => 'Unsupported Media Type',  
		416 => 'Requested Range Not Satisfiable',  
		417 => 'Expectation Failed',  
		500 => 'Internal Server Error',  
		501 => 'Not Implemented',  
		502 => 'Bad Gateway',  
		503 => 'Service Unavailable',  
		504 => 'Gateway Timeout',  
		505 => 'HTTP Version Not Supported',
		600 => 'Connection Refused'
	);
	
	public function setStatusCode($statusCode) {
		$this->response["statusCode"] = $statusCode;
	}
	
	public function setError($e) {
		$this->response["error"] = $e;
	}
	
	public function getParams() {
		return $this->response["params"];
	}
	
	public function setParams($params) {
		$this->response["params"] = $params;
	}
	
	public function setData($data) {
		$this->response["data"] = $data;
	}
	
	public function readInput() {
		// ======= Get input - Handle also Postman TEST!!!!! ========
		try {
			$input = json_decode(file_get_contents('php://input'), true);
			$this->setParams($input);
		}
		catch (Exception $e) {
			$this->setInputError("Error on decoding JSON input");
		}
	}

	public function getInput() {
		// handle exact match search
		$this->handleExactMatch();
		$this->setParams($_GET);
	}

	public function handleExactMatch() {
		if(!array_key_exists("exact_match", $_GET)) {
			$_GET["exact_match"] = false;
		} else {
			$_GET["exact_match"] = ($_GET["exact_match"] === 'true');
		}
	}
	
	public function isEmptyInput() {
		return empty($this->getParams());
	}
	
	public function setInputError($errmsg) {
		$this->setStatusCode(400);
		$this->setError($errmsg);
	}
	
	public function elaborateResponse() {
		$this->setHttpHeaders($this->response["statusCode"]);
		echo json_encode($this->response, JSON_NUMERIC_CHECK);
	}
	
	public function setHttpHeaders($statusCode){
		$statusMessage = $this->getHttpStatusMessage($statusCode);
		try {
			header($this->httpVersion. " ". $statusCode ." ". $statusMessage);		
		} catch (Exception $e) {}
		header("Content-Type:". $this->contentType);

		// set Access-Control-Allow-Origin if set by APP_ALLOWED_HOSTS environment variable
		$ACAO = getenv("APP_ALLOWED_HOSTS");
		if ($ACAO) header("Access-Control-Allow-Origin: " . getenv("APP_ALLOWED_HOSTS"));

		/* set Access-Control-Allow-Headers to allow use of 'authorization' header (used for JWT authorized requests)
		* https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS/Errors/CORSMissingAllowHeaderFromPreflight
		* https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Headers
		* https://developer.mozilla.org/en-US/docs/Glossary/CORS-safelisted_request_header
		*/ 
		header("Access-Control-Allow-Headers: authorization");
	}
	
	public function getHttpStatusMessage($statusCode){
		return ($this->httpStatus[$statusCode]) ? $this->httpStatus[$statusCode] : $this->httpStatus[500];
	}
	
	/**
	 * Check for a valid token in the header authorization and set into class variable JWT_payload
	 */
	protected function _setJWT_payload() {
		
		$token = isset($_SERVER["HTTP_AUTHORIZATION"]) ? $_SERVER["HTTP_AUTHORIZATION"] : NULL;

		if (!is_null($token)) {

			require_once('JWT.php');

			// Get our server-side secret key from a secure location.
			$serverKey = getenv("SERVER_KEY");

			try { 
				$this->JWT_payload = $this->object_to_array(JWT::decode($token, $serverKey, array('HS256'))); 
			}
			catch(Exception $e) { /* do nothing */ }	
		}
	}

	protected function _get_auth_data() {
        
		// Check if valid token
        if (isset($this->JWT_payload) and isset($this->JWT_payload["userId"])) {
            // Auth user info from token as array
            return $this->object_to_array($this->JWT_payload);
        }
		// Try to catch info from session data
		// Start the session
		if(!isset($_SESSION)) { 
			session_start(); 
		} 
		if (isset($_SESSION["userId"])) {
			return array(
				'userId' => $_SESSION['userId']
			);
		}
		return null;
    }

}
?>