<?php

error_reporting(-1);
ini_set('display_errors', 'On');

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
require_once ('../classes/SecureLogin.php');
$sl = new SecureLogin();
$login = $sl->login($_POST['email'], $_POST['password']);

if($login["status"]) {

	require_once('../classes/JWT.php');

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
	 * Uncomment the following line and add an appropriate date to enable the 
	 * "not before" feature.
	 */
	$nbf = $now->getTimestamp();
	//var_dump($nbf);
	
	/**
	 * Uncomment the following line and add an appropriate date and time to enable the 
	 * "expire" feature.
	 */
	$exp = $now->add(new DateInterval('P30D'))->getTimestamp(); // expire in 30 days
	//var_dump($exp);

	// Get our server-side secret key from a secure location.
	$serverKey = file_get_contents("../server_key");
	
	// create a token
	$payloadArray = array();
	$payloadArray['userId'] = $userId;
	if (isset($nbf)) {$payloadArray['nbf'] = $nbf;}
	if (isset($exp)) {$payloadArray['exp'] = $exp;}
	$token = JWT::encode($payloadArray, $serverKey);

	// return to caller
	header("HTTP/1.1 201 Created");
	$returnArray = array('token' => $token);
} 

echo json_encode($returnArray);