<?php // $Id: StationViewPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/StationJob.php');
require_once('section/Menu.php');
require_once('section/StationData.php');
require_once('section/WorkerList.php');
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

    <title><?php echo(SITE_NAME); ?> - Station View Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
	<script src="swwat/js/validate.js"></script>
</head>

<body>
<div id="container">

<?php

$expo = getExpoCurrent();
// use REQUEST as may be a GET
if (isset($_REQUEST[PARAM_LIST2_INDEX]))
{
	$station = getParamItem(PARAM_LIST2, PARAM_LIST2_INDEX);
	if (isset($station))
	{
		if (!($station instanceof StationJob))
		{
			// used in the reports, etc
			if (($station instanceof Job) || ($station instanceof ShiftAssignment))
			{
				$station = StationJob::selectID($station->stationid);
			}
			else // just give it a shot!
			{
				$station = StationJob::selectID($station->stationid);
			}
		}
		setStationCurrent($station);
	}
}
$_SESSION[PARAM_LIST2] = NULL;
$station = getStationCurrent();

$workerList = Worker::selectStation($station->stationid);
$_SESSION[PARAM_LIST] = $workerList;
$_REQUEST[PARAM_LIST2_INDEX] = NULL;

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php include('section/LinkExpo.php'); ?>

    <div id="stationviewpage_stationdata">
        <table>
            <tr style="vertical-align:top">
                <td><table><?php createStationDataHTMLRows($station, $expo, "stationviewpage_stationdata", TRUE); ?></table></td>
                <td style="min-width:25px"><!-- spacer --></td>
                <td><table><?php createJobDataHTMLRows($station, "stationviewpage_stationdata", FALSE, TRUE); ?></table></td>
            </tr>
        </table>

        <?php
        if (!$expo->isPast())
        {
            echo "<table>\n<tr>\n";
            if ($author->isOrganizer())
            {
            ?>
                <td><form method="GET" name="stationviewpage_stationdelete" action="StationDeletePage.php">
                <input class="fieldValue" type="Submit" value="Delete"/></form></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><form method="GET" name="stationviewpage_stationedit_form" action="StationEditPage.php">
                <input class="fieldValue" type="Submit" value="Edit"/></form></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><form method="GET" name="stationviewpage_stationcopy_form" action="StationEditPage.php">
                <input class="fieldValue" type="Submit" name ="<?php echo PARAM_COPY; ?>" value="Copy"/></form></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <?php
            }
            if ($author->isOrganizer() || $author->isSupervisor())
            {
            ?>
                <td><form method="GET" name="stationviewpage_stationcheckin_form" action="ShiftCheckInPage.php">
                <input class="fieldValue" type="Submit" value="Check-In"/></form></td>
            <?php
            }
            echo "</tr>\n</table>\n<br/>\n";
        } // isPast
		?>
    </div><!-- Stationviewpage_Stationdata -->

    <div id="stationviewpage_workerdata">
		<?php
			if (!$expo->isPast())
			{
				echo '<h5>Station Staff</h5>';
				if (!$author->isCrewMember())
				{
					echo '<form method="GET" name="expoviewpage_assign_form" action="ShiftAssignPage.php">';
					echo '<input class="fieldValue" type="Submit" value="Assign Staff to Station"/></form>';
				}
				createWorkerHTMLList($workerList, $author);
			}
		?>
	</div> <!-- Stationviewpage_workerdata -->

    <div id="stationviewpage_detailpreferences">
		<?php
			if (($author->isCrewMember() || $author->isSupervisor()) && !$expo->isPast())
			{
				echo '<h5>Set Station Preference</h5>';
                // works for CIW only because station:job = 1:1
				createStationPreferenceHTMLList($author->workerid, $station->jobid);
			}
		?>
	</div> <!-- Stationviewpage_workerdata -->
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

</div><!-- container -->
</body></html>

<?php
function createStationPreferenceHTMLList($workerid, $jobid)
{
	$preference = ShiftPreference::selectID($workerid, $jobid);
    $_POST[PARAM_DESIRE] = $preference->desirePercent;

	echo '<form method="POST" name="stationviewpage_stationsetpreference_form" action="ShiftPreferenceExplicitAction.php">';
    swwat_createInputValidateInteger(PARAM_DESIRE, "stationviewpage_stationsetpreference_form", 3, FALSE);
	echo '&nbsp;&nbsp;<input class="fieldValue" type="Submit" value="Save Preference"/><p/>';
	echo '</form>';
}
?>
