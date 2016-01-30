<?php // $Id: ShiftStatus.php 642 2012-06-10 19:26:11Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/log.php');

define("SHIFTSTATUS_SELECT_PREFIX", "SELECT DISTINCT shiftstatusid, workerid, stationid, expoid, statusType, statusTime FROM ");
define("SHIFTSTATUS_SELECT_ID_ASC",  SHIFTSTATUS_SELECT_PREFIX . " shiftstatus WHERE shiftstatusid = ? ORDER BY statusTime ASC");
define("SHIFTSTATUS_SELECT_ID_DESC", SHIFTSTATUS_SELECT_PREFIX . " shiftstatus WHERE shiftstatusid = ? ORDER BY statusTime DESC");
define("SHIFTSTATUS_SELECT_STATUS_ASC",  SHIFTSTATUS_SELECT_PREFIX . " shiftstatus WHERE workerid = ? AND stationid = ? AND expoid = ? ORDER BY statusTime ASC");
define("SHIFTSTATUS_SELECT_STATUS_DESC", SHIFTSTATUS_SELECT_PREFIX . " shiftstatus WHERE workerid = ? AND stationid = ? AND expoid = ? ORDER BY statusTime DESC");
define("SHIFTSTATUS_WORKER_SELECT_STATUS_ASC",  SHIFTSTATUS_SELECT_PREFIX . " shiftstatus WHERE workerid = ? AND expoid = ? ORDER BY statusTime ASC");
define("SHIFTSTATUS_WORKER_SELECT_STATUS_DESC", SHIFTSTATUS_SELECT_PREFIX . " shiftstatus WHERE workerid = ? AND expoid = ? ORDER BY statusTime DESC");

class ShiftStatus
{

public $shiftstatusid;
public $workerid;
public $stationid;
public $expoid;
public $statusType;
public $statusTime;

private function fixDates()
{
	if (is_string($this->statusTime))
	{
		$this->statusTime = swwat_parse_datetime($this->statusTime);
	}
} // fixDates

public static function selectID($shiftstatusid)
{
	try
	{
		$rows = simpleSelect("ShiftStatus", SHIFTSTATUS_SELECT_ID_ASC, array($shiftstatusid));
		if (count($rows) != 1)
		{
			return NULL;
		}
		$rows[0]->fixDates();
		return $rows[0];
	}
	catch (PDOException $pe)
	{
		logMessage('ShiftStatus::selectID(' . $shiftstatusid . ')', $pe->getMessage());
	}
} // selectID

public static function selectStatus($workerid, $stationid, $expoid)
{
	try
	{
		$rows = simpleSelect("ShiftStatus", SHIFTSTATUS_SELECT_STATUS_ASC, array($workerid, $stationid, $expoid));
		for ($k = 0; $k < count($rows); $k++)
		{
			$rows[$k]->fixDates();
		}
		return $rows;
	}
	catch (PDOException $pe)
	{
		logMessage('ShiftStatus::selectStatus(' . $workerid . ', ' . $stationid . ', ' . $expoid . ')', $pe->getMessage());
	}
}

public static function mostRecentStatus($workerid, $stationid, $expoid)
{
	try
	{
		$rows = simpleSelect("ShiftStatus", SHIFTSTATUS_SELECT_STATUS_DESC, array($workerid, $stationid, $expoid));
		if (count($rows) == 0)
		{
			return NULL;
		}
		return $rows[0];
	}
	catch (PDOException $pe)
	{
		logMessage('ShiftStatus::mostRecentStatus(' . $workerid . ', ' . $stationid . ', ' . $expoid . ')', $pe->getMessage());
	}
} // currentStatus

public static function mostRecentStatusWorker($workerid, $expoid)
{
	try
	{
		$rows = simpleSelect("ShiftStatus", SHIFTSTATUS_WORKER_SELECT_STATUS_DESC, array($workerid, $expoid));
		if (count($rows) == 0)
		{
			return NULL;
		}
		return $rows[0];
	}
	catch (PDOException $pe)
	{
		logMessage('ShiftStatus::mostRecentStatusWorker(' . $workerid . ', ' . $expoid . ')', $pe->getMessage());
	}
} // currentStatus

public static function WorkerHours($workerid, $expoid)
{
	try
	{
		$rows = simpleSelect("ShiftStatus", SHIFTSTATUS_WORKER_SELECT_STATUS_ASC, array($workerid, $expoid));
		if (!count($rows) == 0)
		{
			$hours = 0;
			for ($k = 0; $k < count($rows); $k++)
			{
				if ($rows[$k]->statusType == 'CHECK_OUT')
				{
					if (isset($rows[$k-1]) && $rows[$k-1]->statusType == 'CHECK_IN')
					{
						$start = swwat_parse_datetime($rows[$k-1]->statusTime);
						$stop = swwat_parse_datetime($rows[$k]->statusTime);
						$diff = $start->diff($stop);

						$hours += ($diff->d)*24 + $diff->h + ($diff->i)/60 + ($diff->s)/360;
					}
				}
			}
			return $hours;
		}
		else
		{
			return 0;
		}
	}
	catch (PDOException $pe)
	{
		logMessage('ShiftStatus::WorkerHours(' . $workerid . ', ' . $expoid . ')', $pe->getMessage());
	}
} // WorkerHours

public static function WorkerStationHours($workerid, $stationid, $expoid)
{
	try
	{
		$rows = simpleSelect("ShiftStatus", SHIFTSTATUS_SELECT_STATUS_ASC, array($workerid, $stationid, $expoid));
		if (!count($rows) == 0)
		{
			$hours = 0;
			for ($k = 0; $k < count($rows); $k++)
			{
				if ($rows[$k]->statusType == 'CHECK_OUT')
				{
					if (isset($rows[$k-1]) && $rows[$k-1]->statusType == 'CHECK_IN')
					{
						$start = swwat_parse_datetime($rows[$k-1]->statusTime);
						$stop = swwat_parse_datetime($rows[$k]->statusTime);
						$diff = $start->diff($stop);

						$hours += ($diff->d)*24 + $diff->h + ($diff->i)/60 + ($diff->s)/360;
					}
				}
			}
			return $hours;
		}
		else
		{
			return 0;
		}
	}
	catch (PDOException $pe)
	{
		logMessage('ShiftStatus::WorkerStationHours(' . $workerid . ', ' . $stationid . ', ' . $expoid . ')', $pe->getMessage());
	}
} // WorkerStationHours

public function insert()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("INSERT INTO shiftstatus (workerid, stationid, expoid, statusType, statusTIme) VALUES (?, ?, ?, ?, ?)");
		if (is_null($this->statusTime))
		{
			$this->statusTime = new DateTime();
		}
		$stmt->execute(array($this->workerid, $this->stationid, $this->expoid, $this->statusType, swwat_format_isodatetime($this->statusTime)));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('ShiftStatus::insert()', $pe->getMessage());
	}
} // insert

public function update()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("UPDATE shiftstatus SET workerid = ?, stationid = ?, expoid = ?, statusType = ?, statusTIme = ? WHERE shiftstatusid = ?");
		if (is_null($this->statusTime))
		{
			$this->statusTime = new DateTime();
		}
		$stmt->execute(array($this->workerid, $this->stationid, $this->expoid, $this->statusType,
		                     swwat_format_isodatetime($this->statusTime), $this->shiftstatusid));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('ShiftStatus::update()', $pe->getMessage());
	}
} // update

public function delete()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("DELETE FROM shiftstatus WHERE shiftstatusid = ?");
		$stmt->execute(array($this->shiftstatusid));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('ShiftStatus::delete()', $pe->getMessage());
	}
} // delete

} // ShiftStatus

?>
