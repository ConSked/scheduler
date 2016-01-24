<?php  // $Id: FirstComeFirstServed.php 2396 2012-10-18 18:51:23Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('db/Expo.php');
require_once('db/ShiftAssignment.php');
require_once('db/Worker.php');
require_once('schedule/AbstractScheduler.php');
require_once('schedule/JobSchedule.php');
require_once('schedule/WorkerSchedule.php');
require_once('schedule/ScheduleException.php');
require_once('util/log.php');

class FirstComeFirstServed extends AbstractScheduler
{

public function assignSchedule($expoId)
{
    if ((0 == count($this->jobList)) || (0 == count($this->workerList)))
    {
        return;
    }
    foreach ($this->workerList as $worker)
    {
        $this->assignWorker($worker, $expoId);
    }
    logMessage('FirstComeFirstServed->assignSchedule('.$expoId.")", "assignAll(".$expoId.")");
    return;
} // assignSchedule

public function assignWorker(WorkerSchedule $worker, $expoId)
{
    if (0 == count($this->jobList))
    {
        return;
    }
    $myJobList = $worker->sortJobPreference($this->jobList, FALSE);
    foreach ($myJobList as $job)
    {
        try
        {
            $job->addWorker($worker, $expoId);
            logMessage("FCFS schedule", "expoid:$expoId - job:$job->jobid - worker:$worker->workerid\n");
        }
        catch (ScheduleConflictException $se)
        {
            // logMessage("conflict", $job->jobid . "   " . $se->getMessage());
            // will have to fix later anyway
            $conflict = $se->conflict;
            $worker->swapHappier($job, $conflict, $expoId);
        }
        catch (ScheduleException $se)
        {
            // logMessage("failure", $job->jobid . "  " . $se->getMessage());
            continue; // do not force
        }
    } // $job
} // assignWorker

public static function assignAsYouGo(Expo $expo, Worker $worker)
{
    if ($expo->scheduleAssignAsYouGo) // else just return
    {
        $workerList = NULL;
        $stationList = NULL;
        $assignmentList = NULL;

        try
        {
            // needed for all
            $workerList = WorkerSchedule::selectExpo($expo->expoid);
            $stationList = JobSchedule::selectExpo($expo->expoid);
            $assignmentList = ShiftAssignment::selectExpo($expo->expoid);
        }
        catch (PDOException $ex)
        {
            logMessage("FirstComeFirstServed", "assignAsYouGo(" . $expo->titleString() . ", " . $worker->email . ") - " . $ex->getMessage());
            return;
        }

        foreach ($workerList as $w)
        {
            if ($w->workerid == $worker->workerid)
            {
                $worker = $w;
                break;
            }
        } // $w

        $aas = new FirstComeFirstServed($expo->expoid, $stationList, $workerList, $assignmentList, TRUE);
        $stationList = NULL;
        $assignmentList = NULL;
        $workerList = NULL;

        $d1 = new DateTime();
        logMessage("FirstComeFirstServed", "**** assignSchedule(".$expoId.") ****", $d1->format('H:i'), "\n");
        $aas->assignWorker($worker, $expoId);
        $d2 = new DateTime();
        logMessage("FirstComeFirstServed", "****assignSchedule(".$expoId.") ****", $d2->format('H:i'), "  elapsed: ", $d2->getTimestamp() - $d1->getTimestamp(), "\n");

        // $aas->logJobListState("FirstComeFirstServed", "jobs after assignment");
        // $aas->logWorkerListState("FirstComeFirstServed", "workers after assignment");
        AbstractScheduler::commitSchedule($expo->expoid, TRUE, $aas->getSchedule());
    }
    return;
} // assignAsYouGo

} // class FirstComeFirstServed

?>
