<?php // $Id: StationDeleteAction.php 1706 2012-09-05 01:51:53Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/StationJob.php');
require_once('util/session.php');

$station = getStationCurrent();

if (isset($_POST[PARAM_SAVE]))
{
    $station->delete();
}

header('Location: ExpoViewPage.php');
include('ExpoViewPage.php');

?>
