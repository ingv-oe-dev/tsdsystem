<?php

error_reporting(-1);
ini_set('display_errors', 'On');
	
header("Content-Type: application/json");
$result = array();

require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SecureLogin.php');
$sl = new SecureLogin();
if (empty($_POST['email']) or empty($_POST['password'])) {
	$result["error"] = "Email or password are not correct";
} else {
	$result = $sl->login($_POST['email'], $_POST['password']);
}

echo json_encode($result);