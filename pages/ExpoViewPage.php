<?php // $Id: ExpoViewPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticateSupervisor.php'); // crew not allowed here

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/Job.php');
require_once('db/StationJob.php');
require_once('db/Worker.php');
require_once('section/StationList.php');
require_once('section/ExpoData.php');
require_once('section/Menu.php');
require_once('section/WorkerList.php');
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
        var DISPLAY_FORMAT = 'DD dd, MM yy';
        var DB_FORMAT = 'yy-mm-dd';
        $(document).ready(function()
        {
            var starttime = $('#<?php echo PARAM_STARTTIME;?>');
            var stoptime = $('#<?php echo PARAM_STOPTIME;?>');

            var d = $.datepicker.parseDate(DB_FORMAT, starttime.val());
            starttime.val($.datepicker.formatDate(DISPLAY_FORMAT, d));

            d = $.datepicker.parseDate(DB_FORMAT, stoptime.val());
            stoptime.val($.datepicker.formatDate(DISPLAY_FORMAT, d));
        });

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
unset($_SESSION[PARAM_MESSAGE]);

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

    <div id="expoviewpage_expodata">
        <?php
        createExpoDataHTMLRows($expo, "", TRUE);
        if ($author->isOrganizer() && !$expo->isPast())
        {
            // you can edit yourself
            echo '<form method="GET" name="expoviewpage_expodata_form" action="ExpoEditPage.php">';
            echo '<input class="fieldValue" type="Submit" value="Edit"/></form>';
		}
        ?>
    </div><!-- expoviewpage_expodata -->

    <?php
    // table of 3 columns - each column a table of 3 rows, 1 column
    if (($author->isOrganizer() || $author->isSupervisor()) && !$expo->isPast())
    {
        // table of 3 (5 with spacers) columns - each column a table of 3 rows, 1 column
    ?>
	<div id="expoviewpage_functions">
		<h5>Expo Functions</h5>
        <table>
        <tr>
            <td>
                <form method="GET" name="expoviewpage_checkindashboard_form" action="CheckInWorkerDashboardPage.php">
                <input class="fieldValue" type="Submit" value="Check-In Dashboard Report"/>
                </form>
            </td>
            <td></td>
            <?php
            if ($author->isOrganizer()) // you get 2 extra columns!
            {
            ?>
            <td>
                <form method="GET" name="expoviewpage_scheduleexpo_form" action="ScheduleExpoPage.php">
                <input class="fieldValue" type="Submit" value="Schedule Expo"/>
                </form>
            </td>
            <td></td>
            <td>
                <form method="GET" name="expoviewpage_invitation_form" action="InvitationPage.php">
                <input class="fieldValue" type="Submit" value="Registration Invitations"/>
                </form>
            </td>
            <?php
            }
            else
            {
                echo "<td></td><td></td><td></td>\n"; // put in empty columns
            }
            ?>
        </tr>
        <tr><td></td><td></td><td></td><td></td><td></td></tr><!-- extra row, 3 (5) columns -->
        <tr>
            <td>
                <form method="GET" name="expoviewpage_checkingrid_form" action="ShiftCheckInGridPage.php">
                <input class="fieldValue" type="Submit" value="Check-In Grid"/>
                </form>
            </td>
            <td></td>
            <?php
            if ($author->isOrganizer()) // you get 2 extra columns!
            {
            ?>
            <td>
                <form method="GET" name="expoviewpage_dashboardreport" action="SchedulingReportPage.php">
                <input class="fieldValue" type="Submit" value="Scheduling Report"/>
                </form>
            </td>
            <td></td>
            <td>
                <form method="GET" name="expoviewpage_documentreport" action="WorkerDocumentPage.php">
                <input class="fieldValue" type="Submit" value="Document Report"/>
                </form>
            </td>
            <?php
            }
            else
            {
                echo "<td></td><td></td><td></td>\n"; // put in empty columns
            }
            ?>
		</tr></table>
    </div><!-- expoviewpage_functions -->
    <?php
    }
    ?>

	<div id="expoviewpage_stationlist">
		<h5>Expo Stations</h5>
		<?php
			if (!$author->isOrganizer() && !$expo->isPast())
			{
        echo '<form method="GET" name="expoviewpage_preferences_form" action="PreferenceWelcomePage.php">', "\n";
        echo '<input class="fieldValue" type="Submit" value="Set Shift Preferences"/></form><br/>', "\n";
			}
			if ($author->isOrganizer() && !$expo->isPast())
			{
        echo '<form method="GET" name="expoviewpage_newstation_form" action="StationEditPage.php">';
        echo '<input class="fieldValue" name="' . PARAM_CREATE . '" type="Submit" value="Create New Station"/></form><br/>', "\n";
			}
			createStationHTMLList($expo, $stationList);
		?>
	</div><!-- expoviewpage_stationlist -->

    <div id="expoviewpage_workerlist">
        <?php
            if ($author->isOrganizer() && !$expo->isPast())
            {
                echo '<h5>Expo Staff</h5>';
                echo '<form method="GET" name="expoviewpage_assign_form" action="ExpoAssignPage.php">';
                echo '<input class="fieldValue" type="Submit" value="Assign Staff to Expo"/></form>';
                createWorkerHTMLList($workerList, $author);
            }
        ?>
    </div><!-- expoviewpage_workerlist -->
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
