<?php  // $Id: Job.php 1864 2012-09-08 16:04:29Z ecgero $ Copyright (c) SwiftStation, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/Station.php');
require_once('util/date.php');
require_once('util/log.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');


define("JOB_SELECT_PREFIX", "SELECT DISTINCT jobid, stationid, expoid, maxCrew, minCrew, assignedCrew, maxSupervisor, minSupervisor, assignedSupervisor, jobTitle, stationTitle, location, startTime, stopTime FROM jobview ");
define("JOB_SELECT_ID",                 JOB_SELECT_PREFIX . " WHERE jobid = ? ");
define("JOB_SELECT_EXPO",               JOB_SELECT_PREFIX . " WHERE expoid = ? ");
define("JOB_SELECT_STATION",            JOB_SELECT_EXPO   . " AND stationid = ? ");
define("JOB_SELECT_WORKER",             JOB_SELECT_PREFIX . " WHERE jobid IN (SELECT jobid FROM shiftassignment WHERE workerid = ?)");


function JobCompare($a, $b)  {  return $a->compare($b);  }

class Job
{

public $jobid;
public $stationid;
public $expoid;
public $maxCrew;
public $minCrew;
public $assignedCrew;
public $maxSupervisor;
public $minSupervisor;
public $assignedSupervisor;
public $startTime;
public $stopTime;
public $jobTitle;
public $stationTitle;
public $location;

public function titleString()
{
    return $this->stationTitle . " - " . $this->jobTitle;
} // titleString

private function fixDates()
{
    if (is_string($this->startTime))
    {
        $this->startTime = swwat_parse_datetime($this->startTime);
    }
    if (is_string($this->stopTime))
    {
        $this->stopTime = swwat_parse_datetime($this->stopTime);
    }
} // fixDates

public static function selectID($jobId)
{
    try
    {
        $rows = simpleSelect("Job", STATION_SELECT_ID, array($jobId));
        if (1 != count($rows))
        {
            return NULL;
        }
        $rows[0]->fixDates();
        return $rows[0];
    }
    catch (PDOException $pe)
    {
        logMessage('Job::selectID(' . $jobId . ')', $pe->getMessage());
    }
} // selectID

public static function selectExpo($expoId)
{
    try
    {
        $rows = simpleSelect("Job", JOB_SELECT_EXPO, array($expoId));
        for ($k = 0; $k < count($rows); $k++)
        {
            $rows[$k]->fixDates();
        } // $k
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('Job::selectExpo(' . $expoId . ')', $pe->getMessage());
    }
} // selectExpo

public static function selectStation($expoId, $stationId)
{
    try
    {
        $rows = simpleSelect("Job", JOB_SELECT_STATION, array($expoId, $stationId));
        for ($k = 0; $k < count($rows); $k++)
        {
            $rows[$k]->fixDates();
        } // $k
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('Job::selectStation($expoId, $stationId)', $pe->getMessage());
    }
} // selectStation

public static function selectWorker($workerId, $date = NULL, $expoId = NULL)
{
    try
    {
        $sql = JOB_SELECT_WORKER;
        $params = array();
        $params[] = $workerId;
        if (!is_null($date))
        {
            $sql .= " AND stopTime > ? ";
            $params[] = swwat_format_isodatetime($date);
        }
        if (!is_null($expoId))
        {
            $sql .= " AND expoid = ? ";
            $params[] = $expoId;
        }

        $rows = simpleSelect("Job", $sql, $params);
        for ($k = 0; $k < count($rows); $k++)
        {
            $rows[$k]->fixDates();
        } // $k
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('Job::selectWorker(' . $workerId . ', ' . $date . ', ' . $expoId . ')', $pe->getMessage());
    }
} // selectWorker

public function insert()
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("INSERT INTO job (expoid, stationid, jobTitle, maxCrew, minCrew, maxSupervisor, minSupervisor) "
                            . " VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array($this->expoid, $this->stationid, $this->jobTitle,
		                     $this->maxCrew, $this->minCrew, $this->maxSupervisor, $this->minSupervisor));
        // now get the workerid
        $this->jobid = $dbh->lastInsertId(); // note before commit
        $dbh->commit();
        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('Job::insert()', $pe->getMessage());
    }
} // insert

public function update()
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE job SET jobTitle = ?, "
                            . " maxCrew = ?, minCrew = ?, maxSupervisor = ?, minSupervisor = ? "
                            . " WHERE jobid = ?");
        $stmt->execute(array($this->jobTitle, $this->maxCrew, $this->minCrew, $this->maxSupervisor, $this->minSupervisor, $this->jobid));
		$dbh->commit();
        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('Job::update()', $pe->getMessage());
    }
} // update

public function delete()
{
	try
	{
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        // only permit delete in future
		$stmt = $dbh->prepare("DELETE job FROM job, station s WHERE job.jobid = ? AND job.stationid = s.stationid AND s.startTime > CURRENT_TIMESTAMP");
		$stmt->execute(array($this->jobid));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('Job::delete()', $pe->getMessage());
	}
} //delete

public function compare($otherJob)
{
	if ($this->jobid == $otherJob->jobid)  {  return 0;  }

	// note ASCending order
	$dc = datetimeCompare($this->startTime, $otherJob->startTime);
	if (0 != $dc)  {  return $dc;  }

	// note ASCending order
	$dc = datetimeCompare($this->stopTime, $otherJob->stopTime);
	if (0 != $dc)  {  return $dc;  }

	$c = strcasecmp($this->location, $otherJob->location);
	if (0 != $c)  {  return $c;  }

	return strcmp($this->jobTitle, $otherJob->jobTitle);
} // compare

} // Job

?>
