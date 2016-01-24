<?php // $Id: CheckInWorkerDashboardPage.php 1315 2012-08-14 17:51:31Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticate.php');
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

    <title>SwiftShift - Check-In Dashboard Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php
require_once('db/Expo.php');
require_once('section/CheckInWorkerDashboardList.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/format.php');

$expo = getExpoCurrent();

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php include('section/LinkExpo.php'); ?>

	<div id="checkindashboard_stationlist">
		<?php createCheckInWorkerDashboardHTMLList($expo->expoid); ?>
	</div><!-- checkindashboard_stationlist -->
</div><!-- main -->

<?php
    $menuItemArray = array();
	$menuItemArray[] = MENU_CHECKIN_STATION_DASHBOARD;
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
