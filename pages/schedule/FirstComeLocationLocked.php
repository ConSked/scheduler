<?php  // $Id: FirstComeLocationLocked.php 2292 2012-09-28 18:21:23Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('schedule/FirstComeFirstServed.php');
require_once('schedule/JobSchedule.php');
require_once('schedule/WorkerSchedule.php');
require_once('schedule/ScheduleException.php');
require_once('util/log.php');

class FirstComeLocationLocked extends FirstComeFirstServed
{

public function assignWorker(WorkerSchedule $worker, $expoId)
{
    if (0 == count($this->jobList))
    {
        return;
    }
    // jobs in preference order
    $myJobList = $worker->sortJobPreference($this->jobList, FALSE);
    // first get locations in preference order
    $locations = array();
    foreach ($myJobList as $job)
    {
        if (!in_array($job->location, $locations))
        {
            $locations[] = $job->location;
        }
    } // $job
    // next reorder jobs ... order by preference group by location!
    $groupByLocation = array();
    foreach ($locations as $location)
    {
        foreach ($myJobList as $job)
        {
            if (0 == strcmp($job->location, $location))
            {
                $groupByLocation[] = $job;
            }
        } // $job
    } // $location
    $myJobList = NULL;
    $locations = NULL;
    $lockLocation = NULL;
    logMessage("LocationLock", "assigning workerid:" . $worker->workerid .
        "  count(gbl):" . count($groupByLocation));
    foreach ($groupByLocation as $job)
    {
        try
        {
            logMessage("LocationLock - isnull?", "jobid:" . $job->jobid . " in try  " );
            if (!is_null($lockLocation))
            {
                logMessage("LocationLock - strcmp", "lockLocation:" . $lockLocation . " after is_null  " . $job->location);
                if (0 != strcmp($lockLocation, $job->location))
                {
                    logMessage("LocationLock - break",  "jobid:" . $job->jobid . "  lockLocation:" . $lockLocation . " before break  " );
                    break; // leave loop
                }
            }
            logMessage("LocationLock - addwprler",  "jobid:" . $job->jobid . "  worker:" . $worker->workerid);
            $job->addWorker($worker, $expoId); // exception leaves $lockLocation NULL
            logMessage("LocationLock - set lockLocation",  "job->location:" . $job->location);
            $lockLocation = $job->location;
        }
        // catch (ScheduleConflictException $se) - major difference from FirstComeFirstServed
        catch (ScheduleException $se)
        {
            logMessage("LocationLock failure", $job->jobid . "  " . $se->getMessage());
            continue; // do not force
        }
    } // $job
    $groupByLocation = NULL;
} // assignWorker

} // class FirstComeLocationLocked
?>
