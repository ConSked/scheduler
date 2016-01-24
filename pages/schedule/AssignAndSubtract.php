<?php  // $Id: AssignAndSubtract.php 2395 2012-10-17 19:37:37Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('db/ShiftAssignment.php');
require_once('schedule/AbstractScheduler.php');
require_once('schedule/JobSchedule.php');
require_once('schedule/WorkerSchedule.php');
require_once('schedule/ScheduleException.php');
require_once('util/log.php');

class AssignAndSubtract extends AbstractScheduler
{

public function assignSchedule($expoId)
{
    if ((0 == count($this->jobList)) || (0 == count($this->workerList)))
    {
        return;
    }
    $this->assignAll($expoId);
    logMessage("AssignAndSubtract->assignSchedule($expoId)", "assignAll($expoId)");
    $this->removeObvious($expoId);
    logMessage("AssignAndSubtract->assignSchedule($expoId)", "removeObvious($expoId)");
    $this->replaceOverMax($expoId);
    $this->replaceOverMax($expoId); // we call a second time as the lists have changed
    logMessage("AssignAndSubtract->assignSchedule($expoId)", "replaceOverMax($expoId)");
    $this->assignUnderEmployed($expoId);
    logMessage("AssignAndSubtract->assignSchedule($expoId)", "assignUnderEmployed($expoId)");
    // here we would begin to swap unhappy people until happiness doesn't change
    return;
} // assignSchedule

private function removeObvious($expoId)
{
    $untilSuperFcn = function(JobSchedule $job)
    {
        return (0 == $job->overSupervisor());
    };
    $untilCrewFcn = function(JobSchedule $job)
    {
        return (0 == $job->overCrew());
    };

    foreach ($this->jobList as $job)
    {
        $staffList = WorkerSchedule::sortJobHappiness($job, $job->filter(SUPERVISOR), TRUE);
        $job->removeUntil($staffList, $untilSuperFcn, $expoId);
        $staffList = WorkerSchedule::sortJobHappiness($job, $job->filter(CREWMEMBER), TRUE);
        $job->removeUntil($staffList, $untilCrewFcn, $expoId);
    } // $job

    $maxedWorkers = WorkerSchedule::sortTimeLeft($this->workerList, TRUE); // maxed at top
    foreach ($maxedWorkers as $worker)
    {
        $worker->removeOverMax();
    } // $worker

    $untilSuperFcn = function(JobSchedule $job)
    {
        return (0 != $job->underSupervisor());
    };
    $untilCrewFcn = function(JobSchedule $job)
    {
        return (0 != $job->underCrew());
    };
    foreach ($this->jobList as $job)
    {
        $staffList = WorkerSchedule::sortJobHappiness($job, $job->filter(SUPERVISOR), TRUE);
        $job->removeUntil($staffList, $untilSuperFcn, $expoId);
        $staffList = WorkerSchedule::sortJobHappiness($job, $job->filter(CREWMEMBER), TRUE);
        $job->removeUntil($staffList, $untilCrewFcn, $expoId);
    } // $job
} // removeObvious

private function assignAll($expoId)
{
    foreach ($this->jobList as $job)
    {
        foreach ($this->workerList as $worker)
        {
            try
            {
                $job->addWorker($worker, $expoId); // look for impossibles
            }
            catch (ScheduleImpossibleException $se)
            {
                continue; // skip impossibles; left for manual assignment
            }
            catch (ScheduleConflictException $se)
            {
                // will have to fix later anyway
                $conflict = $se->conflict;
                $worker->swapHappier($job, $conflict, $expoId);
            }
            catch (ScheduleException $se)
            {
                $job->addWorker($worker, $expoId, TRUE); // force it
            }
        } // $worker
    } // $job
} // assignAll

private function replaceOverMaxWorkerHappiness($worker, $expoId)
{
    $changed = FALSE;
    // birds of a feather
    $roleList = WorkerSchedule::filter($this->workerList, $worker->authrole());
    unset($roleList[$worker->workerid]); // remove $loopWorker
    $loopWorkerUnhappyJob = $worker->sortJobPreference($this->jobList, TRUE);
    foreach ($loopWorkerUnhappyJob as $loopJob)
    {
        $preferredSwapOrder = WorkerSchedule::sortJobHappiness($loopJob, $roleList, FALSE);
        foreach ($preferredSwapOrder as $swapWorker)
        {
            if ($loopJob->swapHappier($loopWorker, $swapWorker, $expoId))
            {
                $changed = TRUE;
                break; // goto $loopJob loop
            }
        } // $swapWorker
        if (!$worker->overMaxHours())  {  break;  } // onto next worker
    } // $loopJob
    return $changed;
} // replaceOverMaxWorkerHappiness

private function replaceOverMaxWorkerTime($worker, $expoId)
{
    $changed = FALSE;
    // birds of a feather
    $roleList = WorkerSchedule::filter($this->workerList, $worker->authrole());
    unset($roleList[$worker->workerid]); // remove $loopWorker
    $loopWorkerUnhappyJob = $worker->sortJobPreference($this->jobList, TRUE);
    foreach ($loopWorkerUnhappyJob as $loopJob)
    {
        $preferredSwapOrder = WorkerSchedule::sortTimeLeft($loopJob, $roleList, FALSE);
        foreach ($preferredSwapOrder as $swapWorker)
        {
            if ($loopJob->swapTime($loopWorker, $swapWorker, $expoId))
            {
                $changed = TRUE;
                break; // goto $loopJob loop
            }
        } // $swapWorker
        if (!$worker->overMaxHours())  {  break;  } // onto next worker
    } // $loopJob
    return $changed;
} // replaceOverMaxWorkerTime

private function replaceOverMax($expoId)
{
    foreach ($this->workerList as $loopWorker) // loop over ALL workers
    {
        if (!$loopWorker->overMaxHours())  {  continue;  }

        // if we didn't change, then move to next worker
        // even if still overMaxHours
        // if we changed, it is possible to change again
        $changed = FALSE;
        while (!changed && $worker->overMaxHours())
        {
            $changed = $this->replaceOverMaxWorkerHappiness($loopWorker, $expoId);
        }
        $changed = FALSE;
        while (!changed && $worker->overMaxHours())
        {
            $changed = $this->replaceOverMaxWorkerTime($loopWorker, $expoId);
        }
    } // $loopWorker
    return;
} // replaceOverMax


private function assignUnderEmployed($expoId)
{
    foreach ($this->workerList as $underWorker)
    {
        if ($underWorker->overMaxHours())  {  continue;  }
        // get job preference list
        $underJobList = $underWorker->sortJobPreference($this->jobList, FALSE);

        foreach ($underJobList as $job)
        {
            try
            {
                $underWorker->addJob($job, $expoId);
            }
            catch (ScheduleException $se)  {  /* ignore; try again */  }
            if ($underWorker->overMaxHours())  {  break;  }
        } // $job
    } // $underWorker
    return;
} // assignUnderEmployed

} // class AssignAndSubtract

?>
