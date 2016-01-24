<?php // $Id: ShiftAssignmentView.php 2330 2012-10-03 19:07:43Z ecgero $ Copyright (c) SwiftStation, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/ShiftAssignment.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');
require_once('util/log.php');


define("SHIFTASSIGNMENTVIEW_SELECT_PREFIX",
    "SELECT DISTINCT workerid, jobid, stationid, expoid, stationTitle, location, startTime, stopTime, expoTitle, " .
    " minCrew, maxCrew, assignedCrew, minSupervisor, maxSupervisor, assignedSupervisor, jobTitle " .
    " FROM shiftassignmentview WHERE ");
define("SHIFTASSIGNMENTVIEW_START",           " AND startTime > CURRENT_TIMESTAMP ");

define("SHIFTASSIGNMENTVIEW_SELECT_EXPO",     SHIFTASSIGNMENTVIEW_SELECT_PREFIX . " expoid = ? ");
define("SHIFTASSIGNMENTVIEW_SELECT_STATION",  SHIFTASSIGNMENTVIEW_SELECT_EXPO . " AND stationid = ? ");
define("SHIFTASSIGNMENTVIEW_SELECT_WORKER",   SHIFTASSIGNMENTVIEW_SELECT_EXPO . " AND workerid = ?");
define("SHIFTASSIGNMENTVIEW_SELECT_JOB",      SHIFTASSIGNMENTVIEW_SELECT_EXPO . " AND jobid = ?");

define("SHIFTASSIGNMENTVIEW_SELECT_WORKERALL",   SHIFTASSIGNMENTVIEW_SELECT_PREFIX . " workerid = ?");
define("SHIFTASSIGNMENTVIEW_SELECT_EXISTS",   SHIFTASSIGNMENTVIEW_SELECT_EXPO . " AND stationid = ? AND workerid = ? ");
define("SHIFTASSIGNMENTVIEW_SELECT_DATE",     SHIFTASSIGNMENTVIEW_SELECT_EXPO . " AND cast(startTime AS DATE) = ? ");

function ShiftAssignmentCompare($a, $b) {  return $a->compare($b);  }

/**
 * This class is SELECT-only and is used by the UI to assemble lists.
 */
class ShiftAssignmentView extends ShiftAssignment
{

// from superclass
// public $workerid;
// public $jobid;
// public $stationid;
// public $expoid;
public $stationTitle;
public $location;
public $startTime;
public $stopTime;
public $expoTitle;
public $minCrew;
public $maxCrew;
public $assignedCrew;
public $minSupervisor;
public $maxSupervisor;
public $assignedSupervisor;
public $jobTitle;
public $URL;
public $instruction;

public function titleString()
{
    return "$this->stationTitle @ $this->location - $this->jobTitle";
}

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

private static function select($sql, $params)
{
    try
    {
        $rows = simpleSelect("ShiftAssignmentView", $sql, $params);
        foreach ($rows as $row)
        {
            $row->fixDates();
        }
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftAssignmentView::select('. $sql . ',  ' . var_export($params, true) . ')', $pe->getMessage());
    }
} // select

public static function selectDate($expoId, $date)
{
    return self::select(SHIFTASSIGNMENTVIEW_SELECT_DATE, array($expoId, swwat_format_isodate($date)));
} // selectDate

public static function selectExpo($expoId)
{
    return self::select(SHIFTASSIGNMENTVIEW_SELECT_EXPO, array($expoId));
} // selectExpo

public static function selectStation($expoId, $stationId)
{
    return self::select(SHIFTASSIGNMENTVIEW_SELECT_STATION, array($expoId, $stationId));
} // selectStation

public static function selectJob($expoId, $jobId)
{
    return self::select(SHIFTASSIGNMENTVIEW_SELECT_JOB, array($expoId, $jobId));
} // selectJob

public static function selectWorker($expoId, $workerId)
{
    if (is_null($expoId))
    {
        return self::select(SHIFTASSIGNMENTVIEW_SELECT_WORKERALL, array($workerId));
    }
    return self::select(SHIFTASSIGNMENTVIEW_SELECT_WORKER, array($expoId, $workerId));
} // selectWorker

public static function isWorkerAssignedStation($workerId, $expoId, $stationId)
{
    $rows = self::select(SHIFTASSIGNMENTVIEW_SELECT_EXISTS, array($expoId, $stationId, $workerId));
    return (count($rows) != 0);
} // isWorkerAssignedStation

public function compare($otherStation)
{
	if ($this->stationid == $otherStation->stationid)  {  return 0;  }

	$dc = datetimeCompare($this->startTime, $otherStation->startTime);
	if (0 != $dc)  {  return $dc;  }

	$dc = datetimeCompare($this->stopTime, $otherStation->stopTime);
	if (0 != $dc)  {  return $dc;  }

	$c = strcasecmp($this->location, $otherStation->location);
	if (0 != $c)  {  return $c;  }

	return strcmp($this->stationTitle, $otherStation->stationTitle);
} // compare

} // ShiftAssignmentView

?>
