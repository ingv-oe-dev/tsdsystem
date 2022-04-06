<?php
require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SecureLogin.php');

$email = $_POST['email'];
$password = $_POST['password'];

$sl = new SecureLogin();
$response = $sl->reset_password($email, $password);
	
header("Content-type: application/json");
echo json_encode($response);