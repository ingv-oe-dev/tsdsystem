<?php
require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SecureLogin.php');
require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Mailer.php');

$response = array();
$sl = new SecureLogin();

$email = $_GET['email'];
$pattern = '/[.]*@[ct\.]*ingv\.it/i'; // solo ingv

if(preg_match($pattern, $email)) {

	$result = $sl->create_temp_reset_key($email);

	if ($result["status"]) {    

		/** invio email **/
		$subject = "TSDSystem: Reset your password";
		$body = "Hi " .$email . ",<br><br> 
		Click the following URL to reset your password (<b>valid for 1 day</b>):<br><br>
		<a href='" . $result["reset_url"] . "'>" . $result["reset_url"] . "</a>";

		$mail_addresses = array();
		array_push($mail_addresses, array(
			"email" => $email
		));

		$mail_addresses_sent = Mailer::sendMailSingly_PHPMailer($mail_addresses, $subject, $body);
		$response["mail_address_sent"] = $mail_addresses_sent;
		$response["message"] = "OK";
		$response["status"] = true;
	}
	else {
		$response["status"] = false;
		$response["error"] = $result["error"];
	} 
} else {
	$response["status"] = false;
	$response["error"] = "Invalid email";
}

header("Content-type: application/json");
echo json_encode($response);