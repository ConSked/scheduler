<?php  // $Id: ReminderSent.php 2328 2012-10-03 04:24:29Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/log.php');

define("REMINDERSEND_SELECT", "SELECT DISTINCT date FROM remindersent WHERE date = ?");

class ReminderSent
{
public $date;

public static function selectDate($date)
{
	try
	{
		$rows = simpleSelect("ReminderSent", REMINDERSEND_SELECT, array($date));
		if (0 < count($rows))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	catch (PDOException $pe)
	{
		logMessage('ReminderSent::selectID(' . $date . ')', $pe->getMessage());
	}
}

public static function insert($date)
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("INSERT INTO remindersent (date) VALUES (?)");
		$stmt->execute(array($date));
		$dbh->commit();
		return;
	}
	catch (PDOException $pe)
	{
		logMessage('ReminderSend::insert(' . $date . ')', $pe->getMessage());
	}
}

}
?>
