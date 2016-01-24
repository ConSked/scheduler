<?php  // $Id: AbstractScheduler.php 2292 2012-09-28 18:21:23Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('util/log.php');
require_once('db/ShiftAssignment.php');
require_once('schedule/ScheduleException.php');
require_once('schedule/JobSchedule.php');
require_once('schedule/WorkerSchedule.php');

abstract class AbstractScheduler
{

// DO NOT change these permissions or provide public access to these properties
// if your algorithm requires better access, write a better assignSchedule
protected $expoid;
protected $jobList = NULL;
protected $workerList = NULL;
protected $assignmentList = NULL;

public function __construct($expoId, array $jobList = NULL, array $workerList = NULL, array $assignmentList = NULL, $keepAssignmentsFlag = TRUE)
{
    if (is_null($expoId))
    {
        throw new Exception("must pass in an expoId to AbstractScheduler");
    }
    $this->expoid = $expoId;
    if (is_null($jobList))
    {
        $jobList = JobSchedule::selectExpo($expoId);
    }
    $this->jobList = $jobList;
    $jobList = NULL;
    if (is_null($workerList))
    {
        $workerList = WorkerSchedule::selectExpo($expoId);
    }
    $this->workerList = $workerList;
    $workerList = NULL;
    if (is_null($assignmentList))
    {
        $assignmentList = ShiftAssignment::selectExpo($expoId);
    }
    $this->assignmentList = $assignmentList;
    $assignmentList = NULL;
    $this->setup($keepAssignmentsFlag, $expoId); // todo - pass in parameter
} // constructor

public abstract function assignSchedule($expoId); // override this to execute your algorithm

public function logJobListState($message, $note)
{
    logMessage($message, $note);
    foreach ($this->jobList as $job)
    {
        $job->logState($message);
    }
} // logJobListState

public function logWorkerListState($message, $note)
{
    logMessage($message, $note);
    foreach ($this->workerList as $worker)
    {
        $worker->logState($message);
    }
} // logWorkerListState

/**
 * Note this means that the $this->assignmentList could be NULL, or some subset of the real shiftassignment table's entries
 * Presumably the schedule select existing assignments, jobs, and workers, then calls this method before starting to schedule.
 */
private function setup($keepAssignmentsFlag, $expoId)
{
    // assigned* is a result, not an input
    foreach ($this->jobList as $job)
    {
        $job->vacate($expoId);
    } // $job

    if (is_null($this->assignmentList))  {  return;  }
    foreach ($this->assignmentList as $assignment)
    {
        $sid = $assignment->jobid;
        $wid = $assignment->workerid;
        if (array_key_exists($sid, $this->jobList) &&
            array_key_exists($wid, $this->workerList))
        {
            // the workers have immutable schedules; not the jobs have immutable workers
            if ($keepAssignmentsFlag)
            {
                $this->workerList[$wid]->addJobImmutable($this->jobList[$sid], $expoId);
            }
            else
            {
                $this->workerList[$wid]->addJob($this->jobList[$sid], $expoId, TRUE);
            }
        }
    } // $assignment
} // setup

public function getSchedule()
{
    return WorkerSchedule::createShiftAssignmentList($this->expoid, $this->workerList);
} // getSchedule

public static function commitSchedule($expoId, $keepAssignmentsFlag, array $assignmentList)
{
    // should probably combine these as single DB transaction
    if (!$keepAssignmentsFlag)
    {
        $oldAssignmentList = ShiftAssignment::selectExpo($expoId);
        // actively get rid of old list
        ShiftAssignment::deleteList($oldAssignmentList);
    }
    // create new list
    ShiftAssignment::insertList($assignmentList);
    return;
} // commitSchedule

} // class AbstractScheduler

?>
