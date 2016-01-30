<?php  // $Id: StationJob.php 2436 2012-11-30 20:24:43Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/Job.php');
require_once('db/Station.php');
require_once('util/date.php');
require_once('util/log.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');


// This is an interim object meant for mainting backward compatibility until the UI catches up
class StationJob
{

// from Station
private $station;
public $stationid;
public $expoid;
public $startTime;
public $stopTime;
public $title;
public $description;
public $location;
public $URL;
public $instruction;

private $job;
public $jobid;
public $jobTitle;
public $maxCrew;
public $minCrew;
public $assignedCrew;
public $maxSupervisor;
public $minSupervisor;
public $assignedSupervisor;

public function titleString()
{
    return $this->station->locationString() . " - " . $this->jobTitleString();
} // titleString

public function stationTitleString()
{
    return $this->station->titleString();
} // titleString

/**
 * @deprecated this method will not work in a 1:N world of station:job, only in the 1:1 case of pre-CIW
 */
public function jobTitleString()
{
    return $this->job()->titleString();
} // jobTitleString

/**
 * @deprecated this method will not work in a 1:N world of station:job, only in the 1:1 case of pre-CIW
 */
public function job()
{
    return $this->job;
} // job

public static function selectID($stationId)
{
    $stationJob = new StationJob();
    // note that StationJob has a unique stationid for each jobid (which is where the UI needs to catch up)
    $station = Station::selectID($stationId);
    $stationJob->setStation($station);
    $jobs = Job::selectStation($station->expoid, $station->stationid);
    $stationJob->setJob($jobs[0]);
    return $stationJob;
} // selectID

public static function selectExpo($expoId)
{
    $stations = Station::selectExpo($expoId);
    $stationjobs = array();
    foreach ($stations as $station)
    {
        $stationJob = new StationJob();
        $stationJob->setStation($station);
        $jobs = Job::selectStation($station->expoid, $station->stationid);
        $stationJob->setJob($jobs[0]);
        $stationjobs[] = $stationJob;
    }
    return $stationjobs;
} // selectExpo

/*
public static function selectWorker($workerId, $date = NULL, $expoId = NULL)
{
    $stations = Station::selectWorker($workerId, $date. $expoId);
    $stationjobs = array();
    foreach ($stations as $station)
    {
        $stationJob = new StationJob();
        $stationJob->setStation($station);
        $jobs = Job::selectStation($station->expoid, $station->stationid);
        $stationJob->setJob($jobs[0]);
        $stationjobs[] = $stationJob;
    }
    return $stationjobs;
} // selectWorker
*/

private function getStation()
{
    $station = new Station();
    $station->stationid = $this->stationid;
    $station->expoid = $this->expoid;
    $station->startTime = $this->startTime;
    $station->stopTime = $this->stopTime;
    $station->stationTitle = $this->title;
    $station->description = $this->description;
    $station->location = $this->location;
    $station->URL = $this->URL;
    $station->instruction = $this->instruction;
    //
    return $station;
} // getStation

private function setStation(Station $station)
{
    $this->station = $station;
    $this->stationid = $station->stationid;
    $this->expoid = $station->expoid;
    $this->startTime = $station->startTime;
    $this->stopTime = $station->stopTime;
    $this->title = $station->stationTitle;
    $this->description = $station->description;
    $this->location = $station->location;
    $this->URL = $station->URL;
    $this->instruction = $station->instruction;
    return;
} // setStation

private function getJob()
{
    $job = new Job();
    $job->jobid = $this->jobid;
    $job->stationid = $this->stationid;
    $job->expoid = $this->expoid;
    $job->jobTitle = $this->jobTitle;
    $job->maxCrew = $this->maxCrew;
    $job->minCrew = $this->minCrew;
    $job->assignedCrew = $this->assignedCrew;
    $job->maxSupervisor = $this->maxSupervisor;
    $job->minSupervisor = $this->minSupervisor;
    $job->assignedSupervisor = $this->assignedSupervisor;
    return $job;
} // getJob

private function setJob(Job $job)
{
    $this->job = $job;
    $this->jobid = $job->jobid;
    $this->jobTitle = $job->jobTitle;
    $this->maxCrew = $job->maxCrew;
    $this->minCrew = $job->minCrew;
    $this->assignedCrew = $job->assignedCrew;
    $this->maxSupervisor = $job->maxSupervisor;
    $this->minSupervisor = $job->minSupervisor;
    $this->assignedSupervisor = $job->assignedSupervisor;
    return;
} // setJob

public function insert()
{
    $station = $this->getStation();
    $this->setStation($station->insert());
    $job = $this->getJob();
    $job->jobid = NULL;
    // note order
    $this->setJob($job->insert());
    return $this;
} // insert

public function update()
{
    $station = $this->getStation();
    $job = $this->getJob();
    // note order
    $station->update();
    $job->update();
    return $this;
} // update

public function delete()
{
    $station = $this->getStation();
    $job = $this->getJob();
    // note order
    $job->delete();
    $station->delete();
    return $this;
} //delete

public function isPast()
{
    return $this->station->isPast();
} // isPast

public function oddWorkerList($workerList)
{
    return $this->station->oddWorkerList($workerList);
} // oddWorkerList

} // Station

?>
