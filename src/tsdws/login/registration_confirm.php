<?php

require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SecureLogin.php');
require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Mailer.php');

$sl = new SecureLogin();
$result = $sl->confirm_registration($_GET['email'], $_GET['secret']);	

if ($result["status"]) {

    echo $result["message"];
    
	/** invio email **/
	$subject = "Your TSDSystem registration (CONFIRMED)";
	$body = "Hi " . $_GET['email'] . ",<br><br> 
		Your successful registration was confirmed!<br><br>
	If you need to send some specific requests, please contacts " . getenv("ADMIN_EMAIL") . ".<br><br>

	TSDSystem Admin Group";

	$mail_addresses = array();
	array_push($mail_addresses, array(
		"email" => $_GET['email']
	));
	array_push($mail_addresses, array(
		"email" => getenv("ADMIN_EMAIL")
	));

	$mail_addresses_sent = Mailer::sendMailSingly_PHPMailer($mail_addresses, $subject, $body);

	if ($mail_addresses_sent) {
		echo "<br>An email was sent to:<br>";
		echo "<pre>"; print_r($mail_addresses_sent); echo "</pre>";
	}
} else {
    var_dump($result);
}