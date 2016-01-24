<?php // $Id: StationLockReportPage.php 2251 2012-09-24 20:02:19Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');
require_once('properties/constants.php');
require_once('util/log.php');
require_once('util/session.php');

$author = getWorkerAuthenticated();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Station Lock Report Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">

</head>

<body>
<div id="container">

<?php
require_once('db/Worker.php');
require_once('section/StationLockReportMatrix.php');
require_once('section/Menu.php');

$expo = getExpoCurrent();

$worker = getWorkerCurrent();
if (isset($_REQUEST[PARAM_LIST_INDEX]))
{
	$worker = getParamItem(PARAM_LIST, PARAM_LIST_INDEX);
	setWorkerCurrent($worker);
}

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php include('section/LinkExpoWorker.php'); ?>
	<div id="stationLockReport_stationmatrix">
	<?php
		createStationLockHTMLMatrix($expo->expoid, $worker->workerid);
	?>
	</div>
</div><!-- main -->

<?php
    $menuItemArray = array();
	$menuItemArray[] = MENU_SCHEDULING_REPORT;
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
