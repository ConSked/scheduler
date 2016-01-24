<?php // $Id: RegistrationPage.php 1675 2012-09-02 20:52:07Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
//Don't make the user authenticate before registering!
require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/Invitation.php');
require_once('db/Worker.php');
require_once('section/WorkerData.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/html.php');

isLoggedIn(); // ignore values; but sets up the (generic) session
// 3 possibilities
// a - SAVE found an error and we are back again - $_SESSION[WORKERCURRENT] is good
// b - .../pages/RegistrationPage.php?email=a@b.c[&code=asdf] - $_GET[PARAM_EMAIL] is good
// c - .../pages/RegistrationPage.php - else
$worker = getWorkerCurrent();
$code = isset($_SESSION[PARAM_WITHCODE]) ? $_SESSION[PARAM_WITHCODE] : NULL;
if (is_null($worker)) // not case a
{
    $_SESSION[PARAM_MESSAGE] = "";
    $worker = new Worker(); // case c
    setWorkerCurrent($worker);
    if (isset($_GET[PARAM_EMAIL])) // case b
    {
        $email = swwat_parse_string(html_entity_decode($_GET[PARAM_EMAIL]), true);
        $code = swwat_parse_string(html_entity_decode($_GET[PARAM_WITHCODE]), true);
        $_SESSION[PARAM_WITHCODE] = $code;

        $registration = Invitation::selectEmail($email, $code);
        if (!is_null($registration))
        {
            $worker = $registration->worker();
        }
        else
        {
            $_SESSION[PARAM_MESSAGE] = "This email and code is unknown.";
            $worker->email = $email;
        }
    }

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
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<title>SwiftShift - Registration Page</title>
	<link href="css/site.css" rel="stylesheet" type="text/css">
	<link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script src="jquery/jquery-1.7.2.min.js"></script>
	<script src="jquery/jquery-ui-1.8.20.custom.min.js"></script>

	<script type="text/javascript">
        // todo - disable save button if no first, last names or email
	</script>
</head>

<body onload="init()">
<div id="container">

<?php

// ok, start the html
include('section/header.php');

// NOTE THIS SHOULD CLOSELY FOLLOW WORKEREDITPAGE/ACTION
?>


<div id="main">

    <div id="registrationpage_welcome">
        <h5>Welcome to SwiftShift Registration</h5>
        <ol>
            <li>Create your account.</li>
            <li>Change your password after receiving your confirmation email.</li>
            <li>Set your Schedule preferences.</li>
        </ol>
    </div>

	<div id="registrationpage_register">
        <h5>Create your account</h5>
		<form method="POST" name="registrationpage_register_save" action="RegistrationAction.php">
		<table>
			<tr><td><?php createWorkerDataHTMLRows($worker, "registrationpage_form", FALSE, $code); ?></td></tr>
			<tr><td><?php swwat_createInputSubmit(PARAM_SAVE, "Submit Registration"); ?></td></tr>
		</table>
		</form>
	</div><!-- registrationpage_register -->

	<div id="registrationpage_register">
        <h5>Registration Success</h5>
        <p><?php echo $_SESSION[PARAM_MESSAGE]; ?></p>
	</div><!-- registrationpage_register -->

</div><!-- main -->

<?php
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
