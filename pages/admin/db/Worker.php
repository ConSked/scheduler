<?php  // $Id: Worker.php 2411 2012-10-24 17:13:36Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/Expo.php');
require_once('util/RoleEnum.php');
require_once('util/log.php');


define("WORKER_SELECT_CLAUSE",  "SELECT DISTINCT workerid, email, phone, smsemail, isDisabled, lastLoginTime, firstName, middleName, lastName, externalAuthentication, authrole FROM ");
define("WORKER_SELECT_PREFIX",     WORKER_SELECT_CLAUSE . " workerview WHERE ");
define("WORKER_SELECT_ID",         WORKER_SELECT_PREFIX . " workerid = ?");
define("WORKER_SELECT_EXPO",       WORKER_SELECT_PREFIX . " expoid = ?");
define("WORKER_SELECT_NOTEXPO",    WORKER_SELECT_PREFIX . " isDisabled = FALSE AND " . " workerid NOT IN (SELECT workerid FROM workerexpo WHERE expoid = ?)");
define("WORKER_SELECT_STATION",    WORKER_SELECT_PREFIX . " workerid IN (SELECT workerid FROM shiftassignmentview WHERE stationid = ?)");
define("WORKER_SELECT_NOTSTATION", WORKER_SELECT_PREFIX . " expoid = ? AND isDisabled = FALSE AND "
                                                        . " workerid NOT IN (SELECT workerid FROM shiftassignmentview WHERE stationid = ?)");
define("WORKER_SELECT_EMAIL",      WORKER_SELECT_PREFIX . " lower(email) = lower(?)");

define("WORKER_SELECT_INEXPO", WORKER_SELECT_CLAUSE . " workerview");
define("WORKER_SELECT_ISASSIGNED", "SELECT expoid, workerid FROM workerexpo WHERE expoid = ? AND workerid = ?");


function WorkerCompare($a, $b)  {  return $a->compare($b);  }


class Worker
{

// from worker table
public $workerid;
public $email;
public $phone;
public $smsemail;
public $isDisabled;
public $lastLoginTime;
public $firstName;
public $middleName;
public $lastName;
public $externalAuthentication;

// from the workerexpo tables
public $arrayExpo;

// from worker role table
public $authrole;

public function isRole($authrole)  {  return (0 == strcmp($this->authrole, $authrole));  }
public function isCrewMember()  {  return $this->isRole(CREWMEMBER);  }
public function isOrganizer()   {  return $this->isRole(ORGANIZER);  }
public function isSupervisor()  {  return $this->isRole(SUPERVISOR);  }
public function isStaff()  {  return ($this->isCrewMember() || $this->isSupervisor());  }

public function logIdentity()
{
    $identity = "";
    if ($this->isOrganizer())  {  $identity .= "Organizer:";  } // note succeeding else's
    else if ($this->isSupervisor())  {  $identity .= "Supervisor:";  }
    else if ($this->isStaff())  {  $identity .= "CrewMember:";  }
    return $identity . $workerid . "  " . $lastName . ", " . $firstName . "  " . ($isDisabled ? "disabled" : "enabled");
} // logIdentity

public function nameString()
{
    return $this->lastName . ", " . $this->firstName;
} // nameString

public function nameString2()
{
    return $this->firstName . " " . $this->lastName;
} // nameString

public function roleString()
{
    return RoleEnum::getString($this->authrole);
} // roleString

public function assignString()
{
    return $this->nameString() . " - " . $this->roleString();
} // assignString

public function compare($otherWorker)
{
    if ($this->workerid == $otherWorker->workerid)
    {
        return 0;
    }
    if ($this->isDisabled != $otherWorker->isDisabled)
    {
        return ($this->isDisabled) ? 1 : -1; // greater than, therefore disabled on bottom
    }
    if ($this->isSupervisor() != $otherWorker->isSupervisor())
    {
        return ($this->isSupervisor()) ? -1 : 1; // lesser than, therefore super on top
    }
    if ($this->isCrewMember() != $otherWorker->isCrewMember())
    {
        return ($this->isCrewMember()) ? -1 : 1; // lesser than, therefore crew on top
    }
    if ($this->isOrganizer() != $otherWorker->isOrganizer())
    {
        return ($this->isOrganizer()) ? -1 : 1; // lesser than, therefore organizer on top
    }

    $c = strcasecmp($this->lastName, $otherWorker->lastName);
    if (0 != $c)  {  return $c;  }

    $c = strcasecmp($this->firstName, $otherWorker->firstName);
    if (0 != $c)  {  return $c;  }

    $c = strcasecmp($this->middleName, $otherWorker->middleName);
    return $c;
} // compare

public static function selectID($workerId)
{
    try
    {
        $rows = simpleSelect("Worker", WORKER_SELECT_ID, array($workerId));
        if (1 != count($rows))
        {
            return NULL;
        }
        return $rows[0];
    }
    catch (PDOException $pe)
    {
        logMessage('Worker::selectID(' . $workerId . ')', $pe->getMessage());
    }
} // selectID

public static function selectUsername($workerUsername)
{
    try
    {
        $rows = simpleSelect("Worker", WORKER_SELECT_EMAIL, array($workerUsername));
        if (1 != count($rows))
        {
            return NULL;
        }
        return $rows[0];
    }
    catch (PDOException $pe)
    {
        logMessage('Worker::selectUsername(' . $workerUsername . ')', $pe->getMessage());
    }
} // selectUsername

public static function selectMultiple($arrayExpoId = NULL)
{
    // arrayExpoId - need to 'generate' IN clause
    // disabledFlag - NULL (all), TRUE (only disabled), FALSE(not disabled)
    // authrole - NULL (all), CREWMEMBER, ORGANIZER, SUPERVISOR
    try
    {
        $rows = simpleSelect("Worker", WORKER_SELECT_INEXPO);
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('Worker::selectMultiple()', $pe->getMessage());
    }
} // selectMultiple

public static function selectExpo($expoId)
{
    try
    {
        return simpleSelect("Worker", WORKER_SELECT_EXPO, array($expoId));
    }
    catch (PDOException $pe)
    {
        logMessage('Worker::selectExpo(' . $expoId . ')', $pe->getMessage());
    }
} // selectExpo

public static function selectNotExpo($expoId)
{
    try
    {
        return simpleSelect("Worker", WORKER_SELECT_NOTEXPO, array($expoId));
    }
    catch (PDOException $pe)
    {
        logMessage('Worker::selectNotExpo(' . $expoId . ')', $pe->getMessage());
    }
} // selectNotExpo

public function assignToExpo($expoId)
{
    try
    {
        $dbh = getPDOConnection();
        $stmt = $dbh->prepare("INSERT INTO workerexpo (expoid, workerid) VALUES (?, ?)");
        $stmt->execute(array($expoId, $this->workerid));
    }
    catch (PDOException $pe)
    {
        // there is a very high probability this will fail (likely to already be inserted)
    }
} // assignToExpo

public function removeFromExpo($expoId)
{
    try
    {
        $dbh = getPDOConnection();
        $stmt = $dbh->prepare("DELETE FROM workerexpo WHERE expoid = ? AND workerid = ?");
        $stmt->execute(array($expoId, $this->workerid));
    }
    catch (PDOException $pe)
    {
        // there is a reasonable probability this will fail (likely to not exist)
    }
} // removeFromExpo

public function isAssignedToExpo($expoId)
{
	try
	{
        $rows = simpleSelect("Worker", WORKER_SELECT_ISASSIGNED, array($expoId, $this->workerid));
		if (count($rows) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	catch (PDOException $pe)
	{
		logMessage('Worker::isAssignedToExpo(' . $expoId . ')', $pe->getMessage());
	}
} // isAssignedToExpo 

public static function selectStation($stationId)
{
    try
    {
        return simpleSelect("Worker", WORKER_SELECT_STATION, array($stationId));
    }
    catch (PDOException $pe)
    {
        logMessage('Worker::selectStation(' . $stationId . ')', $pe->getMessage());
    }
} // selectStation

public static function selectNotStation($expoId, $stationId)
{
	try
	{
		return simpleSelect("Worker", WORKER_SELECT_NOTSTATION, array($expoId, $stationId));
	}
	catch (PDOException $pe)
	{
		logMessage('Worker::selectNotStation(' . $expoId . ', ' . $stationId . ')', $pe->getMessage());
	}
} // selectNotStation

public function insert() // throws PDOException
{
    if ((0 == strlen($this->phone)) || (0 == strcmp("0000000000", $this->phone)))
    {
        $this->phone = NULL;
    }
    $dbh = getPDOConnection();
    $dbh->beginTransaction();
    $stmt = $dbh->prepare("INSERT INTO worker (email, phone, smsemail, firstName, middleName, lastName) VALUES (lower(?), ?, lower(?), ?, ?, ?)");
    $stmt->execute(array($this->email, $this->phone, $this->smsemail, $this->firstName, $this->middleName, $this->lastName));
    // now get the workerid
    $this->workerid = $dbh->lastInsertId(); // note before commit
    $dbh->commit();
    return $this;
} // insert

public function update()
{
    if ((0 == strlen($this->phone)) || (0 == strcmp("0000000000", $this->phone)))
    {
        $this->phone = NULL;
    }
    try
    {
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE worker SET email = lower(?), phone = ?, smsemail = lower(?), " .
                              " firstName = ?, middleName = ?, lastName = ?, externalAuthentication = ? WHERE workerid = ?");
        $stmt->execute(array($this->email, $this->phone, $this->smsemail,
                             $this->firstName, $this->middleName, $this->lastName, $this->externalAuthentication, $this->workerid));
        $dbh->commit();
        return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('Worker::update()', $pe->getMessage());
    }
} // update

public function updateRole()
{
	try
	{
        $dbh = getPDOConnection();
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE workerrole SET authrole = ? WHERE workerid = ?");
        $stmt->execute(array($this->authrole, $this->workerid));
        $dbh->commit();
        return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('Worker::updateRole()', $pe->getMessage());
	}
} // updateRole

public function selectExpos()
{
    // go get the expolist; presumably using the Expo object
    $this->arrayExpo = Expo::selectWorker($this->workerid);
} // selectExpos

public function selectMaxHours($expoId)
{
    try
    {
        $dbh = getPDOConnection();
		$dbh->beginTransaction();
        $stmt = $dbh->prepare("SELECT maxHours FROM workerexpo WHERE expoid = ? AND workerid = ?");
        $stmt->execute(array($expoId, $this->workerid));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (1 != count($rows))
        {
            throw new PDOException("Worker::selectMaxHours - improper number of rows");
        }
        return $rows[0]['maxHours'];
    }
    catch (PDOException $pe)
    {
        logMessage('Worker::selectMaxHours(' . $expoId . ')', $pe->getMessage());
    }
    return NULL;
} // selectMaxHours

public function updateMaxHours($expoId, $maxHours)
{
    try
    {
        $dbh = getPDOConnection();
		$dbh->beginTransaction();
        $stmt = $dbh->prepare("UPDATE workerexpo SET maxHours = ? WHERE expoid = ? AND workerid = ? ");
        $stmt->execute(array($maxHours, $expoId, $this->workerid));
		$dbh->commit();
		return $this;
    }
    catch (PDOException $pe)
    {
        logMessage('Worker::updateMaxHours(' . $expoId . ', ' . $maxHours . ')', $pe->getMessage());
    }
    return;
} // updateMaxHours

} // Worker

?>
