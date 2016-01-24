<?php  // $Id: JobPreference.php 2200 2012-09-22 18:26:20Z ecgero $ Copyright (c) SwiftStation, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/log.php');

define("JOB_SELECT", "SELECT DISTINCT workerid, job1, job2, job3, job4, job5, job6, job7, job8, job9, job10, job11, job12, job13, job14, job15, job16, job17, job18, job19, job20 FROM jobpreference WHERE workerid = ?");
class JobPreference
{
public $workerid;
public $job1;
public $job2;
public $job3;
public $job4;
public $job5;
public $job6;
public $job7;
public $job8;
public $job9;
public $job10;
public $job11;
public $job12;
public $job13;
public $job14;
public $job15;
public $job16;
public $job17;
public $job18;
public $job19;
public $job20;

public static function selectID($workerId)
{
	try
	{
		$rows = simpleSelect("JobPreference", JOB_SELECT, array($workerId));
		if (1 != count($rows))
		{
			return NULL;
		}
		return $rows[0];
	}
	catch (PDOException $pe)
	{
		logMessage('JobPreference::selectID(' . $workerId . ')', $pe->getMessage());
	}
}

public function insert()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("INSERT INTO jobpreference (workerid, job1, job2, job3, job4, job5, job6, job7, job8, job9, job10, "
		                    . "job11, job12, job13, job14, job15, job16, job17, job18, job19, job20) "
		                    . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute(array($this->workerid, $this->job1, $this->job2, $this->job3, $this->job4, $this->job5, $this->job6, $this->job7, $this->job8,
		                     $this->job9, $this->job10, $this->job11, $this->job12, $this->job13, $this->job14, $this->job15, $this->job16, $this->job17,
		                     $this->job18, $this->job19, $this->job20));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('JobPreference::insert()', $pe->getMessage());
	}
}

public function update()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("UPDATE jobpreference SET job1 = ?, job2 = ?, job3 = ?, job4 = ?, job5 = ?, job6 = ?, job7 = ?, job8 = ?, job9 = ?, job10 = ?, "
		                    . "job11 = ?, job12 = ?, job13 = ?, job14 = ?, job15 = ?, job16 = ?, job17 = ?, job18 = ?, job19 = ?, job20 = ? "
                            . "WHERE workerid = ?");
		$stmt->execute(array($this->job1, $this->job2, $this->job3, $this->job4, $this->job5, $this->job6, $this->job7, $this->job8, $this->job9, $this->job10,
		                     $this->job11, $this->job12, $this->job13, $this->job14, $this->job15, $this->job16, $this->job17, $this->job18, $this->job19, $this->job20,
		                     $this->workerid));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('JobPreference::update()', $pe->getMessage());
	}
}

}
?>
