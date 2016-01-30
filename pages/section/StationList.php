<?php // $Id: StationList.php 2402 2012-10-22 14:35:45Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

// DO NOT PUT PREFERENCE STUFF IN THIS FILE

require_once('properties/constants.php');
require_once('db/StationJob.php');
require_once('db/Job.php');
require_once('db/JobTitle.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/parse.php');
require_once('util/log.php');
require_once('util/date.php');

function makeStationListHTMLRows(StationJob $s, $position)
{
	$stationDateTime = swwat_format_shift($s->startTime, $s->stopTime);
	list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);
	$dclass = preg_replace('/\s/', '_', $stationDate);

	$j = Job::selectStation($s->expoid, $s->stationid);
	$djob = preg_replace('/\s/', '_', $j[0]->jobTitle);
	$djob = preg_replace('/\//', '_', $djob);

	echo "<tr class=\"All ".$dclass." ".$djob."\">\n";
	echo "<td class=\"fieldValueFirst\">\n";
	echo "<a href=\"StationViewPage.php?".PARAM_LIST2_INDEX."=".$position."\">".htmlspecialchars($s->title)."</a>\n";
	echo "</td>\n";
 	echo "<td class=\"fieldValue\">".htmlspecialchars($j[0]->jobTitle)."</td>\n";
 	echo "<td class=\"fieldValue\">".htmlspecialchars($s->location)."</td>\n";
	echo "<td class=\"fieldValue\">".htmlspecialchars($stationDate)."</td>\n";
	echo "<td class=\"fieldValue\">".htmlspecialchars($stationTime)."</td>\n";
	$crew = $s->minCrew.' - '.$s->maxCrew;
 	echo "<td class=\"fieldValue\">".htmlspecialchars($crew)."</td>\n";
	$supervisor = $s->minSupervisor.' - '.$s->maxSupervisor;
	echo "<td class=\"fieldValue\">".htmlspecialchars($supervisor)."</td>\n";
	echo "<td class=\"fieldValue\">".htmlspecialchars($s->instruction)."</td>\n";
	echo "</tr>\n";
} // makeStationListHTMLRows

function createStationHTMLList($expo, array $stationList)
{
	$jobList = Job::selectExpo($expo->expoid);
	usort($jobList, "JobCompare");

	$date = array();
	foreach ($jobList as $j)
	{
		$stationDateTime = swwat_format_shift($j->startTime, $j->stopTime);
		list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);

		$date[] = $stationDate;
	}
	$date = array_values(array_unique($date));

	echo "<table width=\"50%\">\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "Select Date: <select id=\"".PARAM_DATE."\" name=\"".PARAM_DATE."\" onchange=\"hideDateRows()\">\n";
	for ($k = 0; $k < count($date); $k++)
	{
		echo "<option value=\"".$date[$k]."\">&nbsp;".$date[$k]."&nbsp;</option>\n";
	}
	echo "<option value=\"All\">&nbsp;All Dates&nbsp;</option>\n";
	echo "</select>\n";
	echo "</td>\n";

	$jobTitle = JobTitle::titleEnums($expo->expoid);

	echo "<td>\n";
	echo "Select Job: <select id=\"".PARAM_JOB."\" name=\"".PARAM_JOB."\" onchange=\"hideJobRows()\">\n";
	for ($k = 0; $k < count($jobTitle); $k++)
	{
		echo "<option value=\"".$jobTitle[$k]."\">&nbsp;".$jobTitle[$k]."&nbsp;</option>\n";
	}
	echo "<option value=\"All\" selected=\"selected\">&nbsp;All Jobs&nbsp;</option>\n";
	echo "</select>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<p />Search Shift By: <input type=\"text\" id=\"search\" name=\"search\" onkeyup=\"searchRows()\"\>";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<p />\n";

    echo "<div id=\"stationlist_table\">\n";
	echo "<table>\n";
	echo "<tr>\n";
	echo "<th class=\"rowTitle\">Shift</th>\n";
	echo "<th class=\"rowTitle\">Job</th>\n";
	echo "<th class=\"rowTitle\">Location</th>\n";
	echo "<th class=\"rowTitle\">Date</th>\n";
	echo "<th class=\"rowTitle\">Time</th>\n";
	echo "<th class=\"rowTitle\">Crew</th>\n";
	echo "<th class=\"rowTitle\">Supervisors</th>\n";
	echo "<th class=\"rowTitle\">Instructions</th>\n";
	echo "</tr>\n";

	$c = count($stationList);
	if ($c > 0)
	{
		for ($k = 0; $k < $c; $k++)
		{
			$s = $stationList[$k];

			makeStationListHTMLRows($s, $k);
		}
	}
	else
	{
		echo "<tr><td class=\"fieldError\" colspan=\"5\">No stations for this Expo currently exist.</td></tr>\n";
	}

	echo "</table></div><!-- stationlist_table -->\n";
} // createStationHTMLList

// DO NOT PUT PREFERENCE STUFF IN THIS FILE

?>
