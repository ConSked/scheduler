<?php  // $Id: Expo.php 2406 2012-10-22 19:26:26Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/date.php');
require_once('util/log.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');


define("EXPO_SELECT_PREFIX",     "SELECT DISTINCT expoid, startTime, stopTime, expoHourCeiling, title, description, scheduleAssignAsYouGo, scheduleVisible, allowScheduleTimeConflict, newUserAddedOnRegistration FROM ");
define("EXPO_SELECT_ID",         EXPO_SELECT_PREFIX . " expo WHERE expoid = ?");
define("EXPO_SELECT_MULTIPLE",   EXPO_SELECT_PREFIX . " expo");
define("EXPO_SELECT_WORKER",     EXPO_SELECT_PREFIX . " expoworkerview WHERE workerid = ? ORDER BY stopTime");


function ExpoCompare($a, $b)  {  return $a->compare($b);  }

class Expo
{

public $expoid;
public $startTime;
public $stopTime;
public $expoHourCeiling;
public $title;
public $description;
public $scheduleAssignAsYouGo = TRUE;
public $scheduleVisible = TRUE;
public $allowScheduleTimeConflict = FALSE;
public $newUserAddedOnRegistration = TRUE;
//public $scheduleWorkerReset = TRUE; // todo implement BZ 158

public function titleString()
{
    return $this->title . "  (" . swwat_format_expodate($this->startTime, $this->stopTime) . ")";
} // descriptionString

private function fixDates()
{
    if (is_string($this->startTime))
    {
        $this->startTime = swwat_parse_date($this->startTime);
    }
    if (is_string($this->stopTime))
    {
        $this->stopTime = swwat_parse_date($this->stopTime);
    }
    $this->scheduleAssignAsYouGo = (1 == $this->scheduleAssignAsYouGo);
    $this->scheduleVisible = (1 == $this->scheduleVisible);
    $this->allowScheduleTimeConflict = (1 == $this->allowScheduleTimeConflict);
    $this->newUserAddedOnRegistration = (1 == $this->newUserAddedOnRegistration);
    // $this->scheduleWorkerReset = (1 == this->$scheduleWorkerReset);
} // fixDates

public static function selectID($expoId)
{
    try
    {
        $rows = simpleSelect("Expo", EXPO_SELECT_ID, array($expoId));
        if (1 != count($rows))
        {
            return NULL;
        }
        $rows[0]->fixDates();
        return $rows[0];
    }
    catch (PDOException $pe)
    {
        logMessage('Expo::selectID(' . $expoId . ')', $pe->getMessage());
    }
} // selectID

public static function selectMultiple()
{
    try
    {
        $rows = simpleSelect("Expo", EXPO_SELECT_MULTIPLE);
        for ($k = 0; $k < count($rows); $k++)
        {
            $rows[$k]->fixDates();
        } // $k
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('Expo::selectMultiple()', $pe->getMessage());
    }
} // selectMultiple

public static function selectWorker($workerId)
{
    try
    {
        $rows = simpleSelect("Expo", EXPO_SELECT_WORKER, array($workerId));
        for ($k = 0; $k < count($rows); $k++)
        {
            $rows[$k]->fixDates();
        } // $k
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('Expo::selectWorker(' . $workerId . ')', $pe->getMessage());
    }
} // selectWorker

/**
 * Returns back the 'active' Expo (current time or next in future)
 * NULL if none currently or no future expo
 */
public static function selectActive($workerId)
{
	$expoList = Expo::selectWorker($workerId);
    usort($expoList, "ExpoCompare");
    $expoList = array_reverse($expoList);
	foreach ($expoList as $exp)
	{
		if (!$exp->isPast())
		{
			return $exp;
		}
	}
	return NULL;
} // selectActive

public function insert()
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("INSERT INTO expo (startTime, stopTime, expoHourCeiling, title, description, scheduleAssignAsYouGo, scheduleVisible, allowScheduleTimeConflict, newUserAddedOnRegistration) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (is_null($this->startTime))
        {
            $this->startTime = new DateTime();
        }
        if (is_null($this->stopTime))
        {
            $this->stopTime = new DateTime();
        }
        $stmt->execute(array(swwat_format_isodatetime($this->startTime),
                             swwat_format_isodatetime($this->stopTime),
                             $this->expoHourCeiling,
                             $this->title, $this->description,
                             $this->scheduleAssignAsYouGo, $this->scheduleVisible,
		                     $this->allowScheduleTimeConflict, $this->newUserAddedOnRegistration));
        // now get the workerid
        $this->expoid = $dbh->lastInsertId(); // note before commit
        $dbh->commit();
        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('Expo::insert()', $pe->getMessage());
    }
} // insert

public function update()
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE expo SET startTime = ?, stopTime = ?, "
                            . " expoHourCeiling = ?, title = ?, description = ?, "
                            . " scheduleAssignAsYouGo = ?, scheduleVisible = ?, "
		                    . " allowScheduleTimeConflict = ?, newUserAddedOnRegistration = ? "
                            . " WHERE expoid = ?");
        if (is_null($this->startTime))
        {
            $this->startTime = new DateTime();
        }
        if (is_null($this->stopTime))
        {
            $this->stopTime = new DateTime();
        }
        $stmt->execute(array(swwat_format_isodatetime($this->startTime),
                             swwat_format_isodatetime($this->stopTime),
                             $this->expoHourCeiling, $this->title, $this->description,
                             $this->scheduleAssignAsYouGo, $this->scheduleVisible,
		                     $this->allowScheduleTimeConflict, $this->newUserAddedOnRegistration,
                             $this->expoid));
        $dbh->commit();
        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('Expo::update()', $pe->getMessage());
    }
} // update


/**
 * This method compares expoid, then startTime, then stopTime, then title
 */
public function compare($otherExpo)
{
    if ($this->expoid == $otherExpo->expoid)  {  return 0;  }

    // note ASCending order
    $dc = dateCompare($otherExpo->startTime, $this->startTime);
    if (0 != $dc)  {  return $dc;  }

    // note ASCending order
    $dc = dateCompare($otherExpo->stopTime, $this->stopTime);
    if (0 != $dc)  {  return $dc;  }

    return strcmp($this->title, $otherExpo->title);
} // compare

public function isFuture()
{
	$date = new DateTime();
	return (dateCompare($this->startTime, $date) <= 0);

} // isFuture

public function isPast()
{
    $date = new DateTime();
    return (dateCompare($date, $this->stopTime) > 0);
} // isPast

public function isRunning()
{
    $date = new DateTime();
    return (dateCompare($date, $this->stopTime) < 0 && dateCompare($date, $this->startTime) > 0);
} // isRunning

/**
 * returns an array of Worker which is assigned to the expo, but not in the list
 */
public function oddWorkerList($workerList)
{
    $oddWorkerList = Worker::selectExpo($this->expoid);
    for ($k = 0; $k < count($oddWorkerList); $k++)
    {
        $worker = $oddWorkerList[$k];
        if (in_array($worker, $workerList))
        {
            $oddWorkerList[$k] = NULL;
        }
    }
    return array_filter($oddWorkerList); // removes NULL but does not re-index
} // oddWorkerList


} // Expo

?>
