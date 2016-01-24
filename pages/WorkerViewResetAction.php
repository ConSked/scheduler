<?php // $Id: WorkerViewResetAction.php 1345 2012-08-21 15:40:38Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('db/WorkerLogin.php');
require_once('util/log.php');
require_once('util/mail.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

/**
 * This Controller is used by the WorkerViewPage's reset button (typically used by an Organizer)
 * vs. the WorkerLoginPage's reset button (typically used by the Worker themselves)
 */
$email = getWorkerCurrent()->email;
if (is_null($email))
{
    logMessage('WorkerViewResetAction', getWorkerCurrent()->logIdentity());
    // should probably set PARAM_MESSAGE to return back error to viewer
    return;
}
try
{
    $password = WorkerLogin::password_reset($email);
    FormMail::sendPasswordReset($email, $password);
    $password = NULL;
}
catch (Exception $ex)
{
    logMessage('WorkerViewResetAction error', $ex->getMessage());
    // should probably set PARAM_MESSAGE to return back error to viewer
}

// in all cases; redirect back to WorkerViewPage page
header('Location: WorkerViewPage.php');
include('WorkerViewPage.php');
?>
