<?php // $Id: ShiftStatusData.php 2435 2012-11-30 19:56:05Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('db/ShiftStatus.php');
require_once('properties/constants.php');
require_once('util/log.php');

function makeShiftStatusDataHTMLRows($sm, $k, $l, $formName, $isDisabledFlag)
{
	if (!is_null($sm))
	{
		$_POST[PARAM_STATUSID]   = htmlspecialchars($sm->shiftstatusid);
		$_POST[PARAM_STATUSDATE] = htmlspecialchars(swwat_format_isodate($sm->statusTime)); 
		$_POST[PARAM_STATUSHOUR] = htmlspecialchars(swwat_format_isotime($sm->statusTime));
		$_POST[PARAM_STATUSTYPE] = htmlspecialchars($sm->statusType);
	}
	else
	{
		$_POST[PARAM_STATUSID]   = NULL;
		$_POST[PARAM_STATUSDATE] = NULL;
		$_POST[PARAM_STATUSHOUR] = NULL;
		if ($l == 0)
		{
			$_POST[PARAM_STATUSTYPE] = "CHECK_IN";
		}
		else if ($l == 1)
		{
			$_POST[PARAM_STATUSTYPE] = "CHECK_OUT";
		}
	}

	if ($l == 0)
	{
		echo "<tr>\n";
	}
	echo "<td>\n";
	if ($isDisabledFlag)
	{
		$disabled = "disabled=\"disabled\" ";
	}
	else
	{
		$disabled = "";
	}
	echo "<input type=\"hidden\" name=\"".PARAM_STATUSID."[]\" value=\"".$_POST[PARAM_STATUSID]."\" ".$disabled."/>\n";
	echo "<input type=\"hidden\" name=\"".PARAM_STATUSDATE."[]\" value=\"".$_POST[PARAM_STATUSDATE]."\" ".$disabled."/>\n";
	echo "<input type=\"text\" name=\"".PARAM_STATUSHOUR."[]\" value=\"".$_POST[PARAM_STATUSHOUR]."\" ".$disabled."/>\n";
	echo "<input type=\"hidden\" name=\"".PARAM_STATUSTYPE."[]\" value=\"".$_POST[PARAM_STATUSTYPE]."\" ".$disabled."/>\n";
	echo "</td>\n";
	if ($l == 1)
	{
		echo "</tr>\n";
	}
} // makeShiftStatusDataHTMLRows

function createShiftStatusDataHTMLList($expoid, $stationid, $workerid, $type, $isDisabledFlag = TRUE)
{
	echo "<tr><td class=\"rowTitle\">Check In</td><td class=\"rowTitle\">Check Out</td></tr>\n";

	$shiftstatus = ShiftStatus::selectStatus($workerid, $stationid, $expoid);
	$c = count($shiftstatus);

	$n = 0;
	if ($c > 0)
	{
		for ($k = 0; $k < $c; $k++)
		{
			if (isset($shiftstatus[$k+1]))
			{
				if ($shiftstatus[$k]->statusType == "CHECK_IN" && $shiftstatus[$k+1]->statusType == "CHECK_OUT")
				{
					$shiftstatus_matrix[$n][0] = $shiftstatus[$k];
					$shiftstatus_matrix[$n][1] = $shiftstatus[$k+1];
					$k++;
					$n++;
				}
				else if ($shiftstatus[$k]->statusType == "CHECK_IN" && $shiftstatus[$k+1]->statusType == "CHECK_IN")
				{
					$shiftstatus_matrix[$n][0] = $shiftstatus[$k];
					$shiftstatus_matrix[$n][1] = NULL;
					$n++;
				}
				else if ($shiftstatus[$k]->statusType == "CHECK_OUT" && $shiftstatus[$k+1]->statusType == "CHECK_OUT")
				{
					$shiftstatus_matrix[$n][0] = NULL;
					$shiftstatus_matrix[$n][1] = $shiftstatus[$k];
					$n++;
				}
			}
			else
			{
				if ($shiftstatus[$k]->statusType == "CHECK_IN")
				{
					$shiftstatus_matrix[$n][0] = $shiftstatus[$k];
					$shiftstatus_matrix[$n][1] = NULL;
					$n++;
				}
				else if ($shiftstatus[$k]->statusType == "CHECK_OUT")
				{
					$shiftstatus_matrix[$n][0] = NULL;
					$shiftstatus_matrix[$n][1] = $shiftstatus[$k];
					$n++;
				}
			}
		}
	}

	if (isset($shiftstatus_matrix))
	{
		$c = count($shiftstatus_matrix);

		if ($c > 0)
		{
			for ($k = 0; $k < $c; $k++)
			{
				for ($l = 0; $l < 2; $l++)
				{
					makeShiftStatusDataHTMLRows($shiftstatus_matrix[$k][$l], $k, $l, "shiftstatuseditpage_shiftstatusdata", $isDisabledFlag);
				}
			}
		}
		echo "<tr>";
		echo "<td><input class=\"fieldValue\" type=\"Submit\" value=\"".$type."\"/>";
		if (!strcmp($type, 'Save'))
		{
			$_POST[PARAM_SAVE]   = htmlspecialchars($type);
			echo "<input type=\"hidden\" name=\"".PARAM_SAVE."\" value=\"".$_POST[PARAM_SAVE]."\"/>";
		}
		echo "</td>";
		echo "</tr>\n";
	}
	else
	{
		echo "<tr><td colspan=\"2\" class=\"fieldError\">There are no check-ins or check-outs</td></tr>\n";
		echo "<tr>";
		echo "<td><input class=\"fieldValue\" type=\"Submit\" value=\"".$type."\" disabled=\"disabled\"/></td>";
		echo "</tr>\n";
	}
} // createShiftStatusDataHTMLList

?>
