<?php  // $Id: JobScheduleView.php 2277 2012-09-26 17:09:24Z preston $ Copyright (c) SwiftJob, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Job.php');
require_once('schedule/WorkerSchedule.php');
require_once('schedule/JobSchedule.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');
require_once('util/date.php');
require_once('util/log.php');


class JobScheduleView
{
public $jobid;
public $stationid;
protected $maxCrew;
protected $minCrew;
protected $assignedCrew;
protected $maxSupervisor;
protected $minSupervisor;
protected $assignedSupervisor;
protected $startTime;
protected $stopTime;
public $location;

protected function __construct(Job $job)
{
    $this->jobid = $job->jobid;
    $this->stationid = $job->stationid;
    $this->maxCrew = $job->maxCrew;
    $this->minCrew = $job->minCrew;
    $this->assignedCrew = $job->assignedCrew;
    $this->maxSupervisor = $job->maxSupervisor;
    $this->minSupervisor = $job->minSupervisor;
    $this->assignedSupervisor = $job->assignedSupervisor;
    $this->startTime = $job->startTime;
    $this->stopTime = $job->stopTime;
    $this->location = $job->location;
} // constructor

public function assignedCrew()  {  return $this->assignedCrew;  }
public function assignedSupervisor()  {  return $this->assignedSupervisor;  }
public function assignedStaff()  {  return $this->assignedCrew() + $this->assignedSupervisor();  }

public function jobMinutes()  {  return minuteDiff($this->stopTime, $this->startTime);  }

protected $workerList = array();
public function sessionValue()
{
    // $_SESSION[PARAM_SCHEDULE_PUBLISH] = $aas; - this will not work due to the recursion
    // i.e. jobs->workers->jobs->.....
    $sessionValue = array();
    foreach ($this->workerList as $worker)
    {
        $sessionValue[] = $worker->workerid;
    } // $worker
    return $sessionValue;
} // sessionValue

/**
 * job happiness is the happiness of the workers considering just this job
 * expoHappiness is the total happiness of the workers at this job
 */
public function expoHappiness()
{
    if (0 == count($this->workerList))  {  return 0.0;  }
    $happiness = 0.0;
    foreach ($this->workerList as $worker)
    {
        $happiness += $worker->expoHappiness();
    } // $worker
    return $happiness / count($this->workerList);
} // expoHappiness

/**
 * job happiness is the happiness of the workers considering just this job
 * expoHappiness is the total happiness of the workers at this job
 */
public function jobHappiness()
{
    if (0 == count($this->workerList))  {  return 0.0;  }
    $happiness = 0.0;
    foreach ($this->workerList as $worker)
    {
        $happiness += $worker->jobHappiness($this->jobid);
    } // $worker
    return $happiness / count($this->workerList);
} // jobHappiness

public function logState($message, $indent = "")
{
    $content = $indent . "Job:" . $this->jobid;

    // supers
    $content .= "\tSupers:";
    if ($this->overSupervisor())  {  $content .= " + ";  }
    else if ($this->underSupervisor())  {  $content .= " - ";  }
    else {  $content .= sprintf("%3d", $this->assignedSupervisor());  }

    // crew
    $content .= "\tCrew:";
    if ($this->overCrew())  {  $content .= " + ";  }
    else if ($this->underCrew())  {  $content .= " - ";  }
    else {  $content .= sprintf("%3d", $this->assignedCrew());  }

    $content .= "\tHappiness:" . sprintf("%3.2f", $this->jobHappiness());
    $content .= "\tCrew's:" . sprintf("%3.2f", $this->expoHappiness());

    logMessage($message, $content);
    if (0 == strcmp("", $indent))
    {
        foreach ($this->workerList as $worker)
        {
            $worker->logState($message, "\t\t");
        }
    }
    return;
} // logState

} // JobScheduleView

?>
