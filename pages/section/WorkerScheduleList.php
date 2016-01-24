<?php // $Id: StationViewPage.php 819 2012-07-09 17:44:13Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/ShiftAssignmentView.php');
require_once('db/Station.php');
require_once('db/Worker.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');

function makeWorkerScheduleListHTMLRows(ShiftAssignmentView $s, $position)
{
	echo '<tr>'."\n";
	echo '<td class="fieldValueFirst"><a href="StationViewPage.php?';
	echo PARAM_LIST2_INDEX, '=', $position, '">';
	echo (htmlspecialchars($s->stationTitle)), "</a></td>\n";

	echo '<td class="fieldValue">'.htmlspecialchars($s->location).'</td>'."\n";
	$stationDateTime = swwat_format_shift($s->startTime, $s->stopTime);
	list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);
	echo '<td class="fieldValue">'.htmlspecialchars($stationDate).'</td>'."\n";
	echo '<td class="fieldValue">'.htmlspecialchars($stationTime).'</td>'."\n";
	echo '<td class="fieldValue">'.htmlspecialchars($s->instruction).'</td>'."\n";
	$expo = Expo::selectID($s->expoid);
	if ($expo->isPast() || $expo->isRunning())
	{
		$hours = ShiftStatus::WorkerStationHours($s->workerid, $s->stationid, $s->expoid);
	}
	else
	{
		$hours = "-";
	}
	echo "<td class=\"fieldValue\"><a href=\"ShiftStatusViewPage.php?";
	echo PARAM_LIST_INDEX, '=S:', $s->stationid, '">';
	echo htmlspecialchars($hours);
	echo "</a></td>\n";
	if ($s->URL != NULL)
    {
		echo '<td><iframe src="https://www.facebook.com/plugins/like.php?href='.$s->URL.'&amp;send=false&amp;layout=button_count&amp;width=80&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe></td>'."\n";
    }
	echo '</tr>'."\n";
} // makeStationListHTMLRows

/*
 * This module is executed as a function (rather than call-outs from HTML)
 * in order to pass-in the $workerList as a variable (rather than lookup some $_SESSION variable)
 *
 * It is presumed that workerList is provided in the order required.
 */
function createWorkerScheduleHTMLList(Expo $expo, Worker $worker)
{
    // set up links properly
    $shiftList = ShiftAssignmentView::selectWorker($expo->expoid, $worker->workerid);
	usort($shiftList, "ShiftAssignmentCompare");
    if (0 == count($shiftList))
    {
        echo '<tr><td class="fieldError" colspan="5">You are not currently assigned to any stations.</td></tr>';
        return;
    }

    $stationList = array();
    $position = 0;
    foreach ($shiftList as $shift)
    {
        $station = StationJob::selectID($shift->stationid);
        $stationList[] = $station;

        $shift->url = $station->URL; // todo - put this into ShiftAssignmentView as part of the shiftassignmentview
        $shift->instruction = $station->instruction; // todo - put this into ShiftAssignmentView as part of the shiftassignmentview
        makeWorkerScheduleListHTMLRows($shift, $position);
        $position += 1;
    } // $shift
    $_SESSION[PARAM_LIST2] = $stationList;
} // createStationHTMLList

?>
