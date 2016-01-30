<?php // $Id: ExpoAssignAction.php 2424 2003-01-01 21:55:06Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

$expo = getExpoCurrent();

$workerListNew = NULL;
if (isset($_SESSION['workerList']))
{
	$workerListNew = $_SESSION['workerList'];
}

$workerListOld = Worker::selectExpo($expo->expoid);

$workerListRemove = $workerListOld;
for ($k = 0; $k < count($workerListRemove); $k++)
{
	$worker = $workerListRemove[$k];
	if (in_array($worker, $workerListNew))
	{
		$workerListRemove[$k] = NULL;
	}
}
$workerListRemove = array_filter($workerListRemove);

$workerListAdd = $workerListNew;
for ($k = 0; $k < count($workerListAdd); $k++)
{
	$worker = $workerListAdd[$k];
	if (in_array($worker, $workerListOld))
	{
		$workerListAdd[$k] = NULL;
	}
}
$workerListAdd = array_filter($workerListAdd);

foreach ($workerListAdd as $worker)
{
	$worker->assignToExpo($expo->expoid);
	logMessage("Assign to expo", "expo:$expo->expoid - worker:$worker->workerid\n");
}
foreach ($workerListRemove as $worker)
{
	$worker->removeFromExpo($expo->expoid);
	logMessage("Remove from expo", "expo:$expo->expoid - worker:$worker->workerid\n");
}

header('Location: ExpoViewPage.php');
include('ExpoViewPage.php');

?>
