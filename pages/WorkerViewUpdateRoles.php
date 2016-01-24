<?php // $Id: WorkerViewUpdateRoles.php 604 2012-06-07 21:11:57Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

$worker = getWorkerCurrent();
if (isset($_REQUEST[PARAM_AUTHROLE]))
{
	$worker->authrole = swwat_parse_enum(html_entity_decode($_REQUEST[PARAM_AUTHROLE]), RoleEnum::$ROLE_ARRAY, true);
	$worker = $worker->updateRole();
}

// in all cases
header('Location: WorkerViewPage.php');
include('WorkerViewPage.php');

?>
