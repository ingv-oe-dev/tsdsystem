<?php
/*
error_reporting(-1);
ini_set('display_errors', 'On');
*/
// Token validity in days
$validity_days = 30;

// define scopes
$resources = array("owners","nets","sensortypes","sensors","channels","timeseries");
$actions = array("read", "edit");
$scopes = array_merge(array("all"), $resources);
foreach($resources as $resource) {
	foreach($actions as $action) {
		array_push($scopes, "$resource-$action");
	}
}
//var_dump($scopes);

// default response 
header('Content-type: application/json');
header("HTTP/1.1 409 Unauthorized");
$returnArray = array('error' => 'Invalid user ID or password.');

// check email and password
if (!array_key_exists('email',$_POST)) {
	header("HTTP/1.1 400 Bad Request");
	$returnArray = array('error' => 'Missing input \'email\'');
	echo json_encode($returnArray);
	exit();
}
if (!array_key_exists('password',$_POST)) {
	header("HTTP/1.1 400 Bad Request");
	$returnArray = array('error' => 'Missing input \'password\'');
	echo json_encode($returnArray);
	exit();
}

// login phase
require_once ('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SecureLogin.php');
$sl = new SecureLogin();
$login = $sl->login($_POST['email'], $_POST['password']);

if($login["status"]) {

	require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'JWT.php');

	$now = new DateTime("now", new DateTimeZone("UTC"));

	/** 
	 * Create some payload data with user data we would normally retrieve from a
	 * database with users credentials. Then when the client sends back the token,
	 * this payload data is available for us to use to retrieve other data 
	 * if necessary.
	 */
	$userId = $login["user_id"];
	//var_dump($userId);
	
	/**
	 * If scope is defined: Load user permissions
	 */
	if (isset($_POST['scope'])) {
		if (in_array($_POST['scope'], $scopes)) {
			// load users class
			require_once ('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Users.php');
			$user = new Users($userId);
			
			// retrieve permissions
			 
			$scope = explode('-', $_POST['scope']); // view scope
			$permissions = $user->getPermissions($scope);
			//var_dump($permissions);
		} else {
			header("HTTP/1.1 400 Bad Request");
			$returnArray = array("error" => "Choose scope among the following: '" . implode("','", $scopes) . "'");
			echo json_encode($returnArray);
			exit();
		}
	}
	/**
	 * Uncomment the following line and add an appropriate date to enable the 
	 * "not before" feature.
	 */
	$nbf = $now->getTimestamp();
	//var_dump($nbf);
	
	/**
	 * Uncomment the following line and add an appropriate date and time to enable the 
	 * "expire" feature.
	 */
	$exp = $now->add(new DateInterval('P'.$validity_days.'D'))->getTimestamp(); // expire in days
	//var_dump($exp);

	// Get our server-side secret key from a secure location.
	$serverKey = getenv("SERVER_KEY");
	
	// create a token
	$payloadArray = array();
	$payloadArray['userId'] = $userId;
	if (isset($permissions)) {
		if (count($scope) > 0 and $scope[0] != "all") {
			$append = (count($scope) > 1) ? array($scope[1] => $permissions) : $permissions;
			$permissions = array("resources" => array($scope[0] => $append));
		}

		$payloadArray['rights'] = $permissions;
	}
	if (isset($nbf)) {$payloadArray['nbf'] = $nbf;}
	if (isset($exp)) {$payloadArray['exp'] = $exp;}
	$token = JWT::encode($payloadArray, $serverKey);

	// save generated token into db
	try {
		// load query manager class
		require_once ('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'QueryManager.php');
		$QueryManager = new QueryManager();
		$query = "INSERT INTO tsd_users.tokens (token) VALUES ('$token')";
		//echo $query;
		$stmt = $QueryManager->myConnection->prepare($query);
		$stmt->execute();
	} catch (Exception $e){
		// do nothing
	}

	// return to caller
	header("HTTP/1.1 201 Created");
	$returnArray = array('token' => $token);
	// $returnArray = $token; // for development
} 

echo json_encode($returnArray);