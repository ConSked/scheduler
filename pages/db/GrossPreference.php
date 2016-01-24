<?php  // $Id: GrossPreference.php 2128 2012-09-21 19:38:39Z ecgero $ Copyright (c) SwiftStation, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/ShiftPreference.php');
require_once('util/log.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');

// use JobTitle to get unique job titles
define("GROSSPREF_SELECT_DESCRIPTION", "SELECT DISTINCT description FROM station WHERE expoid = ? ORDER BY stationid ASC");
define("GROSSPREF_SELECT_TITLE",    "SELECT DISTINCT stationTitle FROM station WHERE expoid = ? ORDER BY stationTitle ASC");
define("GROSSPREF_SELECT_LOCATION", "SELECT DISTINCT location FROM station WHERE expoid = ? ORDER BY location ASC");
define("GROSSPREF_SELECT_DATE",     "SELECT DISTINCT cast(startTime AS DATE) AS startDate FROM station WHERE expoid = ? ORDER BY startDate ASC");
define("GROSSPREF_SELECT_TIMESPAN",     "SELECT DISTINCT cast(startTime AS TIME) AS startTime, cast(stopTime AS TIME) AS stopTime FROM station WHERE expoid = ? ORDER BY startTime ASC, stopTime ASC");
define("GROSSPREF_SELECT_DATESPAN", "SELECT DISTINCT startTime, stopTime FROM station WHERE expoid = ? ORDER BY startTime ASC, stopTime ASC");
define("GROSSPREF_SELECT_WORKERS", "SELECT DISTINCT workerid FROM shiftpreference WHERE expoid = ? ORDER BY workerid ASC");
// #jobs update
define("GROSSPREF_UPDATE_STATION",   "UPDATE shiftpreference sp, station s SET desirePercent = ? "
    . " WHERE sp.stationid = s.stationid "
    . " AND sp.expoid = ? AND workerid = ? "
    . " AND location = ? AND cast(startTime AS DATE) = cast(? AS DATE) "
    . " AND cast(startTime AS TIME) = cast(? AS TIME) AND cast(stopTime AS TIME) = cast(? AS TIME) ");

class GrossPreference
{

private static function select($sql, $expoId)
{
    $dbh = getPDOConnection();
    $stmt = $dbh->prepare($sql);
    $stmt->execute(array($expoId));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} // select

public static function selectTitle($expoId)
{
    try
    {
        $rows = self::select(GROSSPREF_SELECT_TITLE, $expoId);
        $values = array();
        foreach ($rows as $row)
        {
            $values[] = $row['stationTitle'];
        }
        $rows = NULL;
		return $values;
    }
    catch (PDOException $pe)
    {
        logMessage('GrossPreference::selectTitle(' . $expoId . ')', $pe->getMessage());
    }
} // selectTitle

public static function selectLocation($expoId)
{
    try
    {
        $rows = self::select(GROSSPREF_SELECT_LOCATION, $expoId);
        $values = array();
        foreach ($rows as $row)
        {
            $values[] = $row['location'];
        }
        $rows = NULL;
		return $values;
    }
    catch (PDOException $pe)
    {
        logMessage('GrossPreference::selectLocation(' . $expoId . ')', $pe->getMessage());
    }
} // selectLocation

public static function selectDescription($expoId)
{
    try
    {
        $rows = self::select(GROSSPREF_SELECT_DESCRIPTION, $expoId);
        $values = array();
        foreach ($rows as $row)
        {
            $values[] = $row['description'];
        }
        $rows = NULL;
                return $values;
    }
    catch (PDOException $pe)
    {
        logMessage('GrossPreference::selectDescription(' . $expoId . ')', $pe->getMessage());
    }
} // selectDescription

public static function selectWorkers($expoId)
{
    try
    {
        $rows = self::select(GROSSPREF_SELECT_WORKERS, $expoId);
        $values = array();
        foreach ($rows as $row)
        {
            $values[] = $row['workerid'];
        }
        $rows = NULL;
		return $values;
    }
    catch (PDOException $pe)
    {
        logMessage('GrossPreference::selectWorkers(' . $expoId . ')', $pe->getMessage());
    }
} // selectWorkers

// arbitrary as long as parseDateKey agrees
const DATE_FORMAT = "Y-m-d";
public static function formatDateKey(DateTime $date)
{
    return date_format($date, self::DATE_FORMAT);
} // formatDateKey

// arbitrary as long as formatDateKey agrees
public static function parseDateKey($key)
{
    return DateTime::createFromFormat(self::DATE_FORMAT, $key);
} // parseDateKey

/**
 * @see formatDateKey
 * @return array of string of form '2012-10-02'
 */
public static function selectDate($expoId)
{
    try
    {
        $rows = self::select(GROSSPREF_SELECT_DATE, $expoId);
        $values = array();
        foreach ($rows as $row)
        {
            $values[] = self::formatDateKey(swwat_parse_date($row['startDate']));
        }
        $rows = NULL;
		return $values;
    }
    catch (PDOException $pe)
    {
        logMessage('GrossPreference::selectDate(' . $expoId . ')', $pe->getMessage());
    }
} // selectDate

// arbitrary as long as parseTimeSpanKey agrees
const SPAN_FORMAT_TIME = "H:i";
public static function formatTimeSpanKey(DateTime $start, DateTime $stop)
{
    return sprintf("%s - %s", date_format($start, self::SPAN_FORMAT_TIME), date_format($stop, self::SPAN_FORMAT_TIME));
} // formatTimeSpanKey

// arbitrary as long as formatTimeSpanKey agrees
public static function parseTimeSpanKey($key)
{
    $strs = sscanf($key, "%s - %s");
    $times = array();
    $times[] = DateTime::createFromFormat(self::SPAN_FORMAT_TIME, $strs[0]);
    $times[] = DateTime::createFromFormat(self::SPAN_FORMAT_TIME, $strs[1]);
    $strs = NULL;
    return $times;
} // parseTimeSpanKey

/**
 * @see formatTimeKey
 * @return array of string of form '10:00 - 11:00'
 */
public static function selectTimeSpan($expoId)
{
    try
    {
        $rows = self::select(GROSSPREF_SELECT_TIMESPAN, $expoId);
        $values = array();
        foreach ($rows as $row)
        {
            $values[] = self::formatTimeSpanKey(swwat_parse_time($row['startTime']), swwat_parse_time($row['stopTime']));
        }
        $rows = NULL;
		return $values;
    }
    catch (PDOException $pe)
    {
        logMessage('GrossPreference::selectTime(' . $expoId . ')', $pe->getMessage());
    }
} // selectTimeSpan

/**
 * This method presumes arrays of the following and calls updateRaw to update the db.
 *
 *  $locationDesires = array('registration desk'->23, 'lobby'->50);
 *  $dateDesires = array('2012-10-02'->NULL, '2012-10-02'->100);
 *  $timeSpanDesires = array('10:00 - 11:00'->NULL, '12:00 - 12:00'->100);
 *  ....
 *  $jobDesires can be either NULL/array() to indicates ignore the jobs.
 * note all arrays can include NULL as the value.
 */
public static function updateHelper_Location_Date_TimeSpan($expoId, $workerId, array $locationDesires, array $dateDesires, array $timeSpanDesires)
{
    $locationKeys = array_keys($locationDesires);
    $dateKeys = array_keys($dateDesires);
    $timeSpanKeys = array_keys($timeSpanDesires);
    try
    {
        $updates = 0;
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        foreach ($locationKeys as $location)
        {
            $desireLocation = $locationDesires[$location];
            foreach ($dateKeys as $dateKey)
            {
                $desireDate = $dateDesires[$dateKey];
                $stationDate = self::parseDateKey($dateKey);
                foreach ($timeSpanKeys as $timeSpan)
                {
                    $desireTime = $timeSpanDesires[$timeSpan];
                    $times = self::parseTimeSpanKey($timeSpan);
                    $startTime = $times[0];
                    $stopTime = $times[1];
                    $times = NULL;
                    $updates += self::updateRaw($dbh, $desireLocation, $desireDate, $desireTime,
                        $expoId, $workerId, $location, $stationDate, $startTime, $stopTime);
                } // $timeSpan
            } // $dateKey
        } // $location
        $dbh->commit();
        return $updates;
    }
    catch (PDOException $pe)
    {
        logMessage("GrossPreference::updateHelper_Location_Date_TimeSpan($expoId, $workerId, ...)", $pe->getMessage());
        throw $pe;
    }
} // updateHelper_Location_Date_TimeSpan

// arbitrary as long as parseDateSpanKey agrees
const DATESPAN_FORMAT_DATE = "Y-m-d"; // note is independent of DATE_FORMAT, etc
const DATESPAN_FORMAT_TIME = "H:i";
public static function formatDateSpanKey(DateTime $start, DateTime $stop)
{
    // full is "Y-m-d H:i - H:i"
    return sprintf("%s %s - %s",
        date_format($start, self::DATESPAN_FORMAT_DATE),
        date_format($start, self::DATESPAN_FORMAT_TIME),
        date_format($stop, self::DATESPAN_FORMAT_TIME));
} // formatDateSpanKey

// arbitrary as long as formatDateSpanKey agrees
public static function parseDateSpanKey($key)
{
    $strs = sscanf($key, "%s %s - %s");
    $times = array();
    $times[] = DateTime::createFromFormat(self::DATESPAN_FORMAT_DATE, $strs[0]);
    $times[] = DateTime::createFromFormat(self::DATESPAN_FORMAT_TIME, $strs[1]);
    $times[] = DateTime::createFromFormat(self::DATESPAN_FORMAT_TIME, $strs[2]);
    $strs = NULL;
    return $times;
} // parseDateSpanKey

/**
 * @see formatDateTimeKey
 * @return array of string of form '2012-10-02 10:00 - 11:00'
 */
public static function selectDateSpan($expoId)
{
    try
    {
        $rows = self::select(GROSSPREF_SELECT_DATESPAN, $expoId);
        $values = array();
        foreach ($rows as $row)
        {
            $values[] = self::formatDateSpanKey(swwat_parse_datetime($row['startTime']), swwat_parse_datetime($row['stopTime']));
        }
        $rows = NULL;
		return $values;
    }
    catch (PDOException $pe)
    {
        logMessage('GrossPreference::selectDateSpan(' . $expoId . ')', $pe->getMessage());
    }
} // selectDateSpan

/**
 *  This method presumes arrays of the following and calls updateRaw to update the db.
 *
 *  $locationDesires = array('registration desk'->23, 'lobby'->50);
 *  $dateSpanDesires = array('2012-10-02 10:00 - 11:00'->NULL, '2012-10-02 12:00 - 12:00'->100);
 */
public static function updateHelper_Location_DateSpan($expoId, $workerId, array $locationDesires, array $dateSpanDesires)
{
    $locationKeys = array_keys($locationDesires);
    $dateSpanKeys = array_keys($dateSpanDesires);
    try
    {
        $updates = 0;
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        foreach ($locationKeys as $location)
        {
            $desireLocation = $locationDesires[$location];
            foreach ($dateSpanKeys as $dateSpanKey)
            {
                $desireDateSpan = $dateSpanDesires[$dateSpanKey];
                // the formula in update raw = (dD + tD)/2; in this case D = (D+D)/2
                $dateTimeTime = self::parseDateSpanKey($dateSpanKey);
                $updates += self::updateRaw($dbh, $desireLocation, $desireDateSpan, $desireDateSpan,
                    $expoId, $workerId, $location, $dateTimeTime[0], $dateTimeTime[1], $dateTimeTime[2]);
            } // $dateSpanKey
        } // $location
        $dbh->commit();
        return $updates;
    }
    catch (PDOException $pe)
    {
        logMessage("GrossPreference::updateHelper_Location_DateSpan($expoId, $workerId, ...)", $pe->getMessage());
        throw $pe;
    }
} // updateHelper_Location_DateSpan

/**
 * This method updates a calculated desire;
 * use updateRaw for raw desires.
 *
 * @param $dbh is the PDOConnection this transaction is a part of
 * @throws PDOException
 */
public static function updateCalculated(PDO $dbh, $desirePercent, $expoId, $workerId, $location, DateTime $stationDate, DateTime $startTime, DateTime $stopTime)
{
    // validation check
    if (!is_null($desirePercent))
    {
        if ($desirePercent > 100.0)
        {
            $desirePercent = 100.0;
        }
        if ($desirePercent < 0.0)
        {
            $desirePercent = 0.0;
        }
        $desirePercent = round($desirePercent);
    }
    $sql = GROSSPREF_UPDATE_STATION;
    $params = array($desirePercent, $expoId, $workerId,
        $location,
        swwat_format_isodate($stationDate),
        swwat_format_isotime($startTime),
        swwat_format_isotime($stopTime));
    // now onto the update
    $stmt = $dbh->prepare($sql);
    return $stmt->execute($params);
} // updateCalculated

/**
 * This method calculates desire then calls updateCalculated.
 *
 * @param $dbh is the PDOConnection this transaction is a part of
 * @throws PDOException
 */
public static function updateRaw(PDO $dbh, $desireLocation, $desireDate, $desireTime, $expoId, $workerId, $location, DateTime $stationDate, DateTime $startTime, DateTime $stopTime)
{
    $desirePercent = 0;
    if (is_null($desireLocation) || is_null($desireDate) || is_null($desireTime))
    {
        $desirePercent = NULL; // NULL if any are NULL
    }
    else
    {
        $desirePercent = ($desireLocation + ($desireDate + $desireTime)/2)/2;
    }
    return self::updateCalculated($dbh, $desirePercent, $expoId, $workerId, $location, $stationDate, $startTime, $stopTime);
} // updateRaw

} // GrossPreference

?>
