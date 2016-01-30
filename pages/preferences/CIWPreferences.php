<?php // $Id: CIWPreferences.php 2419 2012-10-29 18:01:08Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('db/Job.php');
require_once('db/ShiftAssignment.php');
require_once('db/ShiftPreference.php');
require_once('db/Station.php');
require_once('properties/constants.php');
require_once('schedule/FirstComeFirstServed.php');
require_once('swwat/gizmos/parse.php');
require_once('util/log.php');
require_once('util/mailSchedule.php');

function wizardPageJavascript()
{
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.research').find('td.title').click(function() {
				$(this).parent().next().toggle();

				if ($(this).find("#icon").attr("src") == "<?php echo(PARAM_COLLAPSE_ICON); ?>")
				{
					$(this).find("#icon").attr("src", "<?php echo(PARAM_EXPAND_ICON); ?>");
				}
				else
				{
					$(this).find("#icon").attr("src", "<?php echo(PARAM_COLLAPSE_ICON); ?>");
				}
			});
		});

		function init()
		{
			hideRows();
		}

		function hideRows()
		{
			$(document).ready(function() {
				$('#<?php echo PARAM_DATE;?> option').each(function() {
					var dates = this.value.replace(" ", "_");

					if (this.value != 'All')
					{
						if (this.selected == true)
						{
							$('.'+dates+'').show();
						}
						else
						{
							$('.'+dates+'').hide();
						}
					}
					else
					{
						if (this.selected == true)
						{
							$('.All').show();
						}
					}
				});
			});
			$('.description').hide();
		}

		function sortRows()
		{
			var frm = eval("document.pref_station_sort_form");
			var url = "PreferenceStationPage.php";

			frm.action = url;
			frm.submit();
		}

		function searchRows()
		{
			$(document).ready(function() {
				var val = $('#search').val();
				$('.description').each(function() {
					var text = $(this).text();
					if (text.search(val) != -1)
					{
						$(this).show();
						$(this).prev().show();
					}
					else
					{
						$(this).hide();
						$(this).prev().hide();
					}
				});
			});
		}
	</script>
<?php
}

function reviewPageJavascript()
{
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.research').find('tr').click(function() {
				var rows = $(this).text().replace(/[\n\r]/g, '').replace(' ', '_').split(',')[0];

				if (rows.search("No"))
				{
					if ($(this).find("#icon1").attr("src") == "<?php echo(PARAM_COLLAPSE_ICON); ?>")
					{
						$('.'+rows+'').fadeOut();
						$(this).find("#icon1").attr("src", "<?php echo(PARAM_EXPAND_ICON); ?>");
					}
					else
					{
						$('.'+rows+'').fadeIn();
						$('.description.'+rows+'').hide();
						$(this).find("#icon1").attr("src", "<?php echo(PARAM_COLLAPSE_ICON); ?>");
					}
				}
				else
				{
					var rows = $(this).find('input').attr('name');

					$('.'+rows+'').fadeToggle();
					if ($(this).find("#icon2").attr("src") == "<?php echo(PARAM_COLLAPSE_ICON); ?>")
					{
						$(this).find("#icon2").attr("src", "<?php echo(PARAM_EXPAND_ICON); ?>");
					}
					else
					{
						$(this).find("#icon2").attr("src", "<?php echo(PARAM_COLLAPSE_ICON); ?>");
					}
				}
			});
		});

		function init()
		{
			$(".research tr:not(.accordion)").show();
			$(".description").hide();
		}
	</script>
<?php
}

function navigationLocation($pageNum)
{
	$color = array('lightgray', 'lightgray', 'lightgray');
	$color[$pageNum-1] = 'cyan';

	echo "<div id=\"navi_loc\" style=\"float:right; margin-right: 5px\">\n";
	echo "<table border cellspacing=\"0\" cellmargin=\"0\">\n";
	echo "    <tr>\n";
	echo "        <td style=\"padding-left: 5px; padding-right: 5px; background-color: ".$color[0]."\">1</td>\n";
	echo "        <td style=\"padding-left: 5px; padding-right: 5px; background-color: ".$color[1]."\">2</td>\n";
	echo "        <td style=\"padding-left: 5px; padding-right: 5px; background-color: ".$color[2]."\">3</td>\n";
	echo "    </tr>\n";
	echo "</table>\n";
	echo "<p />\n";
	echo "</div>\n";
} // navigationLocation

function welcomePageTitle($expoTitle, $pageNum)
{
	echo "<div align=\"center\"><h3>Welcome to the $expoTitle Volunteer scheduler!</h3></div>\n";
	navigationLocation($pageNum);
} // welcomePageTitle

function wizardPageTitle($expoTitle, $pageNum)
{
	echo "<div align=\"center\"><h3>Select the Shifts that Interest You</h3></div>\n";
	navigationLocation($pageNum);
}

function reviewPageTitle($expoTitle, $pageNum)
{
	echo "<div align=\"center\"><h3>Thank you for using the $expoTitle Volunteer scheduler!</h3></div>\n";
	navigationLocation($pageNum);
}

function welcomePageContent()
{
	echo "<form method=\"POST\" name=\"preferencewelcome_form\" action=\"PreferenceWizardPage.php\">\n";
	echo "\n";
	echo "<div id=\"content\" style=\"clear:both\">\n";
	echo "Over the next few pages, you will be guided through the process of selecting talk, megatalk, lab and headquarter shifts you wish to volunteer for.\n";
	echo "<p />\n";
	echo "Feel free to request as many shifts as you would like; you will have an opportunity to confirm or remove choices before submitting your request on the final page.\n";
	echo "<p />\n";
	echo "You must set the maximum number of hours that you are able to commit before confirming your schedule.\n";
	echo "<p />\n";
	echo "If you don&apos;t have preferences for where or when you volunteer, select all of the shifts for which you can commit, and then specify your maximum time commitment. We will schedule you where you are of most help to the team!\n";
	echo "<p />\n";
	echo "The scheduler will assign shifts based on the maximum number of hours for which you can commit, your availability, your need, and your preferences.  You will not be assigned to any shifts you did not request.\n";
	echo "<p />\n";
	echo "Should you have any questions, there&apos;s a link to the right for email support.\n";
	echo "<p />\n";
	echo "</div><!-- content -->\n";
}

function wizardPageContent($author, $expo)
{
	echo "<div id=\"content\" style=\"clear:both\">\n";
	echo "Below are all of the volunteer shifts available. Select the YES radio button forall of the shifts that interest you and to which you can commit!\n";
	echo "<p />\n";
	echo "Click on each day to see available volunteer shifts for that day. Drill down further by clicking on an event to see a description of that event. You&apos;ll have the next page to review what you selected!\n";
	echo "<p />\n";
	echo "Click next at the bottom of the page once you have selected your favorites.\n";
	echo "</div><!-- content -->\n";
	echo "<form method=\"POST\" name=\"preferencewizard_sort_form\">\n";

	$jobIncludeList = array();
	$jobIncludeList[] = "Volunteer";

	$jobList = Job::selectExpo($expo->expoid);
	usort($jobList, "JobCompare");

	if (isset($_POST['sort']) && $_POST['sort'] == 'need')
	{
		usort($jobList, "JobCompareNeed");
	}

	$date = array();
	foreach ($jobList as $j)
	{
		$stationDateTime = swwat_format_shift($j->startTime, $j->stopTime);
		list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);

		$date[] = $stationDate;
	}
	$date = array_values(array_unique($date));

	echo "<table width=\"100%\">\n";
	echo "<tr>\n";
	echo "<td colspan=\"2\">\n";
	echo "Sort By: <select id=\"sort\" name=\"sort\" onchange=\"sortRows()\">\n";
	echo "<option value=\"date\"";
	if (!isset($_POST['sort']) || isset($_POST['sort']) && $_POST['sort'] == 'date')
	{
		echo " selected=\"selected\"";
	}
	echo ">&nbsp;Date&nbsp;</option>\n";
	echo "<option value=\"need\"";
	if (isset($_POST['sort']) && $_POST['sort'] == 'need')
	{
		echo "selected=\"selected\"";
	}
	echo ">&nbsp;Need&nbsp;</option>\n";
	echo "</select>\n";
	echo "<p />\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "Select Date: <select id=\"".PARAM_DATE."\" name=\"".PARAM_DATE."\" onchange=\"hideRows()\">\n";
	for ($k = 0; $k < count($date); $k++)
	{
		echo "<option value=\"".$date[$k]."\">&nbsp;".$date[$k]."&nbsp;</option>\n";
	}
	echo "<option value=\"All\">&nbsp;All Dates&nbsp;</option>\n";
	echo "</select>\n";
	echo "</td>\n";
	echo "<td>\n";
	echo "Search By: <input type=\"text\" id=\"search\" name=\"search\" onkeyup=\"searchRows()\"\>";
	echo "&nbsp(Description text) Click on buttons below to display the description.";
	echo "<td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<p />\n";
	echo "</form>\n";
	echo "<form method=\"POST\" name=\"preferencewizard_form\" action=\"PreferenceWizardAction.php\">\n";
	echo "\n";

	$max_radio_size = "10%";
	$max_percent_size = "5%";
	$max_date_size = "6%";
	$max_time_size = "12%";
	$max_hours_size = "5%";
	$max_location_size = "20%";
	$max_title_size = 100 - ($max_radio_size + $max_percent_size + $max_date_size + $max_time_size + $max_hours_size + $max_location_size)."%";
	$table_size = "95%";

	$numColumns = 7;

	echo "<table width=\"".$table_size."\" align=\"center\" class=\"research\">\n";
	echo "<tr>\n";
	echo "<th width=\"".$max_radio_size."\" class=\"rowTitle\"></th>";
	echo "<th width=\"".$max_percent_size."\" class=\"rowTitle\">Percent Filled</th>";
	echo "<th width=\"".$max_date_size."\" class=\"rowTitle\">Date</th>";
	echo "<th width=\"".$max_time_size."\" class=\"rowTitle\">Time</th>";
	echo "<th width=\"".$max_hours_size."\" class=\"rowTitle\">Hours</th>";
	echo "<th width=\"".$max_location_size."\" class=\"rowTitle\">Location</th>";
	echo "<th width=\"".$max_title_size."\" class=\"rowTitle\">Title</th>";
	echo "</tr>\n";

	for ($l = 0; $l < count($jobList); $l++)
	{
		$param_station = PARAM_TITLE . $l;

		if (!isset($_POST[$param_station]))
		{
			$_POST[$param_station] = NULL;
		}

		$sp = ShiftPreference::selectID($author->workerid, $jobList[$l]->jobid);
		if (!is_null($sp))
		{
			$desire = $sp->desirePercent;
		}
		else
		{
			$desire = 0;
		}

		$stationDateTime = swwat_format_shift($jobList[$l]->startTime, $jobList[$l]->stopTime);
		list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);

		$start = $jobList[$l]->startTime;
		$stop = $jobList[$l]->stopTime;
		$diff = $start->diff($stop);
		$hours = ($diff->d)*24 + $diff->h + ($diff->i)/60 + ($diff->s)/360;

		if ($jobList[$l]->minCrew != 0)
		{
			$percent = intval(100*($jobList[$l]->assignedCrew / $jobList[$l]->minCrew))."%";
		}
		else
		{
			$percent = "-";
		}

		$s = Station::selectID($jobList[$l]->stationid);

		$dclass = preg_replace('/\s/', '_', $stationDate);
		if (in_array($jobList[$l]->jobTitle, $jobIncludeList))
		{
			echo "<tr class=\"All ".$dclass."\">\n";
			echo "<td class=\"fieldValue\">\n";
			echo "<input id=\"".$param_station."n\" name=\"".$param_station."\" type=\"radio\" value=\"".$jobList[$l]->jobid.":0"."\" ";
			if ($desire == 0)
			{
				echo "checked=\"checked\" ";
			}
			echo "/>";
			echo "<label for=\"".$param_station."n\">No&nbsp;&nbsp;</label>\n";
			echo "<input id=\"".$param_station."y\" name=\"".$param_station."\" type=\"radio\" value=\"".$jobList[$l]->jobid.":100"."\" ";
			if ($desire != 0)
			{
				echo "checked=\"checked\" ";
			}
			echo "/>";
			echo "<label for=\"".$param_station."y\">Yes</label>\n";
			echo "</td>\n";
			echo "<td class=\"fieldValue\">".htmlspecialchars($percent)."</td>\n";
			echo "<td class=\"fieldValue\">".htmlspecialchars($stationDate)."</td>\n";
			echo "<td class=\"fieldValue\">".htmlspecialchars($stationTime)."</td>\n";
			echo "<td class=\"fieldValue\">".htmlspecialchars($hours)."</td>\n";
			echo "<td class=\"fieldValue\">".htmlspecialchars($jobList[$l]->location)."</td>\n";
			echo "<td class=\"title fieldValue\">";
			echo "<div style=\"float:left\">".htmlspecialchars($jobList[$l]->stationTitle)."</div>\n";
			echo "<div style=\"float:right\"><img id=\"icon\" src=\"".PARAM_EXPAND_ICON."\"/></div>\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "<tr class=\"description All ".$dclass."\">\n";
			echo "<td class=\"fieldValue\" style=\"text-align: center\" colspan=\"".$numColumns."\">";
			echo htmlspecialchars($s->description)."<br><a href=\"".htmlspecialchars($s->URL)."\">".htmlspecialchars($s->URL)."</a></td>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
	}

	echo "</table>\n";
	echo "<p />\n";
}

function wizardActionContent($author, $expo)
{
	$jobList = Job::selectExpo($expo->expoid);
	usort($jobList, "JobCompare");

	$prefJobidList = array();
	$prefDesireList = array();
	if (count($_POST) > 0)
	{
		$keys = array_keys($_POST);
		$values = array_values($_POST);

		for ($k = 0; $k < count($_POST); $k++)
		{
			if (strpos($keys[$k], 'title') !== false)
			{
				list($prefJobidList[], $prefDesireList[]) = explode(':', $values[$k]);
			}
		}
	}

	$shiftpreference = new ShiftPreference;
	foreach ($jobList as $j)
	{
		$shiftpreference->workerid = $author->workerid;
		$shiftpreference->jobid = $j->jobid;
		$shiftpreference->stationid = $j->stationid;
		$shiftpreference->expoid = $j->expoid;

		$pos = array_search($j->jobid, $prefJobidList);
		if ($pos === false)
		{
			$shiftpreference->desirePercent = NULL;
		}
		else
		{
			$shiftpreference->desirePercent = $prefDesireList[$pos];
			if ($shiftpreference->desirePercent == 0)
			{
				$shiftpreference->desirePercent = NULL;
			}
		}
		$shiftpreference->update();
	}

	// note post $shiftpreference save
	if ($expo->scheduleAssignAsYouGo && $expo->scheduleWorkerReset)
	{
		// note is different from PreferenceReviewAction;
		// if NOT scheduleWorkerReset, then PreferenceReviewAction has no impact
		$shifts = ShiftAssignment::selectWorker($expo->expoid, $author->workerid);
		ShiftAssignment::deleteList($shifts);
		FirstComeFirstServed::assignAsYouGo($expo, $author);
		// also, we don't default to schedule page
	} // assignAsYouGo
}

function reviewPageContent()
{
	echo "<form method=\"POST\" name=\"preferencereview_form\" action=\"PreferenceReviewAction.php\">\n";
	echo "\n";
	echo "<div id=\"content\" style=\"clear:both\">\n";
	echo "Below are all the sessions you&apos;ve shown interest in. You&apos;re welcome to trim down the list. You MUST set a maximum number of hours at the bottom of the page (it is defaulted to 20), and we&apos;ll schedule you for where we need you the most!\n";
	echo "<p />\n";
	echo "Note: Our system won&apos;t allow us to double book you, so if you have two sessions at the same time, you&apos;ll only be scheduled for one.  Also know that if you requested more shifts than the maximum amount of hours you can commit, you will not be booked to all of the events you selected.\n";
	echo "<p />\n";
	echo "Click next at the bottom of the page once you are comfortable with your selections.\n";
	echo "<p />\n";

	$jobIncludeList = array();
	$jobIncludeList[] = "Volunteer";

	$jobList = Job::selectExpo($expo->expoid);
	usort($jobList, "JobCompare");

	$date = array();
	$max_job_size = 0;
	$max_time_size = 0;
	$max_location_size = 0;
	$max_title_size = 0;
	foreach ($jobList as $j)
	{
		$stationDateTime = swwat_format_shift($j->startTime, $j->stopTime);
		list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);

		$date[] = $stationDate;

		$job = $j->jobTitle;
		if (strlen($job) > $max_job_size)
		{
			$max_job_size = strlen($job);
		}
		
		if (strlen($stationTime) > $max_time_size)
		{
			$max_time_size = strlen($stationTime);
		}
		$location = $j->location;
		if (strlen($location) > $max_location_size)
		{
			$max_location_size = strlen($location);
		}
		$title = $j->stationTitle;
		if (strlen($title) > $max_title_size)
		{
			$max_title_size = strlen($title);
		}
	}
	$date = array_values(array_unique($date));

	$max_radio_size = "10%";
	$max_percent_size = "5%";
	//$max_job_size = 12*($max_job_size);
	$max_time_size = "12%";
	$max_hours_size = "5%";
	$max_location_size = "15%";
	$max_title_size = "53%";
	$table_size = "95%";

	$numColumns = 6;

	echo "<table width=\"".$table_size."\" align=\"center\" class=\"research\">\n";
	echo "<tr class=\"accordion\">\n";
	echo "<th width=\"120\" class=\"rowTitle\"></th>";
	echo "<th width=\"".$max_percent_size."\" class=\"rowTitle\">Percent Filled</th>";
	//echo "<th width=\"".$max_job_size."\" class=\"rowTitle\">Job</th>";
	echo "<th width=\"".$max_time_size."\" class=\"rowTitle\">Time</th>";
	echo "<th width=\"".$max_hours_size."\" class=\"rowTitle\">Hours</th>";
	echo "<th width=\"".$max_location_size."\" class=\"rowTitle\">Location</th>";
	echo "<th width=\"".$max_title_size."\" class=\"rowTitle\">Title</th>";
	echo "</tr>\n";

	// kludge to remove empty dates
	for ($k = 0; $k < count($date); $k++)
	{
		$count[$k] = 0;
		for ($l = 0; $l < count($jobList); $l++)
		{
			$sp = ShiftPreference::selectID($author->workerid, $jobList[$l]->jobid);
			if (!is_null($sp))
			{
				$desire = $sp->desirePercent;
			}
			else
			{
				$desire = 0;
			}

			$stationDateTime = swwat_format_shift($jobList[$l]->startTime, $jobList[$l]->stopTime);
			list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);
	
			if (!strcmp($date[$k], $stationDate) && in_array($jobList[$l]->jobTitle, $jobIncludeList) && $desire != 0)
			{
				$count[$k]++;
			}
		}
	}

	for ($k = 0; $k < count($date); $k++)
	{
		$dow = date('l', strtotime($date[$k]));

		if ($count[$k] != 0)
		{
			echo "<tr class=\"accordion\">\n";
			echo "<th colspan=\"".$numColumns."\" class=\"rowTitle\">";
			echo "<div style=\"float:left\">".htmlspecialchars($date[$k].", ".$dow)."</div>\n";
			echo "<div style=\"float:right\"><img id=\"icon1\" src=\"".PARAM_COLLAPSE_ICON."\"/></div>\n";
			echo "</th>\n";
			echo "</tr>\n";
		}

		for ($l = 0; $l < count($jobList); $l++)
		{
			//$optionArray = array(array($jobList[$l]->jobid.":0", "No"), array($jobList[$l]->jobid.":1", "Yes"));

			$param_station = PARAM_TITLE . $l;

			if (!isset($_POST[$param_station]))
			{
				$_POST[$param_station] = NULL;
			}

			$sp = ShiftPreference::selectID($author->workerid, $jobList[$l]->jobid);
			if (!is_null($sp))
			{
				$desire = $sp->desirePercent;
			}
			else
			{
				$desire = 0;
			}

			$stationDateTime = swwat_format_shift($jobList[$l]->startTime, $jobList[$l]->stopTime);
			list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);

			$start = $jobList[$l]->startTime;
			$stop = $jobList[$l]->stopTime;
			$diff = $start->diff($stop);
			$hours = ($diff->d)*24 + $diff->h + ($diff->i)/60 + ($diff->s)/360;

			if ($jobList[$l]->minCrew != 0)
			{
				$percent = intval(100*($jobList[$l]->assignedCrew / $jobList[$l]->minCrew))."%";
			}
			else
			{
				$percent = "-";
			}

			$s = Station::selectID($jobList[$l]->stationid);

			$dclass = preg_replace('/\s/', '_', $date[$k]);
			if (!strcmp($date[$k], $stationDate) && in_array($jobList[$l]->jobTitle, $jobIncludeList) && $desire != 0)
			{
				echo "<tr class=\"".$dclass."\">\n";
				echo "<td class=\"fieldValue\">";
				//swwat_createRadioSelect($param_station, $optionArray, "radio", $jobList[$l]->jobid.":".$desire);
				echo "<input id=\"".$param_station."n\" name=\"".$param_station."\" type=\"radio\" value=\"".$jobList[$l]->jobid.":0"."\" ";
				if ($desire == 0)
				{
					echo "checked=\"checked\" ";
				}
				echo "/>";
				echo "<label for=\"".$param_station."n\">No&nbsp;&nbsp;</label>\n";
				echo "<input id=\"".$param_station."y\" name=\"".$param_station."\" type=\"radio\" value=\"".$jobList[$l]->jobid.":100"."\" ";
				if ($desire != 0)
				{
					echo "checked=\"checked\" ";
				}
				echo "/>";
				echo "<label for=\"".$param_station."y\">Yes</label>\n";
				echo "</td>\n";
				echo "<td class=\"fieldValue\">".htmlspecialchars($percent)."</td>\n";
				//echo "<td class=\"fieldValue\">".htmlspecialchars($jobList[$l]->jobTitle)."</td>\n";
				echo "<td class=\"fieldValue\">".htmlspecialchars($stationTime)."</td>\n";
				echo "<td class=\"fieldValue\">".htmlspecialchars($hours)."</td>\n";
				echo "<td class=\"fieldValue\">".htmlspecialchars($jobList[$l]->location)."</td>\n";
				echo "<td class=\"fieldValue\">";
				echo "<div style=\"float:left\">".htmlspecialchars($jobList[$l]->stationTitle)."</div>\n";
				echo "<div style=\"float:right\"><img id=\"icon2\" src=\"".PARAM_EXPAND_ICON."\"/></div>\n";
				echo "</td>\n";
				echo "</tr>\n";
				echo "<tr class=\"description ".$param_station." ".$dclass."\">\n";
				echo "<td class=\"fieldValue\" style=\"text-align: center\" colspan=\"".$numColumns."\">";
				echo htmlspecialchars($s->description)."<br><a href=\"".htmlspecialchars($s->URL)."\">".htmlspecialchars($s->URL)."</a></td>\n";
				echo "</td>\n";
				echo "</tr>\n";
			}
		}
	}
	echo "</table>\n";
	echo "<p />";

	$_POST[PARAM_MAXHOURS] = $author->selectMaxHours($expo->expoid);

	echo "The MAXIMUM number of hours I am available to work is ";
	swwat_createInputValidateInteger(PARAM_MAXHOURS, "content", 2);

	echo "<p />";
	echo "</div><!-- content -->\n";
}

function reviewActionContent($author, $expo)
{
	if (isset($_POST[PARAM_MAXHOURS]) && !is_null($_POST[PARAM_MAXHOURS]))
	{
		$author->updateMaxHours($expo->expoid, swwat_parse_string(html_entity_decode($_POST[PARAM_MAXHOURS])));
	}

	$jobList = Job::selectExpo($expo->expoid);
	usort($jobList, "JobCompare");

	$prefJobidList = array();
	$prefDesireList = array();
	if (count($_POST) > 0)
	{
		$keys = array_keys($_POST);
		$values = array_values($_POST);

		for ($k = 0; $k < count($_POST); $k++)
		{
			if (strpos($keys[$k], 'title') !== false)
			{
				list($prefJobidList[], $prefDesireList[]) = explode(':', $values[$k]);
			}
		}
	}

	$shiftpreference = new ShiftPreference;
	foreach ($jobList as $j)
	{
		$shiftpreference->workerid = $author->workerid;
		$shiftpreference->jobid = $j->jobid;
		$shiftpreference->stationid = $j->stationid;
		$shiftpreference->expoid = $j->expoid;

		$pos = array_search($j->jobid, $prefJobidList);
		if ($pos === false)
		{
			$shiftpreference->desirePercent = NULL;
		}
		else
		{
			$shiftpreference->desirePercent = $prefDesireList[$pos];
			if ($shiftpreference->desirePercent == 0)
			{
				$shiftpreference->desirePercent = NULL;
			}
		}
		$shiftpreference->update();
	}

	// note post $shiftpreference save
	if ($expo->scheduleAssignAsYouGo)
	{
		if ($expo->scheduleWorkerReset)
		{
			$shifts = ShiftAssignment::selectWorker($expo->expoid, $author->workerid);
			ShiftAssignment::deleteList($shifts);
		}
		FirstComeFirstServed::assignAsYouGo($expo, $author);
		if ($expo->scheduleVisible)
		{
			mailSchedule($expo, $author);
			header('Location: WorkerSchedulePage.php');
			include('WorkerSchedulePage.php');
			return;
		}
	} // assignAsYouGo
}

function welcomePageNavi()
{
	echo "<div id=\"navi_next\" style=\"clear: both; float:right; margin-right: 5px\">\n";
	swwat_createBigInputSubmit("Next", "Next");
	echo "</div><!-- navi_next -->\n";
	echo "\n";
	echo "</form>\n";
}

function wizardPageNavi()
{
	echo "<div id=\"navi_back\" style=\"clear: both; float:left; margin-left: 5px\">\n";
	echo "    <input style=\"font-size: 28px; width: 100%; height: 48px; font-weight: bold;\" type=\"button\" id=\"Back\" name=\"Back\" value=\"Back\" onclick=\"window.location='PreferenceWelcomePage.php'\" />\n";
	echo "</div><!-- navi_back -->";
	echo "\n";
	echo "<div id=\"navi_next\" style=\"float:right; margin-right: 5px\">\n";
	swwat_createBigInputSubmit("Next", "Next");
	echo "</div><!-- navi_next -->\n";
	echo "\n";
	echo "</form>\n";
}

function reviewPageNavi()
{
	echo "<div id=\"navi_back\" style=\"clear: both; float:left; margin-left: 5px\">\n";
	echo "    <input style=\"font-size: 28px; width: 100%; height: 48px; font-weight: bold;\" type=\"button\" id=\"Back\" name=\"Back\" value=\"Back\" onclick=\"window.location='PreferenceWizardPage.php'\" />\n";
	echo "</div>\n";
	echo "</div><!-- navi_back -->\n";
	echo "<div id=\"submit\" style=\"float:right; margin-right: 5px\">\n";
	swwat_createBigInputSubmit("Submit", "Submit & Request\n Schedule");
	echo "</div><!-- navi_next -->\n";
	echo "\n";
	echo "</form>\n";
}

?>
