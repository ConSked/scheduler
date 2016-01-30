<?php  // $Id: JobSchedule.php 2396 2012-10-18 18:51:23Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('schedule/ScheduleException.php');
require_once('schedule/JobScheduleView.php');
require_once('schedule/WorkerSchedule.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');
require_once('util/date.php');
require_once('util/log.php');


class JobSchedule extends JobScheduleView
{

protected function __construct(Job $job)
{
    parent::__construct($job);
} // constructor

/**
 * @param DateTime $startTime - provided in the case of an currently running expo; otherwise ignore
 * i.e. a currently running expo shouldn't try to schedule 'today'; tomorrow maybe
 */
public static function selectExpo($expoId, $startTime = NULL)
{
    if (is_null($startTime))
    {
        $startTime = new DateTime();
        $startTime->setTime(0, 0, 0); // set time component zero
    }

    $jobs = Job::selectExpo($expoId);
    $jobList = array();

    foreach ($jobs as $job)
    {
        // since we're walking the list, we can filter out old stations
        if ($startTime > $job->startTime)  {  continue;  }
        // key by id
        $jobList[$job->jobid] = new JobSchedule($job);
    } // $job
    $jobs = NULL; // gc
    return $jobList;
} // selectExpo


public function vacate($expoId)
{
    $this->assignedCrew = 0;
    $this->assignedSupervisor = 0;
    $oldWL = $this->workerList;
    $this->workerList = array();
    foreach($oldWL as $w)
    {
        $w->subJob($this->jobid, $expoId, TRUE);
    } // $w
} // vacate


public function addWorker(WorkerSchedule $worker, $expoId, $override = FALSE)
{
    if (array_key_exists($worker->workerid, $this->workerList))  {  return;  } // already added

    $isCrew = $worker->isCrewMember();
    if (!$override)
    {
        if ($isCrew)
        {
            if ($this->maxCrew == $this->assignedCrew)
            {
                $worker->subJob($this, $expoId, TRUE);
                throw new CrewOverException("Job:" . $this->jobid
                    . " is at max crew, cannot add worker:" . $worker->workerid);
            }
        }
        else // is Supervisor
        {
            if ($this->maxSupervisor == $this->assignedSupervisor)
            {
                $worker->subJob($this, $expoId, TRUE);
                throw new SupervisorOverException("Job:" . $this->jobid
                    . " is at max supers, cannot add supervisor:" . $worker->workerid);
            }
            /*
            staff over for future
            else if ($this->maxCrew == $this->assignedCrew)
            {
                $worker->subJob($this, $expoId, TRUE);
                throw new StaffOverException("Job:" . $this->jobid
                    . " is at max crew, cannot add supervisor:" . $worker->workerid);
            }
            */
        } // is super
    } // $override

    $this->workerList[$worker->workerid] = $worker;
    if ($isCrew)
    {
        $this->assignedCrew += 1;
    }
    else
    {
        $this->assignedSupervisor += 1;
    }
    $worker->addJob($this, $expoId, $override); // must be at very end
    return;
} // addWorker

public function subWorker(WorkerSchedule $worker, $expoId, $override = FALSE)
{
    if (array_key_exists($worker->workerid, $this->workerList))
    {
        unset($this->workerList[$worker->workerid]);
        if ($worker->isCrewMember())
        {
            $this->assignedCrew -= 1;
        }
        if ($worker->isSuper())
        {
            $this->assignedSupervisor -= 1;
        }
        $worker->subJob($this, $expoId, $override); // must be at very end
    }
    return;
} // subWorker

public function removeUntil(array $staffList, closure $untilFcn, $expoId)
{
    foreach ($staffList as $staff)
    {
        if (call_user_func($untilFcn, $this))  {  break;  }
        try
        {
            $this->subWorker($staff, $expoId);
        }
        catch (ScheduleException $se)  {  /*silent ignore*/  }
    }
    $staffList = NULL;
    return;
} // removeUntil

private function swap(WorkerSchedule $old, WorkerSchedule $new, $expoId)
{
    if (array_key_exists($new->workerid, $this->workerList))
    {
        return FALSE; // can't swap with peer
    }
    // remove $existing, add $potential
    try
    {
        $this->subWorker($old, $expoId, TRUE); // TRUE to ignore mins - it IS a swap
        $this->addWorker($new, $expoId); // note do NOT force (might be 2nd conflict)
        return TRUE;
    }
    catch (ScheduleImmutableException $se)
    {
        return FALSE; // no can do
    }
    catch (ScheduleException $se)
    {
        $this->addWorker($old, $expoId, TRUE); // else FORCE return to original
    }
    return FALSE;
} // swap

public function swapHappier(WorkerSchedule $existing, WorkerSchedule $potential, $expoId)
{
    // checks for same ROLE to caller of this method
    if ($potential->jobHappiness($this->jobid) >
        $existing->jobHappiness($this->jobid))
    {
        return $this->swap($existing, $potential, $expoId);
    }
    return FALSE;
} // swapHappier

public function swapTime(WorkerSchedule $existing, WorkerSchedule $potential, $expoId)
{
    // checks for same ROLE to caller of this method
    if ($potential->overMaxHours() && ($existing->timeLeft() < $potential->timeLeft()))
    {
        $this->swap($existing, $potential, $expoId);
    }
    return;
} // swapTime

public function filter($authrole)
{
    return WorkerSchedule::filter($this->workerList, $authrole);
} // filter


// convenience methods to return over/under values
public function overCrew()  {  return ($this->assignedCrew > $this->maxCrew) ? ($this->assignedCrew - $this->maxCrew) : 0;  }
public function underCrew()  {  return ($this->assignedCrew < $this->minCrew) ? ($this->minCrew - $this->assignedCrew) : 0;  }
public function overSupervisor()  {  return ($this->assignedSupervisor > $this->maxSupervisor) ? ($this->assignedSupervisor - $this->maxSupervisor) : 0;  }
public function underSupervisor()  {  return ($this->assignedSupervisor < $this->minSupervisor) ? ($this->minSupervisor - $this->assignedSupervisor) : 0;  }
public function isComplete()  {  return (!overCrew() && !underCrew() && !overSupervisor() && !underSupervisor());  }
public function canAddCrew() { return ($this->assignedCrew < $this->maxCrew) ? True : False; }
public function canAddSupervisor() { return ($this->assignedSupervisor < $this->maxSupervisor) ? True : False; }

public function isTimeConflict(JobSchedule $otherJob)
{
    return (datetimeBetween($this->startTime, $this->stopTime, $otherJob->startTime) ||
            datetimeBetween($this->startTime, $this->stopTime, $otherJob->stopTime));
}

public function isStartTimeConflict(JobSchedule $otherJob)
{
    return (datetimeBetween($this->startTime, $this->startTime, $otherJob->startTime));
}

// sorting, etc
private static function sort(array $workerList, $asc, $function)
{
    $sorted = $workerList; // copies the array
    uasort($sorted, $function);
    if ($asc) // default is always desc
    {
        return array_reverse($sorted, TRUE);
    }
    return $sorted;
} // sort

public static function sortHappiness(array $jobList, $asc = FALSE)
{
    $sortFcn = function(JobSchedule $a, JobSchedule $b)
    {
        return ($b->jobHappiness() - $a->jobHappiness());
    };
    return self::sort($jobList, $asc, $sortFcn);
} // sortHappiness

public static function sortCrew(array $jobList, $asc = FALSE)
{
    $sortFcn = function(JobSchedule $a, JobSchedule $b)
    {
        return ($b->assignedCrew() - $a->assignedCrew());
    };
    return self::sort($jobList, $asc, $sortFcn);
} // sortCrew

public static function sortStaff(array $jobList, $asc = FALSE)
{
    $sortFcn = function(JobSchedule $a, JobSchedule $b)
    {
        return ($b->assignedStaff() - $a->assignedStaff());
    };
    return self::sort($jobList, $asc, $sortFcn);
} // sortStaff

public static function sortSupervisor(array $jobList, $asc = FALSE)
{
    $sortFcn = function(JobSchedule $a, JobSchedule $b)
    {
        return ($b->assignedSupervisor() - $a->assignedSupervisor());
    };
    return self::sort($jobList, $asc, $sortFcn);
} // sortSupervisor

/**
 * @see AbstractScheduler#sessionValue()
 */
public function sessionValue()
{
    $sessionValue = array();
    foreach ($this->workerList as $worker)
    {
        $sessionValue[] = $worker->workerid;
    } // $worker
    return $sessionValue;
} // sessionValue


} // JobSchedule

?>
