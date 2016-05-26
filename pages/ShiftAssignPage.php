<?php // $Id: ShiftAssignPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/ShiftAssignment.php');
require_once('db/StationJob.php');
require_once('db/Worker.php');
require_once('section/ExpoData.php');
require_once('section/Menu.php');
require_once('section/StationData.php');
require_once('section/WorkerStation.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
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

    <title><?php echo(SITE_NAME); ?> - Shift Assign Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">

    <script type="text/javascript">
        function selectAllRight()
        {
            var select = document.shiftassignpage_form.elements['list_index[]'];
            for (var k = 0; k < select.options.length; k++)
            {
                select.options[k].selected = true;
            } // var k
        } // selectAllRight

        function move(from, to)
        {
            // backwards due to removals
            for (var k = from.options.length - 1; k > -1; k--)
            {
                var option = from.options[k];
                if (option.selected)
                {
                    from.removeChild(option);
                    to.appendChild(option);
                }
            } // var k
            selectAllRight();
        } // move

        function sendRight()
        {
            // note we presume PARAM_LIST_MULTIPLE == 'list_index[]'
            move(document.shiftassignpage_form.ignored,
                 document.shiftassignpage_form.elements['list_index[]']);
        } // sendRight
        function sendLeft()
        {
            // note we presume PARAM_LIST_MULTIPLE == 'list_index[]'
            move(document.shiftassignpage_form.elements['list_index[]'],
                 document.shiftassignpage_form.ignored);
        } // sendLeft
    </script>
</head>

<body>
<div id="container">

<?php

$expo = getExpoCurrent();
// must have clicked a button to be 2nd time through
$firstTimeThrough = !(isset($_REQUEST["add"]) || isset($_REQUEST["remove"]) || isset($_REQUEST["save"]));

$job = getStationCurrent(); // note this only works (StationJob not Station) for CIW where station:job == 1:1
if ($firstTimeThrough) // first time thru
{
    // now go get the workers
    $workerList = Worker::selectStation($job->stationid);
    // now go to the non-workers!
    $workerOutList = Worker::selectNotStation($expo->expoid, $job->stationid);
    $oddWorkerList = array(); // but unfilled
}
else // pre-save page
{
    $workerList = getSelectList();
    $oddWorkerList = $job->oddWorkerList($workerList);

    // use REQUEST as may be a GET
    if (isset($_REQUEST["save"]))
    {
        $assignment = new ShiftAssignment(); // can re-use several times
        $assignment->expoid = $expo->expoid;
        $assignment->stationid = $job->stationid;
        $assignment->jobid = $job->jobid;
        foreach ($workerList as $worker)
        {
            // save the new worker list
            // logMessage("save", $worker->lastName);
            $assignment->workerid = $worker->workerid;
            $assignment->insert();
        }
        foreach ($oddWorkerList as $worker)
        {
            // save the new worker list
            // logMessage("delete", $worker->lastName);
            $assignment->workerid = $worker->workerid;
            $assignment->delete();
        }

        // return to ExpoView
        header('Location: StationViewPage.php');
        include('StationViewPage.php');
        return;
    } // "save"

    $workerOutList = $_SESSION[PARAM_LIST];
    for ($k = count($workerOutList) - 1; $k > -1; $k--)
    {
        $worker = $workerOutList[$k];
        if (in_array($worker, $workerList))
        {
            $workerOutList[$k] = NULL; // remove if in $workerList
        }
    } // $k
    $workerOutList = array_filter($workerOutList); // remove NULLs
} // pre-save page


$paramList = array();
// should be in order for display
$optionList = array();
usort($workerList, "WorkerCompare");
$k = 0;
foreach ($workerList as $worker)
{
    $optionList[] = array($k, $worker->assignString());
    $paramList[$k] = $worker;
    $k++;
} // workerList
// should be in order for display
$optionOutList = array();
usort($workerOutList, "WorkerCompare");
foreach ($workerOutList as $worker)
{

	$shiftPreference = ShiftPreference::selectID($worker->workerid, $job->jobid);

	$workerstring = $worker->assignString();
	if ($shiftPreference->desirePercent != NULL)
	{
		$workerstring .= ' ('.$shiftPreference->desirePercent.'%)';
	}

    $optionOutList[] = array($k, $workerstring);
    $paramList[$k] = $worker;
    $k++;
} // workerOutList
// paramlist is a combination of the two; such that the posted PARAM_LIST_MULTIPLE is correct
$_SESSION[PARAM_LIST] = $paramList;


// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php include('section/LinkJob.php'); ?>

    <div id="shiftassignpage_jobview">
        <table>
            <?php
            createJobDataHTMLRows($job, "", TRUE, TRUE);
            ?>
        </table>
    </div>

    <div id="shiftassignpage_jobaction">
    	<form method="POST" action="ShiftAssignPage.php" name="shiftassignpage_form">
	    <table><tr>
	            <th style="min-width:40%">Unassigned Staff</th><th></th><th style="min-width:40%">Assigned Staff</th>
	        </tr><tr>
	        <td>
	            <select name="ignored" multiple="multiple" size="20" style="width:100%">
	            <?php
	            for ($j = 0; $j < count($optionOutList); $j++)
	            {
	                echo swwat_createOption(0, $optionOutList[$j], FALSE);
	            } // $j
	            $optionOutList = NULL;
	            ?>
	            </select>
	        </td>
	        <td>
	            <table>
	            <!-- these are javascript only -->
	            <tr><td><input style="width:100%" type="submit" name="add"    value="add >>"    onclick="sendRight()"/></td></tr>
	            <tr><td><input style="width:100%" type="submit" name="remove" value="<< remove" onclick="sendLeft()"/></td></tr>
	        	<tr><td align="center"><br /><br /><br /><input type="submit" name="save" value="Save" onclick="selectAllRight()"/></td></tr>
	            </table>
	        </td>
	        <td>
	            <?php
	            // shiftassignpage_form
	            // note PARAM_LIST_INDEX used in $_REQUEST
	            echo '<select name="', PARAM_LIST_MULTIPLE, '" multiple="multiple" size="20" style="width:100%">';
	            for ($j = 0; $j < count($optionList); $j++)
	            {
	                swwat_createOption(0, $optionList[$j], FALSE);
	            } // $j
	            echo '</select>';
	            $optionList = NULL;
	            ?>
	        </td>
	    </tr>
		</table>
	    </form>
    </div><!-- shiftassignpage -->

    <div id="shiftassignpage_confirm">
        <?php
            // display the 'current' assignments always
            $workerList = Worker::selectStation($job->stationid);
            if (count($workerList) > 0) { ?>
			<p />
            <table class="fieldValue">
            <tr class="rowTitle">
                <th>Name</th><th>Role</th><th>Station</th><th>Job</th><th>Start</th><th>Stop</th><th>Supers</th><th>Crew</th>
            </tr>
            <?php
            foreach ($workerList as $worker)
            {
                $assnArray = ShiftAssignmentView::selectWorker($expo->expoid, $worker->workerid);
                createWorkerStationHTMLRows($worker, $assnArray, TRUE, FALSE);
            } // $worker
            ?>
            </table>
        <?php } ?>
		<p />
    </div><!-- shiftassignpage_confirm -->

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
