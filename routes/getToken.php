<?php

error_reporting(-1);
ini_set('display_errors', 'On');

	header('Content-type: application/json');
	
	$username = '';
	$password = '';
	
	$now = new DateTime("now", new DateTimeZone("UTC"));

	if (isset($_POST['email'])) {$username = $_POST['email'];}
	if (isset($_POST['password'])) {$password = $_POST['password'];}
	//var_dump($username, $password);
	
	require_once ('../classes/SecureLogin.php');
	$sl = new SecureLogin();
	$login = $sl->login($username, $password);

	if($login["status"]) {

		require_once('../classes/JWT.php');

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
		$returnArray = array('token' => $token);
		$jsonEncodedReturnArray = json_encode($returnArray);
		echo $jsonEncodedReturnArray;
	} 
	else {
		$returnArray = array('error' => 'Invalid user ID or password.');
		$jsonEncodedReturnArray = json_encode($returnArray);
		echo $jsonEncodedReturnArray;
	}