<?php // $Id: ShiftStatusEditPage.php 2435 2012-11-30 19:56:05Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/StationJob.php');
require_once('section/Menu.php');
require_once('section/ShiftStatusData.php');
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

    <title><?php echo(SITE_NAME); ?> - Shift Status Edit Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php
$type = NULL;
$index = NULL;
if (isset($_REQUEST[PARAM_LIST_INDEX]))
{
	list ($type, $index) = explode(':', $_REQUEST[PARAM_LIST_INDEX]);
}

if ($type == 'W')
{
	$expo = getExpoCurrent();
	$station = getStationCurrent();

	$worker = Worker::selectID($index);
	setWorkerCurrent($worker);
}
else if ($type == 'S')
{
	$expo = getExpoCurrent();
	$worker = getWorkerCurrent();

	$station = StationJob::selectID($index);
	setStationCurrent($station);
}

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php include('section/LinkStationWorker.php'); ?>

<div id="shiftstatusdata_table">
	<form method="POST" id="shiftstatuseditpage_shiftstatusdata"
	      action="ShiftStatusEditAction.php?<?php echo(PARAM_LIST_INDEX.'='.$type.':'.$index); ?>">
	<table>
   	<?php
		if (!is_null($worker->workerid))
		{
			createShiftStatusDataHTMLList($expo->expoid, $station->stationid, $worker->workerid, 'Save', FALSE);
		}
	    ?>
	</table>
	</form>
</div><!-- checkinclient_workerlist -->

</div><!-- main -->

<?php
    $menuItemArray = array();
    if ($author->isOrganizer())
	{
    	$menuItemArray[] = MENU_VIEW_SITEADMIN;
        $menuItemArray[] = MENU_VIEW_WORKERLIST;
        $menuItemArray[] = MENU_CHECKIN_CLIENT;
    }
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
