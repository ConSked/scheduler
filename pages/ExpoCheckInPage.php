<?php // $Id: ExpoCheckInPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticateSupervisor.php'); // crew not allowed here

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/StationJob.php');
require_once('db/Worker.php');
require_once('section/CheckInStationList.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/format.php');
require_once('util/session.php');
require_once('util/log.php');

$author = getWorkerAuthenticated();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title><?php echo(SITE_NAME); ?> - Expo View Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">

    <script src="jquery/jquery-1.7.2.min.js"></script>
    <script src="jquery/jquery-ui-1.8.20.custom.min.js"></script>

    <script  type="text/javascript">
		function init()
		{
			hideDateRows();
		}

		function hideDateRows()
		{
			$(document).ready(function() {
				$('#<?php echo PARAM_DATE;?> option').each(function() {
					var dates = this.value.replace(/\s/g, "_");

					if (this.value != 'All')
					{
						if (this.selected == true)
						{
							$('.'+dates+'').show();
						}
						else
						{
							$('.'+dates+'').hide();
						}
					}
					else
					{
						if (this.selected == true)
						{
							$('.All').show();
						}
					}
				});
			});
			$('#<?php echo PARAM_JOB; ?>').val('All');
			$('#search').val('');
		}

		function hideJobRows()
		{
			$(document).ready(function() {
				$('#<?php echo PARAM_JOB;?> option').each(function() {
					var dates = $('#<?php echo PARAM_DATE; ?> option:selected').val().replace(/\s/g, "_");
					var jobTitle = this.value.replace(/\s/g, "_").replace(/\//g, "_");

					if (this.value != 'All')
					{
						if (this.selected == true)
						{
							$('.'+dates+'.'+jobTitle+'').show();
						}
						else
						{
							$('.'+dates+'.'+jobTitle+'').hide();
						}
					}
					else
					{
						if (this.selected == true)
						{
							$('.'+dates+'').show();
						}
					}
				});
			});
			$('#search').val('');
		}

		function searchRows()
		{
			$(document).ready(function() {
				var val = $('#search').val();
				var dates = $('#<?php echo PARAM_DATE; ?> option:selected').val().replace(/\s/g, "_");
				var jobTitle = $('#<?php echo PARAM_JOB; ?> option:selected').val().replace(/\s/g, "_").replace(/\//g, "_");

				$('.'+dates+'.'+jobTitle+'').each(function() {
					var text = $(this).children(':first').text().toLowerCase();
					if (text.search(val.toLowerCase()) != -1)
					{
						$(this).show();
					}
					else
					{
						$(this).hide();
					}
				});
			});
		}
    </script>
</head>

<body onload="init()">
<div id="container">

<?php

$expo = getExpoCurrent();
// use REQUEST as may be a GET
if (is_null($expo))
{
    $expo = getParamItem(PARAM_LIST, PARAM_LIST_INDEX);
    setExpoCurrent($expo); // paranoia about some included section
}
$_SESSION[PARAM_LIST] = NULL;

//setStationCurrent(NULL);

// now go get the workers
$workerList = Worker::selectExpo($expo->expoid);
// should be in order for display
usort($workerList, "WorkerCompare");
$_SESSION[PARAM_LIST] = $workerList;
setWorkerCurrent(NULL); // set null wherever param_list set to workers
$_REQUEST[PARAM_LIST_INDEX] = NULL;

$stationList = StationJob::selectExpo($expo->expoid);
$_SESSION[PARAM_LIST2] = $stationList;
$_REQUEST[PARAM_LIST2_INDEX] = NULL;

// ok, start the html
include('section/header.php');
?>

<div id="main">
	<?php include('section/LinkExpo.php'); ?>

	<div id="expoviewpage_stationlist">
		<?php
			createStationHTMLList($expo, $stationList);
		?>
	</div><!-- expoviewpage_stationlist -->
</div><!-- main -->

<?php
    $menuItemArray = array();
    if ($author->isOrganizer())
    {
        $menuItemArray[] = MENU_VIEW_SITEADMIN;
    	$menuItemArray[] = MENU_SEND_MESSAGE;
        $menuItemArray[] = MENU_VIEW_WORKERLIST;
    }
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- continaer -->
</body></html>
