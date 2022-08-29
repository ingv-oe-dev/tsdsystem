<?php
if (getenv("ENV") == 'development') {
	error_reporting(-1);
	ini_set('display_errors', 'On');
}
header("Content-Type: application/json");
$result = array();

require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SecureLogin.php');
$sl = new SecureLogin();
if (empty($_POST['email']) or empty($_POST['password'])) {
	$result["error"] = "Email or password are not correct";
} else {
	$result = $sl->login($_POST['email'], $_POST['password']);
}

if ($result["status"]) {
	session_start();
	$_SESSION["userId"] = $result["user_id"];
	$_SESSION["email"] = $_POST['email'];
	$_SESSION["isAdmin"] = $_SESSION["userId"] == getenv("ADMIN_ID");
}

echo json_encode($result);