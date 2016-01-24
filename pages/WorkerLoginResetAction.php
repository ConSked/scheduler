<?php // $Id: WorkerLoginResetAction.php 1345 2012-08-21 15:40:38Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/WorkerLogin.php');
require_once('util/log.php');
require_once('util/mail.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

/**
 * This Controller is used by the WorkerLoginPage's reset button (typically used by the Worker themselves)
 * vs. the WorkerViewPage's reset button (typically used by an Organizer)
 */
$email = swwat_parse_string(html_entity_decode($_POST[PARAM_EMAIL]), true);
if (is_null($email))
{
    throw new LoginException('username required');
}
try
{
    $password = WorkerLogin::password_reset($email);
    FormMail::sendPasswordReset($email, $password);
    $password = NULL;
}
catch (Exception $ex)
{
    logMessage('WorkerLoginResetAction error', $ex->getMessage());
}
$password = NULL;

// in all cases; redirect back to Login page
header('Location: WorkerLoginPage.php');
include('WorkerLoginPage.php');
?>
