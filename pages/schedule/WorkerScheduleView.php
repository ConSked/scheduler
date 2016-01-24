<?php  // $Id: WorkerScheduleView.php 2234 2012-09-24 14:58:45Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/Expo.php');
require_once('db/ShiftPreference.php');
require_once('db/ShiftAssignment.php');
require_once('schedule/ScheduleException.php');
require_once('schedule/JobSchedule.php');
require_once('schedule/WorkerSchedule.php');
require_once('util/RoleEnum.php');
require_once('util/date.php');
require_once('util/log.php');


define("WORKERSCHEDULEVIEW_SELECT_CLAUSE",  "SELECT DISTINCT workerid, maxMinutes, authrole FROM workerscheduleview WHERE ");
define("WORKERSCHEDULEVIEW_SELECT_EXPO",    WORKERSCHEDULEVIEW_SELECT_CLAUSE . " authrole != 'ORGANIZER' AND expoid = ? ");
define("WORKERSCHEDULEVIEW_SELECT_STATION", WORKERSCHEDULEVIEW_SELECT_EXPO . " AND jobid = ?");
//Needs to test maxMinutes because of possible duplicate entries in the view
define("WORKERSCHEDULEVIEW_SELECT_WORKER",  WORKERSCHEDULEVIEW_SELECT_EXPO . " AND workerid = ? and maxMinutes > 0");
// organizers must be specifically assigned

class WorkerScheduleView
{

// from workerexpo table
public $workerid;
// from grosspreference.maxHours table
protected $maxMinutes; // use minutes as DateTime makes it easier to add fractional hours then
// from worker role table
protected $authrole;

public function authrole()  {  return $this->authrole;  }
public function isRole($authrole)  {  return (0 == strcmp($this->authrole, $authrole));  }
public function isCrewMember()  {  return $this->isRole(CREWMEMBER);  }
public function isOrganizer()   {  return $this->isRole(ORGANIZER);  }
public function isSupervisor()  {  return $this->isRole(SUPERVISOR);  }
public function isStaff()  {  return ($this->isCrewMember() || $this->isSupervisor());  }
public function isSuper()  {  return ($this->isSupervisor() || $this->isOrganizer());  }

protected $jobPreferences = array();
public function getPreference($jobId)
{
    return $this->jobPreferences[$jobId];
} // getPreference

protected static $compareJobId;
private static function WorkerScheduleViewJobCompare($a, $b)
{
    // note desc order
    return ($a->jobHappiness(self::$compareJobId) -
            $b->jobHappiness(self::$compareJobId));
} // WorkerScheduleViewJobCompare

public static function sortWorkerPreferences($jobId, $workers)
{
    self::$compareJobId = $jobId;
    $workerPrefs = array();
    foreach ($workers as $w)
    {
        $workerPrefs[$w->workerid] = $w;
    } // $
    uasort($workerPrefs, "WorkerScheduleView::WorkerScheduleViewJobCompare"); // usort destroys the key indices; uasort maintains them; aiighhh!!
    return $workerPrefs;
} // sortWorkerPreferences

protected $jobList = array();

public static function createShiftAssignmentList($expoId, array $workerList)
{
    $shiftAssignmentList = array();
    foreach ($workerList as $worker)
    {
        foreach ($worker->jobList as $job)
        {
            $shiftToAssign = new ShiftAssignment();
            $shiftToAssign->jobid = $job->jobid;
            $shiftToAssign->stationid = $job->stationid;
            $shiftToAssign->expoid = $expoId;
            $shiftToAssign->workerid = $worker->workerid;
            $shiftAssignmentList[] = $shiftToAssign;
        } // $job
    } // $worker
    return $shiftAssignmentList;
} // createShiftAssignmentList

/**
 * job happiness is the happiness of the workers considering just this job
 * expoHappiness is the total happiness of the workers at this job
 */
public function expoHappiness()
{
    if (0 == count($this->jobList))  {  return 0.0;  }
    $happiness = 0.0;
    foreach ($this->jobList as $job)
    {
        $happiness += $this->jobHappiness($job->jobid);
    } // $preference
    return $happiness / count($this->jobList);
} // expoHappiness

/**
 * job happiness is the happiness of the workers considering just this job
 * expoHappiness is the total happiness of the workers at this job
 */
public function jobHappiness($jobId)
{
    $pref = $this->getPreference($jobId);
    return is_null($pref) ? -1 : $pref->desirePercent;
} // jobHappiness

private static function select($sqlWorker, $sqlPrefs, $params)
{
    $workers = NULL;
    $prefList = NULL;
    try
    {
        $workers  = simpleSelect("WorkerSchedule", $sqlWorker, $params);
        $prefList = simpleSelect("ShiftPreference", $sqlPrefs, $params);
    }
    catch (PDOException $pe)
    {
        logMessage('WorkerScheduleView::select(' . var_export($params, TRUE) . ')', $pe->getMessage());
    }

    $workerList = array();
    foreach ($workers as $worker)
    {
        $workerList[$worker->workerid] = $worker;
    } // $worker
    $workers = NULL; // gc

    foreach ($prefList as $preference)
    {
        if (array_key_exists($preference->workerid, $workerList))
        {
            $worker = $workerList[$preference->workerid];
            $worker->jobPreferences[$preference->jobid] = $preference;
        }
    } // $preference
    foreach ($workerList as $worker)
    {
        $worker->jobPreferences = ShiftPreference::sort($worker->jobPreferences, FALSE);
    } // $worker
    $prefList = NULL; // gc
    return $workerList;
} // select

public static function selectExpo($expoId)
{
    $sqlPrefs = "SELECT * FROM shiftpreference WHERE expoid = ?";
    return self::select(WORKERSCHEDULEVIEW_SELECT_EXPO, $sqlPrefs, array($expoId));
} // selectExpo

public static function selectWorker($expoId, $workerId)
{
    $sqlPrefs = "SELECT * FROM shiftpreference WHERE expoid = ? AND workerid = ?";
    $workerList = self::select(WORKERSCHEDULEVIEW_SELECT_WORKER, $sqlPrefs, array($expoId, $workerId));
    if (1 == count($workerList))
    {
        return $workerList[$workerId];
    }
    return NULL; // should throw exception
} // selectWorker

public function logState($message, $indent = "")
{
    $content = $indent . "Worker:" . $this->workerid;

    // hours
    $content .= "\tHours:";
    if ($this->overMaxHours())  {  $content .= " + ";  }
    else {  $content .= sprintf("%3d", $this->jobHours());  }

    $content .= "\tHappiness:" . sprintf("%3.2f", $this->expoHappiness());

    logMessage($message, $content);
    if (0 == strcmp("", $indent))
    {
        foreach ($this->jobList as $job)
        {
            $job->logState($message, "\t\t");
        }
    }
    return;
} // logState

} // WorkerScheduleView

?>
