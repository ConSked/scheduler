<?php  // $Id: mailSchedule.php 2120 2012-09-21 16:12:09Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('db/Expo.php');
require_once('db/ShiftAssignmentView.php');
require_once('db/Worker.php');
require_once('util/log.php');
require_once('util/mail.php');

function sprintfSchedule(array $shiftAssignmentViewList)
{
    if (0 == count($shiftAssignmentViewList))
    {
        return "no stations assigned";
    }
    $body = "";
    $sizeTitle = 0;
    $sizeDate = 0;
    $sizeTime = 0;
    $lines = array(); // convert to array of lines
    foreach ($shiftAssignmentViewList as $shift)
    {
        $stationDateTime = swwat_format_shift($shift->startTime, $shift->stopTime);
        list($stationDate, $stationTime, $ignore) = explode(';', $stationDateTime);
        $line = array(); // convert to array of strings
        if ($sizeTitle < strlen($shift->titleString()))
        {
            $sizeTitle = strlen($shift->titleString());
        }
        if ($sizeDate < strlen($stationDate))
        {
            $sizeDate = strlen($stationDate);
        }
        if ($sizeTime < strlen($stationTime))
        {
            $sizeTime = strlen($stationTime);
        }
        $line[] = $shift->titleString();
        $line[] = $stationDate;
        $line[] = $stationTime;
        $lines[] = $line;
    } // $shift

    $sizeTitle += 2;
    $sizeDate += 2;
    $sizeTime += 2;
    $format = "\n\t%$sizeTitle" . "s%$sizeDate" . "s%$sizeTime" . "s";
    foreach ($lines as $line)
    {
        $body .= sprintf($format, $line[0], $line[1], $line[2]);
    } // $line
    $lines = NULL;
    return $body;
} // sprintfSchedule

function mailSchedule(Expo $expo, Worker $worker)
{
    $savList = ShiftAssignmentView::selectWorker($expo->expoid, $worker->workerid);

    $paramNames = array("FIRSTNAME", "EXPONAME");
    $params = array("FIRSTNAME" => $worker->firstName, "EXPONAME" => $expo->title);
    $body = "Hello FIRSTNAME,\n\nYour schedule for EXPONAME is:\n";

    $body .= sprintfSchedule($savList);

    $body .= "\n\nSincerely,\nThe SwiftShift Team";
    $form = new FormMail($expo->title . " Schedule", $paramNames, $body);
    $form->sendForm($worker->email, $params);
} // mailSchedule

?>
