<?php // $Id: ShiftPreferenceExplicitAction.php 1800 2012-09-07 04:23:26Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('db/ShiftPreference.php');
require_once('db/StationJob.php');
require_once('db/Worker.php');
require_once('properties/constants.php');
require_once('swwat/gizmos/parse.php');
require_once('util/log.php');
require_once('util/session.php');

/**
 * This Controller is used by the StationViewPage's submit button
 */

$author = getWorkerAuthenticated();
$job = getStationCurrent();

// note only works for CIW when station:job = 1:1
$preference = ShiftPreference::selectID($author->workerid, $job->jobid);
try
{
    $desire = swwat_parse_integer(html_entity_decode($_POST[PARAM_DESIRE]), 3, FALSE);
    $preference->setDesire($desire);
    $preference->update();
}
catch (ParseSWWATException $ex)
{
    // ignore, but do nothing!
}

header('Location: StationViewPage.php');
include('StationViewPage.php');

?>
