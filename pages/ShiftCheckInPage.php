<?php // $Id: ShiftCheckInPage.php 681 2012-06-15 07:51:14Z swash $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/StationJob.php');
require_once('section/ShiftCheckInList.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/format.php');
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

    <title>SwiftShift - Shift Check-In Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
	<script type="text/javascript">
		function viewShiftStatus(workerid)
		{
			document.ShiftCheckIn_form.action = "ShiftStatusViewPage.php?"+"<?php echo(PARAM_LIST_INDEX); ?>"+"=W:"+workerid;
			document.ShiftCheckIn_form.submit();
			return;
		}
	</script>
</head>

<body>
<div id="container">

<?php

$expo = getExpoCurrent();

if (isset($_REQUEST[PARAM_LIST_INDEX]))
{
	$station = StationJob::selectID($_REQUEST[PARAM_LIST_INDEX]);
	setStationCurrent($station);
}
else
{
	$station = getStationCurrent();
}

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php include('section/LinkStation.php'); ?>

    <div id="checkinclient_workerlist">
    	<?php
		createShiftCheckInHTMLList($expo->expoid, $station->stationid);
	    ?>
    </div><!-- checkinclient_workerlist -->
</div><!-- main -->

<?php
    $menuItemArray = array();
    if ($author->isOrganizer())
	{
    	$menuItemArray[] = MENU_VIEW_SITEADMIN;
        $menuItemArray[] = MENU_VIEW_WORKERLIST;
    }
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
