<?php
    require_once('classes/Mailer.php');
    $mail_addresses = array();
    array_push($mail_addresses, array(
        "email" => isset($_GET['email']) ? $_GET['email'] : getenv("ADMIN_EMAIL")
    ));	
	$mail_addresses_sent = Mailer::sendMailSingly_PHPMailer($mail_addresses, "TEST MAIL FROM TSDSYSTEM", "TEST MAIL FROM TSDSYSTEM");
    header('Content-Type: application/json');
    echo json_encode($mail_addresses_sent);
?>