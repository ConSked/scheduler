<?php  // $Id: Station.php 2436 2012-11-30 20:24:43Z ecgero $ Copyright (c) SwiftStation, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/date.php');
require_once('util/log.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');


define("STATION_SELECT_SUFFIX", " ORDER BY startTime ASC, stopTime ASC, stationTitle DESC ");
define("STATION_SELECT_PREFIX", "SELECT DISTINCT stationid, expoid, startTime, stopTime, stationTitle, description, location, URL, instruction FROM ");
define("STATION_SELECT_ID",     STATION_SELECT_PREFIX . " station WHERE stationid = ? " . STATION_SELECT_SUFFIX);
define("STATION_SELECT_EXPO",   STATION_SELECT_PREFIX . " station WHERE expoid = ? " . STATION_SELECT_SUFFIX);
define("STATION_SELECT_WORKER", STATION_SELECT_PREFIX . " assignmentview WHERE workerid = ? " . STATION_SELECT_SUFFIX);


class Station
{

public $stationid;
public $expoid;
public $startTime;
public $stopTime;
public $stationTitle;
public $description;
public $location;
public $URL;
public $instruction;
// public max/min x Super/Crew

public function titleString()
{
    return $this->stationTitle . "  " . swwat_format_shifttime($this->startTime, $this->stopTime);
} // titleString

public function locationString()
{
    return $this->location . "  " . swwat_format_shifttime($this->startTime, $this->stopTime);
} // locationString

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

public static function selectID($stationId)
{
    try
    {
        $rows = simpleSelect("Station", STATION_SELECT_ID, array($stationId));
        if (1 != count($rows))
        {
            return NULL;
        }
        $rows[0]->fixDates();
        return $rows[0];
    }
    catch (PDOException $pe)
    {
        logMessage('Station::selectID(' . $stationId . ')', $pe->getMessage());
    }
} // selectID

public static function selectExpo($expoId)
{
    try
    {
        $rows = simpleSelect("Station", STATION_SELECT_EXPO, array($expoId));
        for ($k = 0; $k < count($rows); $k++)
        {
            $rows[$k]->fixDates();
        } // $k
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('Station::selectExpo(' . $expoId . ')', $pe->getMessage());
    }
} // selectExpo

public static function selectWorker($workerId, $date = NULL, $expoId = NULL)
{
    try
    {
        $sql = STATION_SELECT_WORKER;
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

        $rows = simpleSelect("Station", $sql, $params);
        for ($k = 0; $k < count($rows); $k++)
        {
            $rows[$k]->fixDates();
        } // $k
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('Station::selectWorker(' . $workerId . ', ' . $date . ', ' . $expoId . ')', $pe->getMessage());
    }
} // selectWorker

public function insert()
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("INSERT INTO station (expoid, startTime, stopTime, "
                            . " stationTitle, description, location, instruction) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (is_null($this->startTime))
        {
            $this->startTime = new DateTime();
        }
        if (is_null($this->stopTime))
        {
            $this->stopTime = new DateTime();
        }
        $stmt->execute(array($this->expoid,
		                     swwat_format_isodatetime($this->startTime),
                             swwat_format_isodatetime($this->stopTime),
                             $this->stationTitle, $this->description, $this->location, $this->instruction));
        // now get the workerid
        $this->stationid = $dbh->lastInsertId(); // note before commit
        $dbh->commit();
        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('Station::insert()', $pe->getMessage());
    }
} // insert

public function update()
{
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE station SET startTime = ?, stopTime = ?, "
                            . " stationTitle = ?, description = ?, location = ?, instruction = ? "
                            . " WHERE stationid = ?");
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
                             $this->stationTitle, $this->description, $this->location, $this->instruction, $this->stationid));
		$dbh->commit();
        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('Station::update()', $pe->getMessage());
    }
} // update

public function delete()
{
	try
	{
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        // only permit delete in future
		$stmt = $dbh->prepare("DELETE FROM station WHERE stationid = ? AND expoid = ? AND startTime > CURRENT_TIMESTAMP");
		$stmt->execute(array($this->stationid, $this->expoid));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('Station::delete()', $pe->getMessage());
	}
} //delete

/**
 * This method compares stationid, then startTime, then stopTime, then title
 */
public function compare($otherStation)
{
    if ($this->stationid == $otherStation->stationid)  {  return 0;  }
    if ($this->expoid != $otherStation->expoid)  {  return 1000;  }  // ensuring NOT equal

    // note ASCending order
    $dc = datetimeCompare($otherStation->startTime, $this->startTime);
    if (0 != $dc)  {  return $dc;  }

    // note ASCending order
    $dc = datetimeCompare($otherStation->stopTime, $this->stopTime);
    if (0 != $dc)  {  return $dc;  }

    return strcmp($this->stationTitle, $otherStation->stationTitle);
} // compare


public function isPast()
{
    $date = new DateTime();
    return (dateCompare($date, $this->stopTime) > 0);
} // isPast

public function oddWorkerList($workerList)
{
	$oddWorkerList = Worker::selectStation($this->stationid);
    foreach ($workerList as $worker)
    {
        for ($k = 0; $k < count($oddWorkerList); $k++)
        {
            if (!is_null($oddWorkerList[$k]) && ($worker->workerid == $oddWorkerList[$k]->workerid))
            {
                $oddWorkerList[$k] = NULL;
                break; // onto next worker
            }
        }
    } // $worker
	return array_values(array_filter($oddWorkerList)); // removes NULL but re-indexes
} // oddWorkerList

} // Station

?>
