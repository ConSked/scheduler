<?php // $Id: StationEditAction.php 2431 2003-01-07 20:24:44Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.

include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/StationJob.php');
require_once('db/JobTitle.php');
require_once('db/Worker.php');
require_once('db/JobTitle.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

$station = getStationCurrent();

if (isset($_REQUEST['copy']))
{
	$station->stationid = NULL;
}

if (isset($_POST[PARAM_SAVE]))
{
    $titleEnums = JobTitle::titleEnums($station->expoid);

    try
    {
        $station->startTime          = swwat_parse_date(html_entity_decode($_POST[PARAM_DATE] . " " . $_POST[PARAM_STARTHOUR]), true);
        $station->stopTime           = swwat_parse_date(html_entity_decode($_POST[PARAM_DATE] . " " . $_POST[PARAM_STOPHOUR]), true);
        $station->jobTitle           = swwat_parse_enum($_POST[PARAM_JOB], $titleEnums, false);
        $station->maxCrew            = swwat_parse_integer(html_entity_decode($_POST[PARAM_MAXCREW]), 11, true);
        $station->minCrew            = swwat_parse_integer(html_entity_decode($_POST[PARAM_MINCREW]), 11, true);
        $station->maxSupervisor      = swwat_parse_integer(html_entity_decode($_POST[PARAM_MAXSUPERVISOR]), 11, true);
        $station->minSupervisor      = swwat_parse_integer(html_entity_decode($_POST[PARAM_MINSUPERVISOR]), 11, true);
        $station->description        = swwat_parse_string(html_entity_decode($_POST[PARAM_DESCRIPTION]), true);
        $station->title              = swwat_parse_string(html_entity_decode($_POST[PARAM_TITLE]), true);
        $station->location           = swwat_parse_string(html_entity_decode($_POST[PARAM_LOCATION]), true);
        $station->instruction        = swwat_parse_string(html_entity_decode($_POST[PARAM_INSTRUCTION]), true);
    }
    catch (Exception $ex)
    {
        header('Location: WorkerLoginPage.php');
        include('WorkerLoginPage.php');
        return;
    }

    $station = is_null($station->stationid) ? $station->insert() : $station->update();
    setStationCurrent($station);

    // if saved
    header('Location: StationViewPage.php');
    include('StationViewPage.php');
    return;
}

// else, if not saved
header('Location: SiteAdminPage.php');
include('SiteAdminPage.php');

?>
