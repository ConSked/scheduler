<?php // $Id: ShiftStatusEditAction.php 2435 2012-11-30 19:56:05Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

include('util/authenticate.php');

require_once('db/Expo.php');
require_once('db/StationJob.php');
require_once('db/ShiftStatus.php');
require_once('properties/constants.php');
require_once('util/log.php');
require_once('util/session.php');

$type = NULL;
$index = NULL;
if (isset($_REQUEST[PARAM_LIST_INDEX]))
{
	list ($type, $index) = explode(':', $_REQUEST[PARAM_LIST_INDEX]);
}

if ($type == 'W')
{
	$expo = getExpoCurrent();
	$station = getStationCurrent();

	$worker = Worker::selectID($index);
	setWorkerCurrent($worker);
}
else if ($type == 'S')
{
	$expo = getExpoCurrent();
	$worker = getWorkerCurrent();

	$station = StationJob::selectID($index);
	setStationCurrent($station);
}

if (isset($_POST[PARAM_SAVE]))
{
	$c = count($_POST[PARAM_STATUSID]);

	for ($k = 0; $k < $c; $k++)
	{
		$shiftstatus_new[$k] = new ShiftStatus();

		if ($_POST[PARAM_STATUSDATE][$k] != "" && $_POST[PARAM_STATUSHOUR][$k] != "")
		{
			$shiftstatus_new[$k]->shiftstatusid = swwat_parse_integer(html_entity_decode($_POST[PARAM_STATUSID][$k]), 11, true);
			$shiftstatus_new[$k]->workerid      = swwat_parse_integer(html_entity_decode($worker->workerid), 11, true);
			$shiftstatus_new[$k]->stationid     = swwat_parse_integer(html_entity_decode($station->stationid), 11, true);
			$shiftstatus_new[$k]->expoid        = swwat_parse_integer(html_entity_decode($expo->expoid), 11, true);
			$shiftstatus_new[$k]->statusType    = swwat_parse_string(html_entity_decode($_POST[PARAM_STATUSTYPE][$k]), true);
			$shiftstatus_new[$k]->statusTime    = swwat_parse_date(html_entity_decode($_POST[PARAM_STATUSDATE][$k] . " " . $_POST[PARAM_STATUSHOUR][$k]), true);
		}
		else if ($_POST[PARAM_STATUSDATE][$k] == "" && $_POST[PARAM_STATUSHOUR][$k] != "")
		{
			$shiftstatus_new[$k]->shiftstatusid = swwat_parse_integer(html_entity_decode($_POST[PARAM_STATUSID][$k]), 11, true);
			$shiftstatus_new[$k]->workerid      = swwat_parse_integer(html_entity_decode($worker->workerid), 11, true);
			$shiftstatus_new[$k]->stationid     = swwat_parse_integer(html_entity_decode($station->stationid), 11, true);
			$shiftstatus_new[$k]->expoid        = swwat_parse_integer(html_entity_decode($expo->expoid), 11, true);
			$shiftstatus_new[$k]->statusType    = swwat_parse_string(html_entity_decode($_POST[PARAM_STATUSTYPE][$k]), true);
			if ($_POST[PARAM_STATUSTYPE][$k] == "CHECK_IN")
			{
				$shiftstatus_new[$k]->statusTime    = swwat_parse_date(html_entity_decode($_POST[PARAM_STATUSDATE][$k+1] . " " . $_POST[PARAM_STATUSHOUR][$k]), true);
			}
			else if ($_POST[PARAM_STATUSTYPE][$k] == "CHECK_OUT")
			{
				$shiftstatus_new[$k]->statusTime    = swwat_parse_date(html_entity_decode($_POST[PARAM_STATUSDATE][$k-1] . " " . $_POST[PARAM_STATUSHOUR][$k]), true);
			}
		}
		else
		{
			$shiftstatus_new[$k]->shiftstatusid = NULL;
			$shiftstatus_new[$k]->workerid      = NULL;
			$shiftstatus_new[$k]->stationid     = NULL;
			$shiftstatus_new[$k]->expoid        = NULL;
			$shiftstatus_new[$k]->statusType    = NULL;
			$shiftstatus_new[$k]->statusTime    = NULL;
		}
	}

	for ($k = 0; $k < $c; $k++)
	{
		if (!is_null($shiftstatus_new[$k]->shiftstatusid))
		{
			$shiftstatus_old = ShiftStatus::selectID($shiftstatus_new[$k]->shiftstatusid);
			if ($shiftstatus_new[$k]->workerid != $shiftstatus_old->workerid ||
			    $shiftstatus_new[$k]->stationid != $shiftstatus_old->stationid ||
			    $shiftstatus_new[$k]->expoid != $shiftstatus_old->expoid ||
			    $shiftstatus_new[$k]->statusType != $shiftstatus_old->statusType ||
				swwat_format_isodatetime($shiftstatus_new[$k]->statusTime) != swwat_format_isodatetime($shiftstatus_old->statusTime))
			{
				$shiftstatus_new[$k]->update();
			}
		}
		else if (!is_null($shiftstatus_new[$k]->statusType))
		{
			$shiftstatus_new[$k]->insert();
		}
	}

	header("Location: ShiftStatusViewPage.php?".PARAM_LIST_INDEX."=".$type.":".$index);
	include("ShiftStatusViewPage.php");
	return;
}

header('Location: ShiftCheckInPage.php');
include('ShiftCheckInPage.php');

?>
