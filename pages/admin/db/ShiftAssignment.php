<?php // $Id: ShiftAssignment.php 2427 2003-01-03 21:02:24Z ecgero $ Copyright (c) SwiftStation, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/Worker.php');
require_once('util/log.php');

define("SHIFTASSIGNMENT_SELECT_PREFIX",   "SELECT DISTINCT workerid, jobid, stationid, expoid FROM shiftassignment ");
define("SHIFTASSIGNMENT_SELECT_EXPO",     SHIFTASSIGNMENT_SELECT_PREFIX . " WHERE expoid = ? ");
define("SHIFTASSIGNMENT_SELECT_STATION",  SHIFTASSIGNMENT_SELECT_EXPO . " AND stationid = ? ");
define("SHIFTASSIGNMENT_SELECT_JOB",      SHIFTASSIGNMENT_SELECT_EXPO . " AND jobid = ? ");
define("SHIFTASSIGNMENT_SELECT_WORKER",   SHIFTASSIGNMENT_SELECT_EXPO . " AND workerid = ?");
define("SHIFTASSIGNMENT_SELECT_ID",       SHIFTASSIGNMENT_SELECT_STATION . " AND workerid = ?");

//  AND CURRENT_TIMESTAMP < startTime - permit deleting future only
define("SHIFTASSIGNMENT_DELETE_EXPO", "DELETE FROM shiftassignment WHERE expoid = ? "
     . " AND stationid IN (SELECT stationid FROM station WHERE CURRENT_TIMESTAMP < startTime) ");
define("SHIFTASSIGNMENT_DELETE_ID", "DELETE FROM shiftassignment WHERE expoid = ? AND jobid = ? AND workerid = ? "
     . " AND stationid IN (SELECT stationid FROM station WHERE CURRENT_TIMESTAMP < startTime) ");

class ShiftAssignment
{

public $workerid;
public $jobid;
public $stationid;
public $expoid;

public static function selectExpo($expoId)
{
    try
    {
        $rows = simpleSelect("ShiftAssignment", SHIFTASSIGNMENT_SELECT_EXPO, array($expoId));
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftAssignment::selectExpo(' . $expoId . ')', $pe->getMessage());
    }
} // selectExpo

public static function selectStation($expoId, $stationId)
{
    try
    {
        $rows = simpleSelect("ShiftAssignment", SHIFTASSIGNMENT_SELECT_STATION, array(expoId, $stationId));
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftAssignment::selectExpo(' . $expoId . ', ' . $stationId . ')', $pe->getMessage());
    }
} // selectStation

public static function selectJob($expoId, $jobId)
{
    try
    {
        $rows = simpleSelect("ShiftAssignment", SHIFTASSIGNMENT_SELECT_JOB, array($expoId, $jobId));
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftAssignment::selectExpo(' . $expoId . ', ' . $jobId . ')', $pe->getMessage());
    }
} // selectStation

public static function selectWorker($expoId, $workerId)
{
    try
    {
        $rows = simpleSelect("ShiftAssignment", SHIFTASSIGNMENT_SELECT_WORKER, array($expoId, $workerId));
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftAssignment::selectWorker(' . $expoId . ' ' . $workerId . ')', $pe->getMessage());
    }
} // selectWorker

public function equals($other)
{
    return (($this->workerid  == $other->workerid)
         && ($this->stationid == $other->stationid)
         && ($this->expoid    == $other->expoid));
} // equals

private function insertDuplicate($stmt) // throws PDOException
{
    try
    {
        $stmt->execute(array($this->expoid, $this->stationid, $this->jobid, $this->workerid));
    }
    catch (PDOException $pe)
    {
        /* ignore and don't worry about a duplicate entry */
        if ($pe->getCode() != 23000) // Error: 1022 SQLSTATE: 23000 (ER_DUP_KEY)
        {
            throw $pe;
        }
    }
} // insertDuplicate

public function insert()
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("INSERT INTO shiftassignment (expoid, stationid, jobid, workerid) VALUES (?, ?, ?, ?)");
        $this->insertDuplicate($stmt);
        $dbh->commit();
		logMessage("ShiftAssignment::insert()", "worker:$this->workerid - job:$this->jobid - station:$this->stationid - expo:$this->expoid\n");
        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftAssignment::insert()', $pe->getMessage());
    }
} // insert

public static function insertList($shiftList)
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("INSERT INTO shiftassignment (expoid, stationid, jobid, workerid) VALUES (?, ?, ?, ?)");
        foreach ($shiftList as $assignment)
        {
            $assignment->insertDuplicate($stmt);
			logMessage("ShiftAssignment::insertList()", "worker:$assignment->workerid - job:$assignment->jobid - station:$assignment->stationid - expo:$assignment->expoid\n");
        }
        $dbh->commit();
        return;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftAssignment::insertList()', $pe->getMessage());
    }
} // insertList

public static function deleteList($shiftList)
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare(SHIFTASSIGNMENT_DELETE_ID);
        foreach ($shiftList as $assignment)
        {
            $stmt->execute(array($assignment->expoid, $assignment->jobid, $assignment->workerid));
			logMessage("ShiftAssignment::deleteList()", "worker:$assignment->workerid - job:$assignment->jobid - station:$assignment->stationid - expo:$assignment->expoid\n");
        }
        $dbh->commit();
        return;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftAssignment::deleteList()', $pe->getMessage());
    }
} // deleteList

public static function deleteExpo($expoId)
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare(SHIFTASSIGNMENT_DELETE_EXPO);
		$stmt->execute(array($expoId));
		$dbh->commit();
		logMessage("ShiftAssignment::deleteExpo($expoId)", "worker:$assignment->workerid - job:$assignment->jobid - station:$assignment->stationid - expo:$assignment->expoid\n");
		return;
	}
	catch (PDOException $pe)
	{
		logMessage('ShiftAssignment::deleteExpo('.$expoId.')', $pe->getMessage());
	}
} // deleteExpo

public function delete()
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare(SHIFTASSIGNMENT_DELETE_ID);
        $stmt->execute(array($this->expoid, $this->jobid, $this->workerid));
        $dbh->commit();
		logMessage("ShiftAssignment::delete()", "worker:$this->workerid - job:$this->jobid - station:$this->stationid - expo:$this->expoid\n");
        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftAssignment::delete()', $pe->getMessage());
    }
} // delete

} // ShiftAssignment

?>
