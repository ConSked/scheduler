<?php // $Id: ExpoAssignConfirmAction.php 2361 2012-10-09 15:46:29Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

$expo = getExpoCurrent();

$workerListAdd = NULL;
if (isset($_SESSION['workerListAdd']))
{
	$workerListAdd = $_SESSION['workerListAdd'];
}

$workerListRemove = NULL;
if (isset($_SESSION['workerListRemove']))
{
	$workerListRemove = $_SESSION['workerListRemove'];
}

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
