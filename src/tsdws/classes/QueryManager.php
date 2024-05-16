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
		if (!getenv("DB_HOST")) return null;
		if (!getenv("DB_USER")) return null;
		if (!getenv("DB_PASSWORD")) return null;
		if (!getenv("DB_NAME")) return null;
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
		//$config_read = file_get_contents("..".DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR."db.webapp_user.json"); // for development test
		//$config_read = file_get_contents("..".DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR."db.local.json"); // for local test
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

	public function composeOrderBy($cols, $allowed_cols) {
		$orderby_str = '';
		$counter = 0;
		foreach($cols as $c) {
			$sort_type = "";
			$exp = explode(".", $c); // ex. name.asc (or name), id.desc 
			$colname = $exp[0];
			if (count($exp) > 1) {
				$sort_type = strtoupper($exp[1]);
			}
			if (in_array($sort_type, array("", "ASC", "DESC"))) {  // see if 'asc' or 'desc' sorting
				foreach($allowed_cols as $key => $value) {
					if ($colname == $key or $colname == $value["alias"]) {
						$orderby_str .= $colname . " " . $sort_type . ", ";
						$counter++;
					}
				}
			}
		}
		if ($counter > 0) $orderby_str = " ORDER BY " . $orderby_str;
		return rtrim($orderby_str, ", ");
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
				$str .= $params["fieldname"] . " = '" . pg_escape_string($params["value"]) . "'";
			} else {
				$str .= $params["fieldname"] . " = " . $params["value"];
			}
		} else {
			if ($params["quoted"]) {
				$str .=  "(" . $params["fieldname"] . ")::text ILIKE ('%" . pg_escape_string($params["value"]) . "%')";
			} else {
				$str .= $params["fieldname"] . " = " . $params["value"];
			}
		}
		return $str;
	}

	public function composeUpdateStatement($input, $updateFields) {
		$updStmt = '';
		foreach($updateFields as $key => $value) {
			if (array_key_exists($key, $input) and isset($input[$key])){
				if (array_key_exists("json", $value) and $value["json"]) {
					$updStmt .= $key . " = '" . pg_escape_string(json_encode($input[$key])) . "', ";
				} else {
					$updStmt .= "$key = " . ((array_key_exists("quoted", $value) and $value["quoted"]) ? "'".pg_escape_string($input[$key])."'" : "$input[$key]") . ", ";
				}
			}
		}
		return rtrim($updStmt, ", ");
	}

	public function composeUpdateStatementForceNull($input, $updateFields) {
		$updStmt = '';
		foreach($updateFields as $key => $value) {
			if (array_key_exists($key, $input)){
				if (isset($input[$key])) {
					if (array_key_exists("json", $value) and $value["json"]) {
						if (array_key_exists("associative", $value) and !$value["associative"]) {
							$updStmt .= $key . " = '" . pg_escape_string(json_encode($input[$key])) . "', ";
						} else {
							$updStmt .= $key . " = '" . pg_escape_string(json_encode((object) $input[$key])) . "', ";
						}
					} else {
						$updStmt .= "$key = " . ((array_key_exists("quoted", $value) and $value["quoted"]) ? "'".pg_escape_string($input[$key])."'" : "$input[$key]") . ", ";
					}
				} else {
					$updStmt .= $key . " = NULL, ";
				}
			}
		}
		return rtrim($updStmt, ", ");
	}

	public function genericUpdateRoutine($input, $updateFields=array(), $whereStmt='') {

		$next_query = "";
		$response = array(
			"status" => false,
			"rows" => null
		);

		//$updStmt = $this->composeUpdateStatement($input,  $updateFields);
		$updStmt = $this->composeUpdateStatementForceNull($input,  $updateFields);
		if(empty($updStmt)) {
			return array(
				"status" => true,
				"rows" => 0
			); 
		}

		try {
			$next_query = "UPDATE " . $this->tablename . " SET " . $updStmt . " ";
			if(!empty($whereStmt)) {
				$next_query .=  $whereStmt;
			}
			$stmt = $this->myConnection->prepare($next_query);
			$stmt->execute();
			$response["rows"] = $stmt->rowCount();
			$response["status"] = true;
			// return result
			return $response;
		}
		catch (Exception $e){
			return array(
				"status" => false,
				"failed_query" => $next_query,
				"error" => $e->getMessage()
			);
		}
	}

	/* No Spatial Reference IDentifier (SRID) is set for the $geom_point_field_name geometry point stored into the database */
	public function extendSpatialQuery($input, $geom_point_field_name) {

		$substr = "";

		// check if the $geom_point_field_name is not empty
		if (!isset($geom_point_field_name) or empty($geom_point_field_name)) return $substr;

		// get used projection
		$epsg_degree = getenv("EPSG_DEGREE") ? getenv("EPSG_DEGREE") : "4326"; // WGS84 (SRID) - using degrees
		$epsg_m = getenv("EPSG_M") ? getenv("EPSG_M") : "32632"; // UTM zone 32N (SRID) - using meters
		
		// check that $input is regular
		if (!isset($input) or !is_array($input)) return $substr;

		// Specify southern-northern-western-eastern boundary for search
		if (array_key_exists("minlatitude", $input)) $substr .= " AND ST_Y($geom_point_field_name) >= " . $input["minlatitude"] . " ";
		if (array_key_exists("maxlatitude", $input)) $substr .= " AND ST_Y($geom_point_field_name) <= " . $input["maxlatitude"] . " ";

		if (array_key_exists("minlongitude", $input)) $substr .= " AND ST_X($geom_point_field_name) >= " . $input["minlongitude"] . " ";
		if (array_key_exists("maxlongitude", $input)) $substr .= " AND ST_X($geom_point_field_name) <= " . $input["maxlongitude"] . " ";

		// check if center coords are defined
		if (!(array_key_exists("latitude", $input) and array_key_exists("longitude", $input))) return $substr;

		// if here both latitude and longitude center are defined
		// minradius (degrees)
		if (array_key_exists("minradius", $input)) {
			$substr .= " AND ST_DISTANCE(
				ST_SetSRID(ST_POINT(". $input["longitude"] . ", " . $input["latitude"] . "), $epsg_degree), 
				ST_SetSRID($geom_point_field_name, $epsg_degree) 
			) >= " . $input["minradius"] . " ";
		}
		// maxradius (degrees)
		if (array_key_exists("maxradius", $input)) {
			$substr .= " AND ST_DISTANCE(
				ST_SetSRID(ST_POINT(". $input["longitude"] . ", " . $input["latitude"] . "), $epsg_degree), 
				ST_SetSRID($geom_point_field_name, $epsg_degree) 
			) <= " . $input["maxradius"] . " ";
		}
		// minradius (km)
		if (array_key_exists("minradiuskm", $input)) {
			$substr .= " AND ST_DISTANCE(
				ST_TRANSFORM(ST_SetSRID(ST_POINT(". $input["longitude"] . ", " . $input["latitude"] . "), $epsg_degree), $epsg_m),  
				ST_TRANSFORM(ST_SetSRID($geom_point_field_name, $epsg_degree), $epsg_m) 
			) >= (" . $input["minradiuskm"] . " * 1000)";
		}
		// maxradius (km)
		if (array_key_exists("maxradiuskm", $input)) {
			$substr .= " AND ST_DISTANCE(
				ST_TRANSFORM(ST_SetSRID(ST_POINT(". $input["longitude"] . ", " . $input["latitude"] . "), $epsg_degree), $epsg_m),  
				ST_TRANSFORM(ST_SetSRID($geom_point_field_name, $epsg_degree), $epsg_m) 
			) <= (" . $input["maxradiuskm"] . " * 1000)";
		}
		
		return $substr . " ";
	}
}
?>