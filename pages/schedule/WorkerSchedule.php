<?php  // $Id: WorkerSchedule.php 2396 2012-10-18 18:51:23Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/ShiftPreference.php');
require_once('schedule/JobSchedule.php');
require_once('schedule/ScheduleException.php');
require_once('schedule/WorkerScheduleView.php');
require_once('util/RoleEnum.php');
require_once('util/log.php');


class WorkerSchedule extends WorkerScheduleView
{

private $jobMinutes = 0;
public function jobHours()  {  return ($this->jobMinutes / 60.0);  }
public function overMaxHours()  {  return $this->jobMinutes > $this->maxMinutes;  }
public function timeLeft()  {  return $this->maxMinutes - $this->jobMinutes;  }

public function addJob(JobSchedule $job, $expoId, $override = FALSE)
{
    if (array_key_exists($job->jobid, $this->jobList))  {  return;  } // already added

    if (!$override)
    {
		$expo = Expo::selectID($expoId);

        $preference = $this->jobPreferences[$job->jobid];
        if (is_null($preference->desirePercent))
        {
            $job->subWorker($this, $expoId, TRUE); // we may have added it
            throw new ScheduleImpossibleException("Worker:" . $this->workerid
                . " cannot work in Job:" . $job->jobid);
        }
        $newJobMinutes = $this->jobMinutes + $job->jobMinutes();
        if ($newJobMinutes > $this->maxMinutes)
        {
            $job->subWorker($this, $expoId, TRUE); // we may have added it
            throw new ScheduleOverMaxHoursException("Worker:" . $this->workerid
                . " cannot work in Job:" . $job->jobid
                . " as will have total minutes above max:" . ($newJobMinutes - $this->maxMinutes));
        }
        if ($newJobMinutes > 60*($expo->expoHourCeiling))
        {
            $job->subWorker($this, $expoId, TRUE); // we may have added it
            throw new ScheduleOverMaxHoursException("Worker:" . $this->workerid
                . " cannot work in Job:" . $job->jobid
                . " as will have total minutes above expo max:" . ($newJobMinutes - 60*($expo->expoHourCeiling)));
        }
        foreach ($this->jobList as $existing)
        {
			if ($job->isTimeConflict($existing))
			{
				if ($expo->allowScheduleTimeConflict)
				{
					logMessage("WorkerSchedule", "overlapping conflict allowed");
					if ($job->isStartTimeConflict($existing))
					{
						$job->subWorker($this, $expoId, TRUE); // we may have added it
						$sce = new ScheduleConflictException("Worker:" . $this->workerid
						     . " cannot work in Job:" . $job->jobid
						     . " due to identical start time conflict with existing Job:" . $existing->jobid);
						$sce->conflict = $existing;
						logMessage("WorkerSchedule", $sce);
						throw $sce;
					}
				}
				else
				{
					$job->subWorker($this, $expoId, TRUE); // we may have added it
					$sce = new ScheduleConflictException("Worker:" . $this->workerid
					     . " cannot work in Job:" . $job->jobid
					     . " due to conflict with existing Job:" . $existing->jobid);
					$sce->conflict = $existing;
					logMessage("WorkerSchedule", $sce);
					throw $sce;
				} // allowScheduleTimeConflict
			}
		} // $existing
	} // $override
    $this->jobList[$job->jobid] = $job;
    $this->jobMinutes += $job->jobMinutes();
    $job->addWorker($this, $expoId, $override); // must be at very end
    return;
} // addJob

// the workers have immutable schedules; not the jobs have immutable workers
private $jobListImmutable = array();

public function addJobImmutable(JobSchedule $job, $expoId)
{
    if (array_key_exists($job->jobid, $this->jobListImmutable))  {  return;  } // already added
    $this->jobListImmutable[$job->jobid] = $job;
    $this->addJob($job, $expoId, TRUE);
    return;
} // addJobImmutable

public function subJob(JobSchedule $job, $expoId, $override = FALSE)
{
    if (array_key_exists($job->jobid, $this->jobListImmutable))
    {
        $this->addJob($job, $expoId, TRUE);
        throw new ScheduleImmutableException("Worker:$this->workerid cannot unschedule Job:$job->jobid");
    }
    if (array_key_exists($job->jobid, $this->jobList))
    {
        unset($this->jobList[$job->jobid]);
        $this->jobMinutes -= $job->jobMinutes();
        $job->subWorker($this, $expoId, $override); // must be at very end
    }
    return;
} // subJob

/**
 * This method removes jobs until is under max, but ignores exceptions;
 * therefore, at the end, it is possible it is still overMax
 * @see JobSchedule::removeOverMax
 */
public function removeOverMax()
{
    if (!$this->overMaxHours())  {  return;  }

    // loop jobs in reverse priority order
    $jobList = $this->sortJobPreference($this->jobList, TRUE);
    foreach ($jobList as $job)
    {
        if (!$this->overMaxHours())  {  return;  }
        try
        {
            $this->subJob($job, $expoId);
        }
        catch (ScheduleImmutableException $ex)
        {
            // can't force this
            // ignore and continue
        }
        catch (ScheduleException $ex)
        {
            // ignore and continue
        }
    } // $job
    return;
} // removeOverMax

public function swapHappier(JobSchedule $existing, JobSchedule $potential, $expoId)
{
    if (array_key_exists($potential->jobid, $this->jobList))
    {
        return FALSE; // can't swap with peer
    }
    if ($this->jobHappiness($existing->jobid) <
        $this->jobHappiness($potential->jobid))
    {
        // remove $existing, add $potential
        try
        {
            $this->subJob($existing, $expoId, TRUE); // TRUE to ignore mins
            $this->addJob($potential, $expoId); // note do NOT force (might be 2nd conflict)
            return TRUE;
        }
        catch (ScheduleImmutableException $se)
        {
            return FALSE; // no can do
        }
        catch (ScheduleException $se)
        {
            $this->addJob($existing, $expoId, TRUE); // else FORCE return to original
        }
    }
    return FALSE;
} // swapHappier

public static function filter(array $workerList, $authrole)
{
    $filterList = array();
    foreach ($workerList as $worker)
    {
        if ($worker->isRole($authrole))
        {
            $filterList[$worker->workerid] = $worker;
        }
    } // $worker
    return $filterList;
} // filter

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

public static function sortTimeLeft(array $workerList, $asc = FALSE)
{
    $sortFcn = function(WorkerSchedule $a, WorkerSchedule $b)
    {
        return ($b->timeLeft() - $a->timeLeft());
    };
    return self::sort($workerList, $asc, $sortFcn);
} // sortTimeLeft

public static function sortExpoHappiness(array $workerList, $asc = FALSE)
{
    $sortFcn = function(WorkerSchedule $a, WorkerSchedule $b)
    {
        return ($b->expoHappiness() - $a->expoHappiness());
    };
    return self::sort($workerList, $asc, $sortFcn);
} // sortExpoHappiness

public static function sortJobHappiness(JobSchedule $s, array $workerList, $asc = FALSE)
{
    $sortFcn = function($sid)
    {
        return function (WorkerSchedule $a, WorkerSchedule $b) use ($sid)
        {
            return ($b->jobHappiness($sid) - $a->jobHappiness($sid));
        };
    };
    return self::sort($workerList, $asc, $sortFcn($s->jobid));
} // sortJobHappiness

// cannot - because this class modifies this array getJobPreferences()  {  return $this->jobPreferences;  }
// this method below returns back an invariant (#jobs will not change)
public function sortJobPreference(array $jobList, $asc = FALSE)
{
    $sortedJobList = array();
    foreach ($this->jobPreferences as $preference)
    {
        if (array_key_exists($preference->jobid, $jobList))
        {
            $sortedJobList[$preference->jobid] = $jobList[$preference->jobid];
        }
    } // $job
    if ($asc) // default is always desc
    {
        return array_reverse($sortedJobList, TRUE);
    }
    return $sortedJobList;
} // sortJobPreference

} // WorkerSchedule

?>
