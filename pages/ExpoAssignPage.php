<?php // $Id: ExpoAssignPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/ShiftAssignmentView.php');
require_once('db/StationJob.php');
require_once('db/Worker.php');
require_once('section/ExpoData.php');
require_once('section/Menu.php');
require_once('section/WorkerStation.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');
require_once('util/session.php');

$author = getWorkerAuthenticated();


$expo = getExpoCurrent();
// must have clicked a button to be 2nd time through
$firstTimeThrough = !(isset($_REQUEST["add"]) || isset($_REQUEST["remove"]));

if ($firstTimeThrough) // first time thru
{
    // now go get the workers
    $workerList = Worker::selectExpo($expo->expoid);
    // now go to the non-workers!
    $workerOutList = Worker::selectNotExpo($expo->expoid);
    $oddWorkerList = array(); // but unfilled
}
else // pre-save page
{
    $workerList = getSelectList();
    $oddWorkerList = $expo->oddWorkerList($workerList);

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
    $optionOutList[] = array($k, $worker->assignString());
    $paramList[$k] = $worker;
    $k++;
} // workerOutList
// paramlist is a combination of the two; such that the posted PARAM_LIST_MULTIPLE is correct
$_SESSION[PARAM_LIST] = $paramList;
$_SESSION['workerList'] = $workerList;

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Expo Assign Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">

    <script type="text/javascript">
        function confirmAssignments(cntRemoved)
		{
			if (cntRemoved > 0)
			{
				window.location.href = "ExpoAssignConfirmPage.php";
			}
			else
			{
				window.location.href = "ExpoAssignAction.php";
			}
		}

        function selectAllRight()
        {
            var select = document.expoassignpage_form.elements['list_index[]'];
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
            move(document.expoassignpage_form.ignored,
                 document.expoassignpage_form.elements['list_index[]']);
        } // sendRight
        function sendLeft()
        {
            // note we presume PARAM_LIST_MULTIPLE == 'list_index[]'
            move(document.expoassignpage_form.elements['list_index[]'],
                 document.expoassignpage_form.ignored);
        } // sendLeft
    </script>
</head>

<body>
<div id="container">

<?php
// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php include('section/LinkExpo.php'); ?>

    <div id="expoassignpage">
    	<form method="POST" action="ExpoAssignPage.php" name="expoassignpage_form">
	    <table><tr>
	            <th style="min-width:40%">Unassigned Staff</th><th></th><th style="min-width:40%">Assigned Staff</th>
	        </tr><tr>
	        <td>
	            <select name="ignored" multiple="multiple" size="20" style="width:100%">
	            <?php
	            for ($j = 0; $j < count($optionOutList); $j++)
	            {
	                swwat_createOption($optionOutList[$j], FALSE);
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
	            <tr><td align="center"><br /><br /><br /><input type="button" name="save" value="Save" onclick="confirmAssignments(<?php echo(count($oddWorkerList)); ?>)"/></td></tr>
	            </table>
	        </td>
	        <td>
	            <?php
	            // expoassignpage_form
	            // note PARAM_LIST_INDEX used in $_REQUEST
	            echo '<select name="', PARAM_LIST_MULTIPLE, '" multiple="multiple" size="20" style="width:100%">';
	            for ($j = 0; $j < count($optionList); $j++)
	            {
	                swwat_createOption($optionList[$j], FALSE);
	            } // $j
	            echo '</select>';
	            $optionList = NULL;
	            ?>
	        </td>
	    </tr>
        </table>
	    </form>
    </div><!-- expoassignpage -->

    <div id="expoassignpage_confirm">
        <?php if (count($oddWorkerList) > 0) { ?>
			<br/>
            <table class="fieldValue">
			<tr><td align="center" colspan="8" style="font-weight: bold">Shifts of staff to be removed</td></tr>
            <tr class="rowTitle">
                <th>Name</th><th>Role</th><th>Station</th><th>Job</th><th>Start</th><th>Stop</th><th>Supers</th><th>Crew</th>
            </tr>
            <?php
			$cnt = 0;
            foreach ($oddWorkerList as $worker)
            {
                $assnArray = ShiftAssignmentView::selectWorker($expo->expoid, $worker->workerid);
                createWorkerStationHTMLRows($worker, $assnArray, TRUE, FALSE);
				$cnt += count($assnArray);
            } // $worker
			if ($cnt == 0)
			{
				echo "<tr><td align=\"center\" colspan=\"8\"><span class=\"fieldError\">None</span></td></tr>";
			}
            ?>
            </table>
        <?php } ?>
    </div><!-- expoassignpage_confirm -->

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
