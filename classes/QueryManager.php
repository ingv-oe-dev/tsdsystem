<?php
require_once("Utils.php");

/* This class handle postgresql connection and query by the PDO library */
Class QueryManager extends Utils {

	private $credentials;
	public $myConnection; 
	
	//CONSTRUCTOR
	function __construct($connection_vars = null) {
		if (isset($connection_vars)) {
			$this->connectToMySQL($connection_vars);
		} else {
			$this->readCredentials();
			$this->defaultConnectToMySQL();
		}
	}
	
	//DESTRUCTOR
	function __destruct() {
		$this->closeConnection();
	}
	
	private function readCredentials() {
		
		try {
			$this->credentials = $this->readCredentialsFromEnv();
			if (empty($this->credentials)) {
				$this->credentials = $this->readCredentialsFromConfigFile();
			}
		} catch (Exception $e) {
			// do nothing - if here and empty($this->credentials) is true, then the app is unable to connect
		}
	}

	private function readCredentialsFromEnv() {
		if (!isset($_SERVER["DB_HOST"])) return null;
		if (!isset($_SERVER["DB_USER"])) return null;
		if (!isset($_SERVER["DB_PASSWORD"])) return null;
		if (!isset($_SERVER["DB_NAME"])) return null;
		return array(
			"host" => getenv("DB_HOST"),
			"user" => getenv("DB_USER"),
			"pwd" => getenv("DB_PASSWORD"),
			"db" => getenv("DB_NAME"),
			"port" => getenv("DB_PORT")
		);
	}

	private function readCredentialsFromConfigFile() {
		$config_read = file_get_contents($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR."tsdws".DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR."db.json");
		return json_decode($config_read, true);
	}

	public function defaultConnectToMySQL() {
		$this->connectToMySQL($this->credentials);
	}
	
	public function connectToMySQL($connection_vars) {
		
		// force eventual existing connection to close (= null)
		$this->closeConnection();
		
		$dsn = "pgsql:host=".$connection_vars["host"];
		if (isset($connection_vars["db"])) $dsn .= ";dbname=".$connection_vars["db"];
		if (isset($connection_vars["port"])) $dsn .= ";port=".$connection_vars["port"];
		
		try {
			$this->myConnection = new PDO($dsn, $connection_vars["user"], $connection_vars["pwd"]);
		} catch (Exception $e) {
			$this->closeConnection();
		}
	}
	
	public function getConnection(){
		return $this->myConnection;
	}
	
	public function closeConnection(){
		$this->myConnection = null;
	}
	
	public function isConnected() {
		return ($this->myConnection != null);
	}
	
	protected function getHost() {
		return $this->default_host;
	}
	
	private function getUser() {
		return $this->default_user;
	}
	
	private function getPwd() {
		return $this->default_pass;
	}
	
	protected function getDB() {
		return $this->default_db;
	}
	
	public function setHost($host) {
		$this->default_host = $host;
	}
	
	public function setUser($user) {
		$this->default_user = $user;
	}
	
	public function setPwd($pass) {
		$this->default_pass = $pass;
	}
	
	public function setDB($db) {
		$this->default_db = $db;
	}
	
	public function setPort($port) {
		$this->default_port = $port;
	}
	
	public function getLastInsertId() {
		return $this->myConnection->lastInsertId();
	}
	
	public function getSingleField($sqlQuery) {
		$response = array (
			"status" => false
		);
		try {
			$sqlResult = $this->myConnection->query($sqlQuery);
			$data = $sqlResult->fetchColumn();
			$response["status"] = true;
			$response["data"] = $data;
		} catch (Exception $e) {
			$response["error"] = $e->getMessage();
		}
		return $response;
	}
	
	public function getSingleRecord($sqlQuery) {
		$row = array();
		try {
			$sqlResult = $this->myConnection->query($sqlQuery);
			if ($sqlResult) {
				$row["status"] = true;
				$row["data"] = $sqlResult->fetch(PDO::FETCH_ASSOC);	
				$sqlResult = null;
			} else {
				$row["status"] = false;
				$row["error"] = $this->myConnection->errorinfo();
			}
		} catch (Exception $e) {
			$row["status"] = false;
			$row["error"] = $e->getMessage();
		}
		return $row;
	}
	
	public function getRecordSet($sqlQuery) {
		$rows = array();
		try {
			$sqlResult = $this->myConnection->query($sqlQuery);
			if ($sqlResult) {
				$rows["status"] = true;
				$rows["data"] = $sqlResult->fetchAll(PDO::FETCH_ASSOC);
				$sqlResult = null;
			} else {
				$rows["status"] = false;
				$rows["error"] = $this->myConnection->errorinfo();
			}
		} catch (Exception $e) {
			$rows["status"] = false;
			$rows["error"] = $e->getMessage();
		}
		return $rows;
	}

	public function executeSQLCommand($query) {
		
		$response = array();
		
		try {
			// start transaction
			$this->myConnection->beginTransaction();
			
			// multiple query
			if (is_array($query)) {
				while ($query) {                         
					$next_query = array_shift($query);
					//echo $next_query;
					$stmt = $this->myConnection->prepare($next_query);
					$stmt->execute();
					array_push($response, array(
						"status" => true,
						"query" => $next_query,
						"rows" => $stmt->rowCount()
					));
				}      
			}
			
			// single query
			else {    
				$next_query = $query;
				$stmt = $this->myConnection->prepare($next_query);
				$stmt->execute();
				array_push($response, array(
					"status" => true,
					"query" => $next_query,
					"rows" => $stmt->rowCount()
				));   
			}	
			
			// commit
			$this->myConnection->commit();
			
		} catch (Exception $e){
			array_push($response, array(
				"status" => false,
				"failed_query" => $next_query,
				"next_queries" => $query,
				"error" => $e->getMessage()
			));
			// rollback
			$this->myConnection->rollback();
		}
		
		return $response;
	}
	
	public function executeReadPreparedStatement($query, $data=null) {
		$result = null;
		try {
			$sth = $this->myConnection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)); 
			$sth->execute($data);
			$result = array(
				"status" => true,
				"data" => $sth->fetchAll(PDO::FETCH_ASSOC)
			);
		} catch (Exception $e){
			$result = array(
				"status" => false,
				"error" => $e->getMessage()
			);
		}
		return $result;
	}
	
	public function executeWritePreparedStatement($query, $data=null) {
		try {
			$sth = $this->myConnection->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)); 
			$sth->execute($data);
			return array(
				"status" => true
			);
		} catch (Exception $e){
			return array(
				"status" => false,
				"error" => $e->getMessage()
			);
		}
	}

	public function composeWhereFilter($input, $search_params) {
		$where_filter = '';
		foreach($search_params as $key => $value) {
			if (array_key_exists($key, $input) and isset($input[$key])){
				$where_filter .= $this->matchFieldByValue(array(
					"fieldname" => array_key_exists("alias", $value) ? $value["alias"] : $key,
					"value" => $input[$key],
					"exact_match" => isset($value["id"]) or (array_key_exists("exact_match", $input) and ($input["exact_match"] == 'true')),
					"quoted" => $value["quoted"]
				));
			}
		}
		return $where_filter;
	}

	public function matchFieldByValue($params) {
		$str = " AND ";
		if ($params["exact_match"]) {
			if ($params["quoted"]) {
				$str .= $params["fieldname"] . " = '" . $params["value"] . "'";
			} else {
				$str .= $params["fieldname"] . " = " . $params["value"];
			}
		} else {
			if ($params["quoted"]) {
				$str .=  "UPPER(" . $params["fieldname"] . ") LIKE UPPER('%" . $params["value"] . "%')";
			} else {
				$str .= $params["fieldname"] . " = " . $params["value"];
			}
		}
		return $str;
	}
}
?>