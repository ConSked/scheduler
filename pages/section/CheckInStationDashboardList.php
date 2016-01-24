<?php // $Id: CheckInStationDashboardList.php 2428 2003-01-07 18:56:20Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Job.php');
require_once('db/ShiftAssignmentView.php');
require_once('db/ShiftStatus.php');
require_once('db/StationJob.php');
require_once('db/Worker.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');

function makeCheckInStationDashboardStationListHTMLRows(Job $job, $k, $expoid, $max_name_size, $max_email_size)
{
	$stationDateTime = swwat_format_shift($job->startTime, $job->stopTime);
	list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);
	$dclass = preg_replace('/\s/', '_' , $stationDate);

    echo "<tr class=\"mainTitle All ".$dclass."\">\n";
	echo "<td class=\"fieldValue\"><a href=\"StationViewPage.php?";
	echo PARAM_LIST2_INDEX, "=", $k, "\">";
    echo htmlspecialchars($job->stationTitle);
    echo "</a></td>\n";
	echo "<td class=\"fieldValue\">".htmlspecialchars($job->jobTitle)."</td>\n";
	echo "<td class=\"fieldValue\">".htmlspecialchars($job->location)."</td>\n";
	echo "<td class=\"fieldValue\">".htmlspecialchars($stationDate)."</td>\n";
	echo "<td class=\"fieldValue\">".htmlspecialchars($stationTime)."</td>\n";
	echo "</tr>\n";
	echo "<tr class=\"All ".$dclass."\">\n";
	echo "<td colspan=\"5\" style='padding-left: 15px'>\n";
	echo "<table class=\"research\" width=\"100%\">\n";

	$assignedWorkerList = ShiftAssignmentView::selectStation($expoid, $job->stationid);
	$c = count($assignedWorkerList);

	$nsupervisor = 0;
	$ncrew = 0;
	if ($c > 0)
	{
		for ($k = 0; $k < $c; $k++)
		{
			$w = Worker::selectID($assignedWorkerList[$k]->workerid);
			if ($w->isSupervisor())
			{
				$nsupervisor++;
			}
			if ($w->isCrewMember())
			{
				$ncrew++;
			}
		}
	}

	$supervisor = 'Supervisors: '.$nsupervisor.' ('.$job->minSupervisor.' - '.$job->maxSupervisor.')';
	echo "<tr class=\"accordion Supervisors sectionTitle\">\n";
	echo "<td colspan=\"5\" class=\"fieldValue\">\n";
	echo "<div style=\"float:left\">".htmlspecialchars($supervisor)."</div>\n";
	echo "<div style=\"float:right\"><img id=\"icon\" src=\"".PARAM_EXPAND_ICON."\"/></div>\n";
	echo "</td>\n";
	echo "<tr>\n";

	if ($nsupervisor > 0)
	{
		for ($k = 0; $k < $c; $k++)
		{
			$w = Worker::selectID($assignedWorkerList[$k]->workerid);
			if ($w->isSupervisor())
			{
				$ss = ShiftStatus::mostRecentStatus($assignedWorkerList[$k]->workerid, $job->stationid, $expoid);
				makeCheckInStationDashboardWorkerListHTMLRows($w, $ss, $max_name_size, $max_email_size);
			}
		}
	}
	else
	{
		echo "<tr><td class=\"fieldError\" colspan=\"5\">There are currently no supervisors assigned to this station.</td></tr>\n";
	}
	echo "</table>\n";
	echo "<table class=\"research\" width=\"100%\">\n";

	$crew = 'Crew: '.$ncrew.' ('.$job->minCrew.' - '.$job->maxCrew.')';
	echo "<tr class=\"accordion Crew sectionTitle\">\n";
	echo "<td colspan=\"5\" class=\"fieldValue\">\n";
	echo "<div style=\"float:left\">".$crew."</div>\n";
	echo "<div style=\"float:right\"><img id=\"icon\" src=\"".PARAM_EXPAND_ICON."\"/></div>\n";
	echo "</td>\n";
	echo "<tr>\n";

	if ($ncrew > 0)
	{
		for ($k = 0; $k < $c; $k++)
		{
			$w = Worker::selectID($assignedWorkerList[$k]->workerid);
			if ($w->isCrewMember())
			{
				$ss = ShiftStatus::mostRecentStatus($assignedWorkerList[$k]->workerid, $job->stationid, $expoid);
				makeCheckInStationDashboardWorkerListHTMLRows($w, $ss, $max_name_size, $max_email_size);
			}
		}
	}
	else
	{
		echo "<tr><td class=\"fieldError\" colspan=\"5\">There are currently no crew assigned to this station.</td></tr>\n";
	}

	echo "</table>";
	echo "</td>\n</tr>\n";
} // makeCheckInStationDashboardListHTMLRows

function makeCheckInStationDashboardWorkerListHTMLRows(Worker $w, $ss, $max_name_size, $max_email_size)
{
	echo "<tr>\n";
	echo "<td width=\"".$max_name_size."\" class=\"fieldValueFirst\">".htmlspecialchars($w->nameString())."</td>\n";
	echo "<td width=\"".$max_email_size."\" class=\"fieldValue\">".htmlspecialchars($w->email)."</td>\n";
	echo "<td class=\"fieldValue\">".htmlspecialchars(swwat_format_phone($w->phone))."</td>\n";
	$statusType = NULL;
	if ($ss != NULL)
	{
		if (!strcmp($ss->statusType, 'CHECK_IN'))
		{
			$statusType = "<div style=\"color: red\">Checked In</div>\n";
		}
		if (!strcmp($ss->statusType, 'CHECK_OUT'))
		{
			$statusType = "<div style=\"color: green\">Checked Out</div>\n";
		}
	}
	else
	{
		$statusType = '-';
	}
	echo "<td width=\"110\" class=\"fieldValue\">".$statusType."</td>\n";
	echo "</tr>\n";
} // makeCheckInStationDashboardWorkerListHTMLRows

function createCheckInStationDashboardHTMLList($expoid)
{
	$stationList = StationJob::selectExpo($expoid);

	$dates = array();
	foreach ($stationList as $s)
	{
		$stationDateTime = explode(';', swwat_format_shift($s->startTime, $s->stopTime));
		$dates[]  = $stationDateTime[0];
	}
	$dates = array_values(array_unique($dates));

	echo "Select Date: <select id=\"".PARAM_DATE."\" name=\"".PARAM_DATE."\" onchange=\"hideRows()\">\n";
	for ($k = 0; $k < count($dates); $k++)
	{
		echo "<option value=\"".$dates[$k]."\">&nbsp;".$dates[$k]."&nbsp;</option>\n";
	}
	echo "<option value=\"All\">&nbsp;All Dates&nbsp;</option>\n";
	echo "</select>\n";
	echo "<p />\n";

	echo "<input type=\"radio\" name=\"role\" value=\"Supervisors\" onclick=\"hideRoles()\" /> Supervisors";
	echo "&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"role\" value=\"Crew\" onclick=\"hideRoles()\" /> Crew";
	echo "&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"role\" value=\"Both\" checked=\"checked\" onclick=\"hideRoles()\" /> Both\n";
	echo "<p />\n";

	$assignedWorkerList = ShiftAssignmentView::selectExpo($expoid);
	$c = count($assignedWorkerList);

	$max_name_size = 0;
	$max_email_size = 0;
	for ($k = 0; $k < $c; $k++)
	{
		$worker = Worker::selectID($assignedWorkerList[$k]->workerid);

		$name = $worker->nameString();
		if (strlen($name) > $max_name_size)
		{
			$max_name_size = strlen($name);
		}
		$email = $worker->email;
		if (strlen($email) > $max_email_size)
		{
			$max_email_size = strlen($email);
		}
	}
	$max_name_size = 10*$max_name_size;
	$max_email_size = 10*$max_email_size;
	$table_size = "75%";

	echo "<div id=\"checkinlist_table\">\n";
	echo "<table width=\"".$table_size."\">\n";
	echo "<tr class=\"mainTitle\">\n";
	echo "<td class=\"fieldValue\" colspan=\"5\" onclick=\"ExpandCollapseAll()\">\n";
	echo "<div style=\"float:right\"><div class=\"alldiv\" style=\"display:inline\">Expand All</div>&nbsp;&nbsp;&nbsp;<img id=\"allicon\" src=\"".PARAM_EXPAND_ICON."\"/></div>\n";
	echo "</td>\n</tr>\n";

	$jobList = Job::selectExpo($expoid);
	usort($jobList, "JobCompare");
	$_SESSION[PARAM_LIST2] = $jobList;

	$c = count($jobList);
	if ($c > 0)
	{
		for ($k = 0; $k < $c; $k++)
		{
			$job = $jobList[$k];
			makeCheckInStationDashboardStationListHTMLRows($job, $k, $expoid, $max_name_size, $max_email_size);
		}
	}
	else
	{
		echo "<tr><td class=\"fieldError\" colspan=\"5\">There are currently no stations assigned to this expo.</td></tr>\n";
	}

	echo "</table>\n</div>\n";
} // createCheckInStationDashboardHTMLList

?>
