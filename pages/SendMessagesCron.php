<?php  // $Id: SendMessagesCron.php 2329 2012-10-03 16:18:45Z wnm $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/Expo.php');
require_once('db/MessageData.php');
require_once('db/ReminderSent.php');
require_once('db/ShiftAssignmentView.php');
require_once('swwat/gizmos/format.php');
require_once('util/date.php');
require_once('util/log.php');
require_once('util/mail.php');

$date = new DateTime();
$targtag = $date->modify('+'.PARAM_ANTEREMINDER_TIME.' day');

$messagedata = array(); // will become array of array
$email_array = array();
logMessage("SendMessagesCron","called.");
if (ReminderSent::selectDate(swwat_format_isodate($targtag)))
{
	logMessage("SendMessagesCron","emails for ".swwat_format_isodate($targtag)." already sent out.");
	exit;
}

$expoList = Expo::selectMultiple();
foreach ($expoList as $expo)
{
    if (!$expo->isPast() && $expo->scheduleVisible)
    {
        $shiftassignments = ShiftAssignmentView::selectDate($expo->expoid, $targtag);
        foreach ($shiftassignments as $shift)
        {
			if (strcmp(substr(strtolower($shift->stationTitle), 0, 5), "can't"))
			{
            	$message = new MessageData();
	            $message->fillData($shift);
	            $email = $message->workerEmail; // just a convenience
	            if (is_null($email))
	            {
	                logMessage("SendMessagesCron", "Worker has null email:" . var_export($worker, true));
	                continue;
	            }
	            if (!in_array($email, $email_array))
	            {
	                $email_array[] = $email;
	                $messagedata[$email] = array(); // first time
	            }
	            $messagedata[$email][] = $message;
			}
        } // $shift
    }
} // $expo
// create data array

// loop over array of emails
foreach ($email_array as $email)
{
    $messages = $messagedata[$email];

    $subject = $messages[0]->expo." Reminder";

    $message = "Dear ".$messages[0]->workerName.",\n\n";
	if (PARAM_ANTEREMINDER_TIME == 1)
	{
    	$message .= "This message is to remind you that your upcoming shift assignment for ".PARAM_ANTEREMINDER_TIME." day from now is:\n\n";
	}
	else
	{
    	$message .= "This message is to remind you that your upcoming shift assignment for ".PARAM_ANTEREMINDER_TIME." days from now is:\n\n";
	}
    $message .= "Expo: ".$messages[0]->expo."\n\n";

	$body = $message;

    foreach ($messages as $message)
    {
        $body .= "Station: ".$message->station."\n";
        $shift = explode(';', swwat_format_shift($message->startTime, $message->stopTime));
        $body .= "Time: ".$shift[0]." (".$shift[1].")\n";
    } // $message

    //START SIGNATURE BLOCK
    $message .= "\n\n Your participation in the conference is appreciated.";
    //    $message .= "\n\n Please notify us at zzz-xxx-yyyy if unable to make your shift.";
    $message .= "\n\n Thank you,";
    $message .= "\n\n Your SwiftExpo Team";
    // ENDIT SIGNATURE BLOCK


    FormMail::send($email, $subject, $body);
    logMessage("SendMessagesCron","email sent to " . $email);
} // $email

ReminderSent::insert(swwat_format_isodate($targtag));
logMessage("SendMessagesCron","Date: ".swwat_format_isodate($targtag)." written to database." );

?>
