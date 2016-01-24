<?php // $Id: ExpoEditAction.php 2431 2003-01-07 20:24:44Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/JobTitle.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

$expo = getExpoCurrent();

if (isset($_POST[PARAM_SAVE]))
{
    $expo->title           = swwat_parse_string(html_entity_decode($_POST[PARAM_TITLE]), true);
    $expo->description     = swwat_parse_string(html_entity_decode($_POST[PARAM_DESCRIPTION]), true);
    $expo->expoHourCeiling = swwat_parse_string(html_entity_decode($_POST[PARAM_MAXHOURS]), true);
    $expo->startTime       = swwat_parse_date(html_entity_decode($_POST[PARAM_STARTTIME]), true);
    $expo->stopTime        = swwat_parse_date(html_entity_decode($_POST[PARAM_STOPTIME]), true);
    $expo->scheduleAssignAsYouGo      = isset($_POST[PARAM_SCHEDULE_ALGO]);
    $expo->scheduleVisible            = isset($_POST[PARAM_SCHEDULE_PUBLISH]);
    $expo->allowScheduleTimeConflict  = isset($_POST[PARAM_SCHEDULE_TIME_CONFLICT]);
    $expo->newUserAddedOnRegistration = isset($_POST[PARAM_NEWUSER_ADDED_ON_REGISTRATION]);

	if (is_null($expo->expoid))
	{
		$expo->insert();

		$jobTitle = new JobTitle();
		$jobTitle->expoid = $expo->expoid;
		$jobTitle->jobTitle = 'Crew';
		$jobTitle->insert();
	}
	else
	{
		$expo->update();
	}

    setExpoCurrent($expo);

    // if saved
    header('Location: ExpoViewPage.php');
    include('ExpoViewPage.php');
    return;
}

// else, if not saved
header('Location: SiteAdminPage.php');
include('SiteAdminPage.php');

?>
