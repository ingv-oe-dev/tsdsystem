<?php

/* This class handle emails */
Class Mailer {

    public static function sendMailSingly($addresses, $subject, $body) {
        
        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';

        // Additional headers
        $headers[] = 'From: TSDSystem <tsdsystem.ingvoe@ct.ingv.it>';

        // Initialize email addresses array with sent property = false
        for ($i=0; $i<count($addresses); $i++) {
            $addresses[$i]["sent"] = false;
        }
    
        // Send mails singly
        for ($i=0; $i<count($addresses); $i++) {
            try {
                $headers[] = 'To: ' . $addresses[$i]["email"];

                $sent = mail(
                    $addresses[$i]["email"], 
                    $subject, 
                    $body, 
                    implode("\r\n", $headers)
                );
                
                if($sent){
                    $addresses[$i]["sent"] = true;
                } else {
                    $addresses[$i]["mailinfo"] = error_get_last()['message'];
                }
            }
            catch (Exception $e) {
                $addresses[$i]["mailinfo"] = $e->getMessage();
            }
        }
        return $addresses;
    }

    public static function sendMailSingly_PHPMailer($addresses, $subject, $body, $isHTML=true, $filename=null) {
        //includiamo la classe PHPMailer
        require_once "PHPMailer/class.phpmailer.php";
    
        // inizializzo l'array degli indirizzi con valore sent = false
        for ($i=0; $i<count($addresses); $i++) {
            $addresses[$i]["sent"] = false;
        }
    
        // procedo con l'invio delle mail
        for ($i=0; $i<count($addresses); $i++) {
            try {
                //istanziamo la classe
                $mail = new PHPmailer();
                $mail->IsSMTP();
                $mail->Host = getenv("SMTP_HOST");
                if (intval(getenv("SMTP_AUTH")) == 1) {
                    $mail->SMTPAuth = true;
                    $mail->Username = getenv("SMTP_USERNAME");
                    $mail->Password = getenv("SMTP_PASSWORD");
                    $mail->SMTPSecure = getenv("SMTP_SECURE");
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                }
                $mail->From = getenv("SMTP_FROM_ADDRESS") ? getenv("SMTP_FROM_ADDRESS") : "admin.tsdystem@ct.ingv.it";
                $mail->FromName = getenv("SMTP_FROM_NAME") ? getenv("SMTP_FROM_NAME") : "TSDSystem";
                $mail->AddAddress($addresses[$i]["email"], $addresses[$i]["email"]);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->IsHTML($isHTML);
                
                //percorso all'allegato
                if (isset($filename)) {
                    $mail->AddAttachment($filename);
                }
                if($mail->Send()){
                    $addresses[$i]["sent"] = true;
                } else {
                    $addresses[$i]["mailinfo"] = $mail->ErrorInfo;
                }
                $mail->SmtpClose();
                unset($mail);
            }
            catch (Exception $e) {
                $addresses[$i]["mailinfo"] = $e->getMessage();
            }
        }
        return $addresses;
    }
}