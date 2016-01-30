<?php // $Id: SendMessageAction.php 921 2012-07-14 16:03:23Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('util/log.php');
require_once('util/mail.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

$author = getWorkerAuthenticated();

$type = swwat_parse_string(html_entity_decode($_POST[PARAM_TYPE_MESSAGE]), true);
$typeFlag = (0 != strcmp($type, PARAM_SMS_SERVICE)); // email TRUE (default), sms FALsE
$subject = swwat_parse_string(html_entity_decode($_POST[PARAM_SUBJECT_MESSAGE]));
$message = swwat_parse_string(html_entity_decode($_POST[PARAM_MESSAGE]));
$list = $_POST[PARAM_LIST_INDEX];

if (!is_null($list) && (
    ($typeFlag && (!is_null($subject) || !is_null($message)))
    ||
    (!$typeFlag && !is_null($message))
   ))
{
    if (!$typeFlag)
    {
        $subject = ""; // ensure blank
        $message = substr($message, 0, 160);
    }
    $workerList = $_SESSION[PARAM_LIST];
    for ($k = 0; $k < count($list); $k++)
    {
        try
        {
            $listIndex = swwat_parse_number(html_entity_decode($list[$k]), FALSE);
            $worker = $workerList[$listIndex];
            $to = $typeFlag ? $worker->email : $worker->smsemail;
            if (!is_null($to) && (strlen($to) > 0))
            {
                FormMail::send($to, $subject, $message);
            }
            else
            {
                /* continue to process list */
                logMessage("SendMessageAction", "failure with to field:" . $to . " index:" . $k . " value:" . $list[$k]);
            }
        }
        catch (ParseSWWATException $pe)
        {
            /* continue to process list */
            logMessage("SendMessageAction", "failure with index:" . $k . " value:" . $list[$k]);
        }
    } // $k
} // all null

// return to whence we came
if (is_null(getStationCurrent()))
{
    if (is_null(getExpoCurrent()))
    {
        if (is_null(getWorkerCurrent()))
        {
            header('Location: WorkerListPage.php');
            include('WorkerListPage.php');
            return;
        }
        else // from WorkerViewPage
        {
            header('Location: WorkerViewPage.php');
            include('WorkerViewPage.php');
            return;
        }
    }
    else
    {
        header('Location: ExpoViewPage.php');
        include('ExpoViewPage.php');
        return;
    }
}
else
{
    header('Location: StationViewPage.php');
    include('StationViewPage.php');
    return;
}

?>
