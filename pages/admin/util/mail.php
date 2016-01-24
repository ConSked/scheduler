<?php  // $Id: mail.php 2119 2012-09-21 16:10:11Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('util/log.php');

class FormMail
{

public $paramNames; // using NAME and EXPONAME leads to bad results; because NAME might get replaced first!
public $subject;
public $body;

public function __construct($subject, array $paramNames, $body)
{
    $this->subject = $subject;
    $this->paramNames = $paramNames;
    $this->body = $body;
}

public static function sendPasswordReset($to, $password)
{
    // TODO - read subject, params, body from a properties file
    $pwResetForm = new FormMail("Your SwiftShift account", array("CODE"), "Your account password has been reset to: CODE");
    $pwResetForm->sendForm($to, array('CODE' => $password));
} // sendPasswordReset

/**
 * This is the base function for sending mail;
 * it looks up the form, and fills in the params,
 * then calls mail.
 */
public function sendForm($to, array $params = NULL)
{
    try
    {
        $sendBody = $this->body;
        if (!is_null($params))
        {
            if (count($this->paramNames) != count($params))
            {
                logMessage('FormMail.sendForm(' . $to .')', 'params do not match');
            }
            $sendBody = $this->body;
            foreach ($this->paramNames as $param)
            {
                $sendBody = str_replace($param, $params[$param], $sendBody);
            }
        }
        FormMail::send($to, $this->subject, $sendBody);
    }
    catch (Exception $ex)
    {
        logMessage('FormMail.sendForm(' . $to .')', $ex->getMessage());
    }
} // sendForm

/**
 * used both internally, and to send any arbitrary mail.
 * See notes on WorkerViewPage
 */
public static function send($to, $subject, $body)
{
    try
    {
		$headers = 'From: support@swiftexpos.com'."\r\n".'Reply-To: support@swiftexpos.com'."\r\n".'X-Mailer: PHP/' . phpversion();
        mail($to, $subject, $body, $headers);
    }
    catch (Exception $ex)
    {
        logMessage('FormMail.send(' . $to .')', $ex->getMessage());
    }
} // send

} // FormMail

?>
