<?php // $Id: ScheduleExpoDeleteAction.php 2297 2012-09-30 15:04:17Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/ShiftAssignment.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

$expo = getExpoCurrent();

if ($expo != NULL)
{
	ShiftAssignment::deleteExpo($expo->expoid);
	$_SESSION[PARAM_DELETE] = true;
}

session_write_close();
header('Location: ScheduleExpoPage.php');
include('ScheduleExpoPage.php');

?>
