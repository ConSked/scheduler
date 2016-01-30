<?php // $Id: ShiftCheckInList.php 1930 2012-09-12 21:46:54Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('db/ShiftAssignmentView.php');
require_once('db/ShiftStatus.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');


function makeShiftCheckInListHTMLRows(Worker $w, $statusType)
{
    echo "<tr>\n";
    echo "<td class=\"fieldValueFirst\">".htmlspecialchars($w->nameString())."</td>\n";
	echo "<td class=\"fieldValue\"><input type=\"submit\" name=\"".$w->workerid."\" value=\"Check In\"";
	if (!strcmp($statusType, 'CHECK_IN'))
	{
		echo " disabled=\"disabled\"";
	}
	echo "></td>\n";
	echo "<td class=\"fieldValue\"><input type=\"submit\" name=\"".$w->workerid."\" value=\"Check Out\"";
	if ($statusType == NULL || !strcmp($statusType, 'CHECK_OUT'))
	{
		echo " disabled=\"disabled\"";
	}
	echo "></td>\n";
	echo "<td class=\"fieldValue\"><input type=\"button\" name=\"".$w->workerid."\" value=\"View\" onclick=\"viewShiftStatus('".$w->workerid."')\"></td>\n";
	echo "</tr>\n";
} // makeShiftCheckInListHTMLRows

function createShiftCheckInHTMLList($expoid, $stationid)
{
    echo "<div id=\"workerlist_table\">\n";
	echo "<form method=\"POST\" name=\"ShiftCheckIn_form\" action=\"ShiftCheckInAction.php?".PARAM_LIST_INDEX."=".$stationid."\">\n";
	echo "<table>\n";

	$shiftAssignmentList = ShiftAssignmentView::selectStation($expoid, $stationid);
    $c = count($shiftAssignmentList);

	$workerList = array();
	for ($k = 0; $k < $c; $k++)
	{
		$workerList[$k] = Worker::selectID($shiftAssignmentList[$k]->workerid);
	}
	usort($workerList, "WorkerCompare");

	echo "<tr><td class=\"rowTitle\" colspan=\"4\">Supervisors</td></tr>\n";
	$supervisors = 0;
	for ($k = 0; $k < $c; $k++)
	{
		if ($workerList[$k]->isSupervisor() && !$workerList[$k]->isDisabled)
		{
			$ss = ShiftStatus::mostRecentStatus($workerList[$k]->workerid, $stationid, $expoid);
			if (count($ss) > 0)
			{
				$statusType = $ss->statusType;
			}
			else
			{
				$statusType = NULL;
			}

        	makeShiftCheckInListHTMLRows($workerList[$k], $statusType);
			$supervisors++;
		}
	}
	if ($supervisors == 0)
	{
		 echo "<tr><td class=\"fieldError\" colspan=\"4\">There are currently no Supervisors assigned to this station.</td></tr>\n";
	}

	echo "<tr><td class=\"rowTitle\" colspan=\"4\">Crew</td></tr>\n";
	$crew = 0;
    for ($k = 0; $k < $c; $k++)
    {
		if ($workerList[$k]->isCrewMember() && !$workerList[$k]->isDisabled)
		{
			$ss = ShiftStatus::mostRecentStatus($workerList[$k]->workerid, $stationid, $expoid);
			if (count($ss) > 0)
			{
				$statusType = $ss->statusType;
			}
			else
			{
				$statusType = NULL;
			}
        	makeShiftCheckInListHTMLRows($workerList[$k], $statusType);
			$crew++;
		}
    } // $k
	if ($crew == 0)
	{
		 echo "<tr><td class=\"fieldError\" colspan=\"4\">There are currently no Crew assigned to this station.</td></tr>\n";
	}

    echo "</table></form></div><!-- workerlist_table -->\n";
} // createShiftCheckInHTMLList

?>
