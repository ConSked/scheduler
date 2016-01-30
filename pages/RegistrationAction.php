<?php // $Id: RegistrationAction.php 2411 2012-10-24 17:13:36Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
// NOT include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/Invitation.php');
require_once('db/Worker.php');
require_once('db/WorkerLogin.php');
require_once('util/SMSEnum.php');
require_once('util/log.php');
require_once('util/mail.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

isLoggedIn(); // ignore values, but sets up session

$worker = getWorkerCurrent();
try
{
    $worker->firstName = swwat_parse_string(html_entity_decode($_POST[PARAM_FIRSTNAME]), true);
}
catch (ParseSWWATException $ex)
{
    $_SESSION[PARAM_MESSAGE] = "Registration was unsuccessful. Please examine your first name entry.";
    header('Location: RegistrationPage.php');
    include('RegistrationPage.php');
    return;
}
try
{
    $worker->middleName = swwat_parse_string(html_entity_decode($_POST[PARAM_MIDDLENAME]), true);
}
catch (ParseSWWATException $ex)
{
    $worker->middleName = NULL;
}
try
{
    $worker->lastName = swwat_parse_string(html_entity_decode($_POST[PARAM_LASTNAME]), true);
}
catch (ParseSWWATException $ex)
{
    $_SESSION[PARAM_MESSAGE] = "Registration was unsuccessful. Please examine your last name entry.";
    header('Location: RegistrationPage.php');
    include('RegistrationPage.php');
    return;
}
try
{
    $worker->email = swwat_parse_string(html_entity_decode($_POST[PARAM_EMAIL]), true);
}
catch (ParseSWWATException $ex)
{
    $_SESSION[PARAM_MESSAGE] = "Registration was unsuccessful. Please examine your email entry.";
    header('Location: RegistrationPage.php');
    include('RegistrationPage.php');
    return;
}
try
{
    $worker->phone = swwat_parse_phone(html_entity_decode($_POST[PARAM_PHONE]), true);
}
catch (ParseSWWATException $ex)
{
    $worker->phone = NULL;
}
/*try
{
    $sms = swwat_parse_enum(html_entity_decode($_POST[PARAM_SMS_SERVICE]), SMSEnum::$SMS_ARRAY, true);
    if (0 == strcmp(SMSEnum::$NONE, $sms))
    {
        $sms = NULL;
    }
}
catch (ParseSWWATException $ex)
{
    $sms = NULL;
}
$worker->smsemail   = (is_null($worker->phone) || is_null($sms)) ? NULL : $worker->phone . "@" . $sms;*/


if (!is_null($worker->email))
{
    $check = Worker::selectUsername($worker->email);
    if (!is_null($check))
    {
        $_SESSION[PARAM_MESSAGE] = "Registration was unsuccessful. This email is already registered.\n\n"
            . "Are you looking to <a href='/pages/WorkerLoginPage.php'>Reset Password</a>?\n";
        header('Location: RegistrationPage.php');
        include('RegistrationPage.php');
        return;
    }
    $check = NULL; // gc hint
}


if (is_null($worker->email) || is_null($worker->firstName) || is_null($worker->lastName))
{
    $_SESSION[PARAM_MESSAGE] = "Registration was unsuccessful. First name, last name, and email are required.";
    header('Location: RegistrationPage.php');
    include('RegistrationPage.php');
    return;
}


try
{
	$worker = $worker->insert();
}
catch (PDOException $ex)
{
    logMessage("RegistrationAction", $ex->getMessage());
    $_SESSION[PARAM_MESSAGE] = "Registration was unsuccessful due to a database error. Please try again in a few minutes.";
    header('Location: RegistrationPage.php');
    include('RegistrationPage.php');
    return;
}
unset($_SESSION[PARAM_MESSAGE]);
unset($_SESSION[PARAM_WITHCODE]);
// default ACCEPT on all invitations
$registrationArray = array();
try
{
    $registrationArray = Invitation::selectWorker($worker->workerid);
}
catch (PDOException $ex)
{
    logMessage("RegistrationAction", "selecting worker:$worker->workerid  " . $ex->getMessage());
    // but ignore and continue
}
foreach ($registrationArray as $registration)
{
    try
    {
        $worker->assignToExpo($registration->expoid);
//        $registration->delete(); // accepted, so don't keep around
    }
    catch (PDOException $ex)
    {
        logMessage("RegistrationAction", "assign worker:$worker->workerid to expo:$expo->expoid failure " . $ex->getMessage());
        // but ignore and continue
    }
} // $registration
foreach ($registrationArray as $registration)
{
    try
    {
        $registration->delete(); // accepted, so don't keep around
    }
    catch (PDOException $ex)
    {
        logMessage("RegistrationAction", "delete registration for worker:$worker->workerid failure " . $ex->getMessage());
        // but ignore and continue
    }
} // $registration

$expoArray = array();
try
{
	$expoArray = Expo::selectMultiple();
}
catch (PDOException $ex)
{
    logMessage("RegistrationAction", "selecting expo list " . $ex->getMessage());
    // but ignore and continue
}

foreach ($expoArray as $expo)
{
	if ($expo->newUserAddedOnRegistration && !$expo->isPast())
	{
		if (!$worker->isAssignedToExpo($expo->expoid))
		{
			try
			{
        		$worker->assignToExpo($expo->expoid);
			}
			catch (PDOException $ex)
			{
				logMessage("RegistrationAction", "assign worker:$worker->workerid failure " . $ex->getMessage());
				// but ignore and continue
			}
		}
	}
}

//Send out a confirmation e-mail
$welcomeForm = new FormMail(SITE_NAME." Registration Confirmation",
    array("FIRSTNAME", "LOGINURL"),
    "Hello FIRSTNAME,\nWelcome to ".SITE_NAME."!\n\n"
    . "If you forget your password, simply enter your e-mail address on the login page and click the \"Reset Password\" button.\n\n"
    . "Login Page: LOGINURL."
    . "\n\nSincerely,\nThe ".SITE_NAME." Team");
$welcomeForm->sendForm($worker->email,
    array("FIRSTNAME" => $worker->firstName, "LOGINURL" => LOGIN_URL));

$_SESSION[AUTHENTICATED_TEMP] = $worker;
$_SESSION[AUTHENTICATED] = $worker;
header('Location: WorkerLoginChangePage.php');
include('WorkerLoginChangePage.php');
?>
