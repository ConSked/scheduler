<?php  // $Id: MessageData.php 2310 2012-10-01 22:03:17Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('db/Expo.php');
require_once('db/ShiftAssignmentView.php');
require_once('db/StationJob.php');
require_once('db/Worker.php');
require_once('util/date.php');
require_once('util/log.php');

class MessageData
{

public $workerid;
public $stationid;
public $expoid;
public $expo;
public $station;
public $startTime;
public $stopTime;
public $workerName;
public $workerEmail;

public function fillData(ShiftAssignmentView $shiftassignment)
{
	$this->workerid = $shiftassignment->workerid;
	$this->stationid = $shiftassignment->stationid;
	$this->expoid = $shiftassignment->expoid;

	$this->expo = $shiftassignment->expoTitle;

	$this->station = $shiftassignment->location." (".$shiftassignment->stationTitle.")";
	$this->startTime = $shiftassignment->startTime;
	$this->stopTime = $shiftassignment->stopTime;

	$worker = Worker::selectID($shiftassignment->workerid);

	$this->workerName = $worker->nameString2();
	$this->workerEmail = $worker->email;

	return $this;
}

}

?>
