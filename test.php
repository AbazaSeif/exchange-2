<?php
/**
 * send_email
 * Sends mail via SMTP
 * uses Pear::Mail
 * @author Andrew McCombe <andrew@iweb.co.uk>
 * 
 * @param string $to Email address of the recipient in 'Name <email>' format
 * @param string $from Email address of sender
 * @param string $subject Subject of email
 * @param string $body Content of email
 * 
 * @return boolean if false, error message written to error_log
 */
function sendEmail($to, $from, $subject, $body) {
    //require_once("/usr/share/pear/Mail.php");
    //require_once("/usr/share/pear/Mail/mime.php");
    
    
    require_once "Mail.php";
    require_once "Mail/mime.php";    

    $host = "smtp.yourhost.com";

    $headers = array (
            'From' => $from,
            'To' => $to,
            'Subject' => $subject
    );

    $mime = new Mail_mime();
    $mime->setHTMLBody($body);

    $body = $mime->get();
    $headers = $mime->headers($headers);

    $smtp = Mail::factory('smtp', array ('host' => $host));
    $mail = $smtp->send($to, $headers, $body);

    if (PEAR::isError($mail)) {
            return false;
    } else {
            return true; 
    }
    
}
 
$body = '<h1>Test Mail</h1><p style="color: red">This is a test</p>';
 
sendEmail('John Doe <john@doe.com>', 'Bob Smith <bob@aol.com>', 'Test HTML message', $body);