<?php // $Id: ExpoEditAction.php 751 2012-06-26 19:31:30Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/ShiftStatus.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

$expo = getExpoCurrent();
$expoid = NULL;
if ($expo != NULL)
{
	$expoid = $expo->expoid;
}

$stationid = NULL;
if (isset($_REQUEST[PARAM_LIST_INDEX]))
{
	$stationid = swwat_parse_number(html_entity_decode($_REQUEST[PARAM_LIST_INDEX]), FALSE);
}

$workerid = NULL;
$statusType = NULL;
if (isset($_POST) && $_POST != NULL)
{
	$keys = array_keys($_POST);
	$workerid = $keys[0];

	$values = array_values($_POST);
	if (!strcmp($values[0], 'Check In'))
	{
		$statusType = 'CHECK_IN';
	}
	else if (!strcmp($values[0], 'Check Out'))
	{
		$statusType = 'CHECK_OUT';
	}
}

if ($workerid != NULL && $stationid != NULL && $expoid != NULL && $statusType != NULL)
{
	$shiftstatus = new ShiftStatus;

	$shiftstatus->workerid = $workerid;
	$shiftstatus->stationid = $stationid;
	$shiftstatus->expoid = $expoid;
	$shiftstatus->statusType = $statusType;
	$shiftstatus->statusTime = NULL;

	$shiftstatus->insert();
}

header('Location: ShiftCheckInPage.php?'.PARAM_LIST_INDEX.'='.$stationid);
include('ShiftCheckInPage.php');

?>
