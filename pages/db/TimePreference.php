<?php  // $Id: TimePreference.php 2200 2012-09-22 18:26:20Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/log.php');

define("SHIFT_SELECT", "SELECT DISTINCT workerid, shift1, shift2, shift3, shift4, shift5, shift6, shift7, shift8, shift9, shift10, shift11, shift12, shift13, shift14, shift15, shift16, shift17, shift18, shift19, shift20 FROM timepreference WHERE workerid = ?");
class TimePreference
{
public $workerid;
public $shift1;
public $shift2;
public $shift3;
public $shift4;
public $shift5;
public $shift6;
public $shift7;
public $shift8;
public $shift9;
public $shift10;
public $shift11;
public $shift12;
public $shift13;
public $shift14;
public $shift15;
public $shift16;
public $shift17;
public $shift18;
public $shift19;
public $shift20;

public static function selectID($workerId)
{
	try
	{
		$rows = simpleSelect("TimePreference", SHIFT_SELECT, array($workerId));
		if (1 != count($rows))
		{
			return NULL;
		}
		return $rows[0];
	}
	catch (PDOException $pe)
	{
		logMessage('TimePreference::selectID(' . $workerId . ')', $pe->getMessage());
	}
}

public function insert()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("INSERT INTO timepreference (workerid, shift1, shift2, shift3, shift4, shift5, shift6, shift7, shift8, shift9, shift10, "
		                    . "shift11, shift12, shift13, shift14, shift15, shift16, shift17, shift18, shift19, shift20) "
		                    . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute(array($this->workerid, $this->shift1, $this->shift2, $this->shift3, $this->shift4, $this->shift5, $this->shift6, $this->shift7, $this->shift8, $this->shift9, $this->shift10,
		                     $this->shift11, $this->shift12, $this->shift13, $this->shift14, $this->shift15, $this->shift16, $this->shift17, $this->shift18, $this->shift19, $this->shift20));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('TimePreference::insert()', $pe->getMessage());
	}
}

public function update()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("UPDATE timepreference SET shift1 = ?, shift2 = ?, shift3 = ?, shift4 = ?, shift5 = ?, shift6 = ?, shift7 = ?, shift8 = ?, shift9 = ? , shift10 = ?, "
		                    . "shift11 = ?, shift12 = ?, shift13 = ?, shift14 = ?, shift15 = ?, shift16 = ?, shift17 = ?, shift18 = ?, shift19 = ?, shift20 = ? "
                            . "WHERE workerid = ?");
		$stmt->execute(array($this->shift1, $this->shift2, $this->shift3, $this->shift4, $this->shift5, $this->shift6, $this->shift7, $this->shift8, $this->shift9, $this->shift10,
		                     $this->shift11, $this->shift12, $this->shift13, $this->shift14, $this->shift15, $this->shift16, $this->shift17, $this->shift18, $this->shift19, $this->shift20,
		                     $this->workerid));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('TimePreference::update()', $pe->getMessage());
	}
}

}
?>
