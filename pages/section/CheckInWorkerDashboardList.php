<?php // $Id: CheckInWorkerDashboardList.php 1930 2012-09-12 21:46:54Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/ShiftStatus.php');
require_once('db/StationJob.php');
require_once('db/Worker.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');

function makeCheckInWorkerDashboardListHTMLRows2(Worker $w, $expoid)
{
	echo "<tr>\n";
	echo "<td class=\"fieldValueFirst\">".htmlspecialchars($w->nameString())."</td>\n";
	echo "<td class=\"fieldValue\">".htmlspecialchars($w->email)."</td>\n";
	echo "<td class=\"fieldValue\">".swwat_format_phone($w->phone)."</td>\n";
	$ss = ShiftStatus::mostRecentStatusWorker($w->workerid, $expoid);
	echo "<td class=\"fieldValue\">";
	if (!is_null($ss))
	{
		$s = StationJob::selectID($ss->stationid);
		echo $s->location." (".$s->title.")";
	}
	else
	{
		echo "-";
	}
	echo "</td>\n";
	echo "<td class=\"fieldValue\">";
	if (!is_null($ss))
	{
		if ($ss->statusType == "CHECK_IN")
		{
			echo "<div style=\"color: red\">Checked In</div>";
		}
		else if ($ss->statusType == "CHECK_OUT")
		{
			echo "<div style=\"color: green\">Checked Out</div>";
		}
	}
	else
	{
		echo "Never checked in";
	}
	echo "</td>\n";
	echo "<td class=\"fieldValue\">";
	if (!is_null($ss))
	{
		$hours = ShiftStatus::WorkerHours($w->workerid, $expoid);
		if (is_int($hours))
		{
			echo $hours;
		}
		else
		{
			echo sprintf('%.2f', $hours);
		}
	}
	else
	{
		echo "-";
	}
	echo "</td>\n";

	echo "</tr>\n";
} // makeCheckInWorkerDashboardListHTMLRows2

function createCheckInWorkerDashboardHTMLList($expoid)
{
	$numColumns = 6;
	$workerList = Worker::selectExpo($expoid);
	$c = count($workerList);

	echo "<div id=\"workerlist_table\"><table>\n";
	echo "<tr>\n";
	echo "<th class='rowTitle'>Name</th>\n";
	echo "  <th class='rowTitle'>Email</th>\n";
	echo "  <th class='rowTitle'>Phone</th>\n";
	echo "  <th class='rowTitle'>Most Recent Shift</th>\n";
	echo "  <th class='rowTitle'>Status</th>\n";
	echo "  <th class='rowTitle'>Hours</th>\n";
	echo "</tr>\n";

	echo "<tr class=\"rowTitle\"><td colspan=\"".$numColumns."\">Supervisors</td></tr>\n";
	$supervisors = 0;
	for ($k = 0; $k < $c; $k++)
	{
		$w = $workerList[$k];
		if ($w->isDisabled)  {  continue;  } // skip to next
		if (FALSE == ($w->isSupervisor()))  {  continue;  } // skip to next
		// else
		makeCheckInWorkerDashboardListHTMLRows2($w, $expoid);
		$supervisors++;
	}
	if ($supervisors == 0)
	{
		echo "<tr><td class=\"fieldError\" colspan=\"".$numColumns."\">No Supervisors assigned to this expo.</td></tr>\n";
	}

	echo "<tr class=\"rowTitle\"><td colspan=\"".$numColumns."\">Crew</td></tr>\n";
	$crew = 0;
	for ($k = 0; $k < $c; $k++)
	{
		$w = $workerList[$k];
		if ($w->isDisabled)  {  continue;  } // skip to next
		if (FALSE == ($w->isCrewMember()))  {  continue;  } // skip to next
		// else
		makeCheckInWorkerDashboardListHTMLRows2($w, $expoid);
		$crew++;
	}
	if ($crew == 0)
	{
		echo "<tr><td class=\"fieldError\" colspan=\"".$numColumns."\">No Crew assigned to this expo.</td></tr>\n";
	}

	echo "<tr class=\"rowTitle\"><td colspan=\"".$numColumns."\">Organizers</td></tr>\n";
	$organizers = 0;
	for ($k = 0; $k < $c; $k++)
	{
		$w = $workerList[$k];
		if ($w->isDisabled)  {  break;  } // skip to next
		if (FALSE == ($w->isOrganizer()))  {  continue;  } // skip to next
		// else
		makeCheckInWorkerDashboardListHTMLRows2($w, $expoid);
		$organizers++;
	}
	if ($organizers == 0)
	{
		echo "<tr><td class=\"fieldError\" colspan=\"".$numColumns."\">No Organizers assinged to this expo.</td></tr>\n";
	}

	echo "</table></div><!-- workerlist_table -->\n";
} // createCheckInWorkerDashboardHTMLList
?>
