<?php // $Id: InvitationAction.php 2360 2012-10-09 01:14:19Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.

include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

require_once('db/Invitation.php');


$expo = getExpoCurrent();
// minor housekeeping
unset($_SESSION[PARAM_UPLOAD]);
unset($_SESSION[PARAM_MESSAGE]);
unset($_SESSION[PARAM_STOPTIME]);
unset($_SESSION[PARAM_WITHCODE]);
unset($_SESSION[PARAM_UNIQUE]);

$expirationDate = swwat_parse_date(html_entity_decode($_POST[PARAM_STOPTIME]));
$withCode = (0 == strcmp(PARAM_WITHCODE, swwat_parse_string(html_entity_decode($_POST[PARAM_WITHCODE]))));
$uniqueCode = (0 == strcmp(PARAM_UNIQUE, swwat_parse_string(html_entity_decode($_POST[PARAM_UNIQUE]))));
$errorMessage = "";
// invite or upload file button?
$invite = isset($_POST[PARAM_SAVE]);
$upload = isset($_POST[PARAM_UPLOAD]);
//
// todo should be _enum(, get if we ever have anything other than 5degrees
// $uploadFileType = swwat_parse_string(html_entity_decode($_POST[PARAM_UPLOADFILETYPE]));

if ($invite)
{
    // get emails typed in
    $emails = swwat_parse_string(html_entity_decode($_POST[PARAM_EMAIL]));
    $emails = preg_replace('/\s+/', ':', $emails);
    logMessage("message", $emails);
    // parse via whitespace
    $invitationArray = Invitation::loadEmails($emails);

    $existingWorkers = Worker::selectExpo($expo->expoid);
    $checkWorkers = array();
    foreach ($existingWorkers as $worker)
    {
        $checkWorkers[] = $worker->workerid;
    }
    $existingWorkers = NULL;

    $workerArray = array();
    $unknownArray = array();
    $index = 0;
    foreach ($invitationArray as $invitation)
    {
        $index += 1;
        try
        {
            if (!empty($invitation->email))
            {
                $worker = Worker::selectUsername($invitation->email);
                if (!is_null($worker))
                {
                    if (FALSE != array_search($worker->workerid, $checkWorkers))
                    {
                        $errorMessage .= "<tr><td>$index</td><td>$invitation->email</td><td>already assigned</td><td>no email</td><td>OK!</td></tr>\n";
                    }
                    else
                    {
                        $workerArray[] = $worker;
                        $errorMessage .= "<tr><td>$index</td><td>$invitation->email</td><td>account exists</td><td>email sent</td><td>OK!</td></tr>\n";
                    }
                    continue;
                }
                $unknownArray[] = $invitation;
                $errorMessage .= "<tr><td>$index</td><td>$invitation->email</td><td>invite issued</td><td>email sent</td><td>OK!</td></tr>\n";
            }
        }
        catch (PDOException $ex)
        {
            $errorMessage .= "<tr><td>$index</td><td>$invitation->email</td><td>bad data</td><td>no email</td><td>databse failure</td></tr>\n";
            $msg = $ex->getMessage();
            logMessage("InvitationAction", "PDOException:$msg file line error:$error");
        }
    }
    // send emails
    Invitation::inviteWorkers($expo, $expirationDate, $workerArray);
    $workerArray = NULL;
    Invitation::inviteUnknown($expo, $expirationDate, $unknownArray, $withCode, $uniqueCode);
    $unknownArray = NULL;

    if (strlen($errorMessage) > 0)
    {
        $_SESSION[PARAM_MESSAGE] = $errorMessage;
    }

    header('Location: InvitationPage.php');
    include('InvitationPage.php');
}
else if ($upload) // upload file
{
    // move defaults from _POST to _SESSION
    $_SESSION[PARAM_UPLOADFILETYPE] = swwat_parse_string("5 degrees");
    $_SESSION[PARAM_STOPTIME] = $_POST[PARAM_STOPTIME];
    $_SESSION[PARAM_WITHCODE] = $_POST[PARAM_WITHCODE];
    $_SESSION[PARAM_UNIQUE] = $_POST[PARAM_UNIQUE];
    if (isset($_SESSION[PARAM_UNIQUE]))
    {
        $_SESSION[PARAM_WITHCODE] = PARAM_WITHCODE; // ensure set if UNIQUE is
    }
    header('Location: InvitationFileUploadPage.php');
    include('InvitationFileUploadPage.php');
    return;
}

/*
 * else the select a set of workers item; analogous to ExpoAssignPage
    $workerArray = array(); // set from UI
    Invitation::inviteWorkers($expo, $expirationDate, $workerArray, $withCode, $uniqueCode);
 */

// default - go back
header('Location: InvitationPage.php');
include('InvitationPage.php');

?>
