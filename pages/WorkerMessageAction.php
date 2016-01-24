<?php // $Id: WorkerMessageAction.php 1958 2012-09-13 21:33:05Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/Worker.php');
require_once('util/log.php');
require_once('util/mail.php');
require_once('util/mailSchedule.php');
require_once('util/session.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');

$author = getWorkerAuthenticated();
$expo = Expo::selectActive($author->workerid);
$subject = swwat_parse_string(html_entity_decode($_POST[PARAM_SUBJECT_MESSAGE]));
$message = swwat_parse_string(html_entity_decode($_POST[PARAM_MESSAGE]));

$to = "a@emailxl.com";
$message .= "\n\n\n...............................\nStandard Data Included\n...............................";
$message .= "\nfrom: $author->lastName, $author->firstName";
$message .= "\nrole: " . RoleEnum::getString($author->authrole);
$message .= "\nemail: $author->email";
$message .= "\nphone: " . swwat_format_phone($author->phone);
$message .= "\nstatus: " . ($author->isDisabled ? "disabled" : "enabled");
if (!is_null($expo))
{
    $message .= "\ncurrent expo: " . $expo->titleString();
    $message .= "\nschedule:\n";

    $savList = ShiftAssignmentView::selectWorker($expo->expoid, $author->workerid);
    $message .= sprintfSchedule($savList);
}
else
{
    $message .= "no current expo";
}
$message .= "\n\n";

FormMail::send($to, $subject, $message);

header('Location: WorkerViewPage.php');
include('WorkerViewPage.php');

?>
