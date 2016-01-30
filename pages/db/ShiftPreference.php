<?php  // $Id: ShiftPreference.php 2367 2012-10-10 18:30:23Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/log.php');


define("SHIFTPREF_SELECT_PREFIX",     "SELECT DISTINCT workerid, jobid, stationid, expoid, desirePercent FROM shiftpreference WHERE ");
define("SHIFTPREF_SELECT_WORKER",      SHIFTPREF_SELECT_PREFIX . " expoid = ? AND workerid = ? ");
define("SHIFTPREF_SELECT_ID",          SHIFTPREF_SELECT_PREFIX . " workerid = ? AND jobid = ? ");


class ShiftPreference
{

public $workerid;
public $jobid;
public $stationid;
public $expoid;
public $desirePercent;

// convenience function to take floats, etc
public function setDesire($desire)
{
	$this->desirePercent = $desire;
} // setDesire

public static function selectID($workerId, $jobId)
{
    try
    {
        $rows = simpleSelect("ShiftPreference", SHIFTPREF_SELECT_ID, array($workerId, $jobId));
		if (1 != count($rows))
        {
            return NULL;
        }
		return $rows[0];
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftPreference::selectID(' . $expoId . ', ' . $workerId . ', ' . $stationId . ')', $pe->getMessage());
    }
} // selectID

public static function selectWorker($expoId, $workerId)
{
    try
    {
        return simpleSelect("ShiftPreference", SHIFTPREF_SELECT_WORKER, array($expoId, $workerId));
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftPreference::selectWorker(' . $expoId . ', ' . $workerId . ')', $pe->getMessage());
    }
} // selectWorker

public static function preferencesEntered($expoId, $workerId)
{
    try
    {
		$preferences = self::selectWorker($expoId, $workerId);

		$preferencesEntered = false;
		foreach ($preferences as $p)
		{
			if ($p->desirePercent != NULL)
			{
				$preferencesEntered = true;
			}
		}
		return $preferencesEntered;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftPreference::preferencesEntered(' . $expoId . ', ' . $workerId . ')', $pe->getMessage());
    }

} // preferencesEntered

public function update()
{
    // validation check
    if (!is_null($this->desirePercent))
    {
        if ($this->desirePercent > 100.0)
        {
            $this->desirePercent = 100.0;
        }
        if ($this->desirePercent < 0.0)
        {
            $this->desirePercent = 0.0;
        }
        $this->desirePercent = round($this->desirePercent);
    }
    // now onto the update
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE shiftpreference SET desirePercent = ? "
                            . " WHERE workerid = ? AND jobid = ?");
        $stmt->execute(array($this->desirePercent, $this->workerid, $this->jobid));
        $dbh->commit();

        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('ShiftPreference::update()', $pe->getMessage());
        throw $pe;
    }
} // update

public function compare($otherShift)
{
    return ($otherShift->desirePercent - $this->desirePercent);
} // compare

public static function sort(array $shiftList, $asc = FALSE)
{
    $sortFcn = function(ShiftPreference $a, ShiftPreference $b)
    {
        return $a->compare($b);
    };
    $sorted = $shiftList; // copies the array
    uasort($sorted, $sortFcn);
    if ($asc) // default is always desc
    {
        return array_reverse($sorted, TRUE);
    }
    return $sorted;
} // sort

} // ShiftPreference

?>
