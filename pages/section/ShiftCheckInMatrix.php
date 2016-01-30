<?php // $Id: ShiftCheckInMatrix.php 1706 2012-09-05 01:51:53Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/StationJob.php');
require_once('util/log.php');

function createShiftCheckInHTMLMatrix($expoid)
{
	$station = StationJob::selectExpo($expoid);

	// make titles
	$htitle = array();
	$date = array();
	$vtitle = array();
	$dates = array();
	foreach ($station as $s)
	{
		$stationDateTime = explode(';', swwat_format_shift($s->startTime, $s->stopTime));
		$htitle[] = $stationDateTime[0]." (".$stationDateTime[1].")";
		$date[]  = $stationDateTime[0];
		$vtitle[] = $s->location." (".$s->title.")";
	}
	$htitle = array_values(array_unique($htitle));
	$vtitle = array_values(array_unique($vtitle));
	$dates = array_values(array_unique($date));

	echo "Select Date: <select id=\"".PARAM_DATE."\" name=\"".PARAM_DATE."\" onchange=\"hideColumns()\">\n";
	for ($k = 0; $k < count($dates); $k++)
	{
		echo "<option value=\"".$dates[$k]."\">&nbsp;".$dates[$k]."&nbsp;</option>\n";
	}
	echo "<option value=\"All\">&nbsp;All Dates&nbsp;</option>\n";
	echo "</select>\n";
	echo "<p />";

	// get class names
	$hdate = array();
	for ($h = 0; $h < count($htitle); $h++)
	{
		for ($d = 0; $d < count($dates); $d++)
		{
			if (preg_match("/".$dates[$d]."/", $htitle[$h]))
			{
				$hdate[$h] = preg_replace('/\s/', '_', $dates[$d]);
			}
		}
	}

	// initialize matrix
	for ($h = 0; $h <= count($htitle); $h++)
	{
		for ($v = 0; $v <= count($vtitle); $v++)
		{
			if ($h == 0 && $v == 0)
			{
				$matrix[$h][$v] = "<td align=\"center\"></td>\n";
			}
			else
			{
				if ($h == 0)
				{
					$matrix[$h][$v] = "<td align=\"center\">-</td>\n";
				}
				else
				{
					$matrix[$h][$v] = "<td align=\"center\" class=\"".$hdate[$h-1]."\">-</td>\n";
				}
			}
		}
	}

	//add horizontal titles to matrix
	for ($h = 0; $h < count($htitle); $h++)
	{
		$matrix[$h+1][0] = "<td class=\"rowTitle ".$hdate[$h]."\">".$htitle[$h]."</td>\n";
	}

	//add vertical titles
	for ($v = 0; $v < count($vtitle); $v++)
	{
		$matrix[0][$v+1] = "<td class=\"rowTitle\">".$vtitle[$v]."</td>\n";
	}

	// fill assignment data to matrix
	foreach ($station as $s)
	{
		$stationDateTime = explode(';', swwat_format_shift($s->startTime, $s->stopTime));
		$hvalue = $stationDateTime[0]." (".$stationDateTime[1].")";
		$hpos   = array_search($hvalue, $htitle);

		$vvalue = $s->location." (".$s->title.")";
		$vpos   = array_search($vvalue, $vtitle);

		$matrix[$hpos+1][$vpos+1] = "<td align=\"center\" class=\"".$hdate[$hpos]."\">";
		$matrix[$hpos+1][$vpos+1] .= "<a href=\"ShiftCheckInPage.php?".PARAM_LIST_INDEX."=".$s->stationid."\">";
		$matrix[$hpos+1][$vpos+1] .= "Super: ".$s->assignedSupervisor."; Crew: ".$s->assignedCrew."</a></td>\n";
	}

	// determine pattern array
	for ($d = 0; $d < count($dates); $d++)
	{
		$darray[$d] = array();
		for ($h = 1; $h <= count($htitle); $h++)
		{
			if (!strpos($matrix[$h][0], $dates[$d]))
			{
				$darray[$d][] = $h;
			}
		}
	}

	// find row class
	for ($v = 0; $v <= count($vtitle); $v++)
	{
		$vdate[$v] = NULL;
		for ($d = 0; $d < count($dates); $d++)
		{
			$flag = true;
			foreach ($darray[$d] as $dvalue)
			{
				if (!strpos($matrix[$dvalue][$v], '>-<'))
				{
					$flag = false;
				}
			}

			if ($flag)
			{
				$vdate[$v] = preg_replace('/\s/', '_', $dates[$d]);
			}
		}
	}

	echo "<table>\n";
	for ($v = 0; $v <= count($vtitle); $v++)
	{
		if (!is_null($vdate[$v]))
		{
			echo "<tr class=\"".$vdate[$v]."\">\n";
		}
		else
		{
			echo "<tr>\n";
		}
		for ($h = 0; $h <= count($htitle); $h++)
		{
			echo $matrix[$h][$v];
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "<p />\n";

} // createShiftCheckInHTMLMatrix

?>
