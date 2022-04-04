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
}