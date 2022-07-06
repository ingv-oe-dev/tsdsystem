<?php
if (getenv("ENV") == 'development') {
	error_reporting(-1);
	ini_set('display_errors', 'On');
}
require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Mailer.php');
	
header("Content-Type: application/json");
$result = array();

$email = $_POST['email'];
$pattern = '/[.]*@[ct\.]*ingv\.it/i'; // solo ingv
// $pattern = "/.*/i"; //tutti
$send_mail = (isset($_POST["send_mail"]) and ($_POST["send_mail"] == "0" or strtolower($_POST["send_mail"]) == "false")) ? false : true;

if(preg_match($pattern, $email)) {

	require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SecureLogin.php');
	$sl = new SecureLogin();
	if (empty($_POST['email']) or empty($_POST['password'])) {
		$result["error"] = "Email or password are not correct";
	} else {
		$result = $sl->registration($_POST['email'], $_POST['password']);

		if (
			array_key_exists("message", $result) and 
			$result["message"] and 
			array_key_exists("salt", $result) and 
			$result["salt"] and 
			$send_mail
		) {		
			/** invio email **/
			$subject = "Your TSDSystem registration";
			$body = "Hi " . $_POST['email'] . ",<br><br> 
				This email for your successful registration!<br><br>
			Please wait to be contacted by our administrators to confirm your registration.";
	
			$mail_addresses = array();
			array_push($mail_addresses, array(
				"email" => $_POST['email']
			));
			
			$mail_addresses_sent = Mailer::sendMailSingly_PHPMailer($mail_addresses, $subject, $body);
			$result["mail_address_sent"] = $mail_addresses_sent;
			
			
			/** invio email admin **/
			$subject = "NEW TSDSystem registration [" . $_POST['email'] . "]";
			$adminbody = "A new registration:<br><br>
			<b>Username</b>: " . $_POST['email'] . "<br><br>
			Registration result:<br><pre>" . json_encode($result) . "</pre><br><br>
			By clicking the following (local) URL (or reachable by VPN) you confirm the registration and allow the new user to access into website:<br><a href='" . $sl::getHostAddress() . "/tsdws/login/registration_confirm.php?email=" . $_POST['email'] . "&secret=" . $result['salt'] . "'>Confirm here</a>";
			
			$mail_addresses = array();
			array_push($mail_addresses, array(
				"email" => getenv("ADMIN_EMAIL")
			));
			$mail_addresses_sent = Mailer::sendMailSingly_PHPMailer($mail_addresses, $subject, $adminbody);
			$result["mail_address_sent_admin"] = $mail_addresses_sent;
		}
	}

} else {
	$result["error"] = "Registration allowed only to INGV members.";

	$subject = "Failed TSDSystem registration [" . $_POST['email'] . "]";
	$adminbody = "A new registration was tried:<br><br>
	<b>Username</b>: " . $_POST['email'] . "<br><br>
	Registration result:<br><pre>" . json_encode($result) . "</pre><br><br>";
	
	$mail_addresses = array();
	array_push($mail_addresses, array(
		"email" => getenv("ADMIN_EMAIL")
	));
	$mail_addresses_sent = Mailer::sendMailSingly_PHPMailer($mail_addresses, $subject, $adminbody);
	
	$result["mail_address_sent_admin"] = $mail_addresses_sent;
}

echo json_encode($result);