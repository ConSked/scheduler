<?php // $Id: JobTitleAction.php 2424 2003-01-01 21:55:06Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/JobTitle.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

$expo = getExpoCurrent();

if (isset($_REQUEST['id']) && isset($_REQUEST['type']))
{
	$jt = new JobTitle();

	$jt->expoid = $expo->expoid;

	if ($_REQUEST['type'] == 'add')
	{
		$jt->jobTitle = $_POST[PARAM_TITLE.$_REQUEST['id']];
		$jt->insert();
	}
	else if ($_REQUEST['type'] == 'delete')
	{
		$jt->jobTitle = $_POST[PARAM_TITLE.$_REQUEST['id']];
		$jt->delete();
	}
	else if ($_REQUEST['type'] == 'edit')
	{
		$jt->jobTitle = $_POST[PARAM_TITLE.$_REQUEST['id']."_old"];
		$jt->update($_POST[PARAM_TITLE.$_REQUEST['id']]);
	}
}

header('Location: JobTitlePage.php');
include('JobTitlePage.php');

?>
