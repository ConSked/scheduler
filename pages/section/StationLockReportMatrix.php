<?php // $Id: StationLockReportMatrix.php 1751 2012-09-06 20:47:58Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('db/StationJob.php');
require_once('db/ShiftAssignmentView.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');

function createStationLockHTMLMatrix($expoid, $workerid)
{
	$station = StationJob::selectExpo($expoid);

	// make titles
	$htitle = array();
	$vtitle = array();
	foreach ($station as $s)
	{
		$htitle[] = $s->location." (".$s->title.")";
		$stationDateTime = explode(';', swwat_format_shift($s->startTime, $s->stopTime));
		$vtitle[] = $stationDateTime[0]." (".$stationDateTime[1].")";
	}
	$htitle = array_values(array_unique($htitle));
	$vtitle   = array_values(array_unique($vtitle));

	// initialize matrix
	for ($h = 0; $h <= count($htitle); $h++)
	{
		for ($v = 0; $v <= count($vtitle); $v++)
		{
			$matrix[$h][$v] = '<td align="center"></td>';
		}
	}

	//add horizontal titles to matrix
	for ($h = 0; $h < count($htitle); $h++)
	{
		$matrix[$h+1][0] = '<td class="rowTitle">'.$htitle[$h].'</td>';
	}

	//add vertical titles
	for ($v = 0; $v < count($vtitle); $v++)
	{
		$matrix[0][$v+1] = '<td class="rowTitle">'.$vtitle[$v].'</td>';
	}

	// fill assignment data to matrix
	foreach ($station as $s)
	{
		$hvalue = $s->location." (".$s->title.")";
		$hpos   = array_search($hvalue, $htitle);

		$stationDateTime = explode(';', swwat_format_shift($s->startTime, $s->stopTime));
		$vvalue = $stationDateTime[0]." (".$stationDateTime[1].")";
		$vpos   = array_search($vvalue, $vtitle);

		if (ShiftAssignmentView::isWorkerAssignedStation($workerid, $expoid, $s->stationid))
		{
			$matrix[$hpos+1][$vpos+1] = '<td align="center">X</td>';
		}
		else
		{
			$matrix[$hpos+1][$vpos+1] = '<td align="center">-</td>';
		}
	}

	// make table
	echo '<table>';
	for ($v = 0; $v <= count($vtitle); $v++)
	{
		echo '<tr>';
		for ($h = 0; $h <= count($htitle); $h++)
		{
			echo $matrix[$h][$v];
		}
		echo '</tr>';
	}
	echo '</table>';
	echo '<p />';
} // createStationLockHTMLMatrix

?>
