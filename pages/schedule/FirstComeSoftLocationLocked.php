<?php  // $Id: FirstComeSoftLocationLocked.php 2259 2012-09-25 02:50:21Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('schedule/FirstComeFirstServed.php');
require_once('schedule/JobSchedule.php');
require_once('schedule/WorkerSchedule.php');
require_once('schedule/ScheduleException.php');
require_once('util/log.php');

class FirstComeSoftLocationLocked extends FirstComeFirstServed
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
    foreach ($groupByLocation as $job)
    {
        try
        {
            $job->addWorker($worker, $expoId);
        }
        // catch (ScheduleConflictException $se) - major difference from FirstComeFirstServed
        catch (ScheduleException $se)
        {
            // logMessage("failure", $job->jobid . "  " . $se->getMessage());
            continue; // do not force
        }
    } // $job
    $groupByLocation = NULL;
} // assignWorker

} // class FirstComeSoftLocationLocked

?>
