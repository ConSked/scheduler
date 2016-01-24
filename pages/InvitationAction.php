<?php // $Id: InvitationAction.php 2360 2012-10-09 01:14:19Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

require_once('db/Invitation.php');


// $expo = getExpoCurrent();
// minor housekeeping
unset($_SESSION[PARAM_UPLOAD]);
unset($_SESSION[PARAM_MESSAGE]);
unset($_SESSION[PARAM_STOPTIME]);
unset($_SESSION[PARAM_WITHCODE]);
unset($_SESSION[PARAM_UNIQUE]);

//$expirationDate = swwat_parse_date(html_entity_decode($_POST[PARAM_STOPTIME]));
//$withCode = (0 == strcmp(PARAM_WITHCODE, swwat_parse_string(html_entity_decode($_POST[PARAM_WITHCODE]))));
//$uniqueCode = (0 == strcmp(PARAM_UNIQUE, swwat_parse_string(html_entity_decode($_POST[PARAM_UNIQUE]))));
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
    logMessage("message", $emails);
    // parse via whitespace
    // send emails
    $unknownArray = array(); // set from UI
    Invitation::inviteUnknown($expo, $expirationDate, $unknownArray, $withCode, $uniqueCode);
    header('Location: InvitationPage.php');
    include('InvitationPage.php');
}
else
if ($upload) // upload file
{
    // move defaults from _POST to _SESSION
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
