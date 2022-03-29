<?php

error_reporting(-1);
ini_set('display_errors', 'On');
	
header("Content-Type: application/json");
$result = array();

$email = $_POST['email'];
$pattern = '/[.]*@[ct\.]*ingv\.it/i'; // solo ingv
// $pattern = "/.*/i"; //tutti

if(preg_match($pattern, $email)) {

	require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SecureLogin.php');
	$sl = new SecureLogin();
	if (empty($_POST['email']) or empty($_POST['password'])) {
		$result["error"] = "Email or password are not correct";
	} else {
		$result = $sl->registration($_POST['email'], $_POST['password']);
	}

} else {
	$result["error"] = "Registration allowed only to INGV members.";
}

echo json_encode($result);