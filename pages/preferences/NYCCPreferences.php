<?php // $Id: NYCCPreferences.php 2419 2012-10-29 18:01:08Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('db/GrossPreference.php');
require_once('db/JobPreference.php');
require_once('db/TimePreference.php');
require_once('properties/constants.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');

function wizardPageJavascript()
{
?>
	<script type="text/javascript">
		function init()
		{
		}
	</script>
<?php
}

function reviewPageJavascript()
{
?>
	<script type="text/javascript">
		function init()
		{
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
	echo "<div align=\"center\"><h3>Welcome to the $expoTitle Crew scheduler!</h3></div>\n";
	navigationLocation($pageNum);
} // welcomePageTitle

function wizardPageTitle($expoTitle, $pageNum)
{
	echo "<div align=\"center\"><h3>Station Preference</h3></div>\n";
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
	echo "Over the next few pages, you will be guided through the process of selecting shifts you wish to volunteer for.\n";
	echo "<p />\n";
	echo "You must set the maximum number of hours that you are able to commit before confirming your schedule.\n";
	echo "<p />\n";
	echo "If you don&apos;t have preferences for where or when you volunteer, select all of the shifts for which you can commit, and then specify your maximum time commitment. We will schedule you where you are of most help to the team!\n";
	echo "<p />\n";
	echo "The scheduler will assign shifts based on the maximum number of hours for which you can commit, your availability, your need, and your preferences. You will not be assigned to any shifts you did not request.\n";
	echo "<p />\n";
	echo "</div><!-- content -->\n";
}

function format_shift($shift)
{
	list($day, $start, $dash, $stop) = explode(' ', $shift);
	$day = date_format(swwat_parse_date($day), 'D M j');
	$min1 = date_format(swwat_parse_time($start.":00"), 'i');
	if (strcmp($min1,'00'))
	{
		$start = date_format(swwat_parse_time($start.":00"), 'g:i A');
	}
	else
	{
		$start = date_format(swwat_parse_time($start.":00"), 'g A');
	}
	$min2 = date_format(swwat_parse_time($stop.":00"), 'i');
	if (strcmp($min2,'00'))
	{
		$stop = date_format(swwat_parse_time($stop.":00"), 'g:i A');
	}
	else
	{
		$stop = date_format(swwat_parse_time($stop.":00"), 'g A');
	}
	return ($day.', '.$start.' - '.$stop);
}

function createPreferenceHTMLRows($spaces, $label, $value, $paramPrefix, $unavailable = FALSE)
{
	swwat_spaces($spaces); echo "<tr>\n";
	swwat_spaces($spaces+3); echo "<th class=\"rowTitle\">", $label, "</th>\n";
	swwat_spaces($spaces+3); echo "<td>\n";
	swwat_spaces($spaces+6); echo "<select name=\"", $paramPrefix, "\">\n";
	for($selectIndex = 0; $selectIndex < 11; $selectIndex++)
	{
		$checked = ($value == $selectIndex*10) ? ' selected' : '';
		swwat_spaces($spaces+9); echo "<option value=\"",$selectIndex*10,"\"" , $checked , ">",$selectIndex,"</option>", "\n";
	}
	swwat_spaces($spaces+6); echo "</select>\n";
	swwat_spaces($spaces+3); echo "</td>\n";
	swwat_spaces($spaces); echo "</tr>\n";
}

function wizardPageContent($author, $expo)
{
	$dateSpanList = GrossPreference::selectDateSpan($expo->expoid);
	$locationList = GrossPreference::selectLocation($expo->expoid);

	$_SESSION[PARAM_DATETIME] = $dateSpanList;
	$_SESSION[PARAM_LOCATION] = $locationList;

	echo "<form method=\"POST\" name=\"preferencewizard_form\" action=\"PreferenceWizardAction.php\">\n";
	echo "<div id=\"content\" style=\"clear:both\">\n";
	echo "<table>\n";
	echo "   <tr>\n";
	echo "      <td>\n";
	echo "         <br><b>For the following dropdown menus:</b><br>\n";
	echo "         <i>\n";
	echo "            0 = I absolutely cannot work at this station and/or time.<br>\n";
	echo "            1 = I'll do it if absolutely necessary.<br>\n";
	echo "            5 or 6 ish = Yes, sounds like fun, I'm available.<br>\n";
	echo "            10 = I'd absolutely love to work this one!<br>\n";
	echo "            So consider 0, 1, and 10 as extremes (pay attention to \"absolutely\").<br><br>\n";
	echo "            <b>Note: We'll do our best to schedule everyone to their top choices, but you'll be schedule between your preferences and our need!</b>\n";
	echo "         </i>\n";
	echo "      </td>\n";
	echo "   </tr>\n";
	echo "</table>\n";
	echo "<p />\n";
	echo "<div id=\"shiftpreferencepage_data\">\n";
	echo "<table width=\"100%\">\n";
	echo "   <tr>\n";
	echo "      <td valign=\"top\">\n";
	echo "         <table>\n";
	echo "            <tr><th class=\"rowTitle2\" colspan=\"5\">Shift</th></tr>\n";
	$tp = TimePreference::selectID($author->workerid);
	for ($k = 0; $k < count($dateSpanList); $k++)
	{
		$dateSpanFormatted = format_shift($dateSpanList[$k]);

		if (!is_null($tp))
		{
			$arg = 'shift' . ($k+1);
			$default = $tp->$arg;
		}
		else
		{
			$default = 50;
		}
		createPreferenceHTMLRows(12, $dateSpanFormatted, $default, PARAM_DATETIME . $k, TRUE);
	}
	echo "         </table>\n";
	echo "      </td>\n";
	echo "      <td valign=\"top\">\n";
	echo "         <table>\n";
	echo "            <tr><th class=\"rowTitle2\" colspan=\"5\">Job</th></tr>\n";
	$jp = JobPreference::selectID($author->workerid);
	for ($k = 0; $k < count($locationList); $k++)
	{
		if (!is_null($jp))
		{
			$arg = 'job' . ($k+1);
			$default = $jp->$arg;
		}
		else
		{
			$default = 50;
		}
		createPreferenceHTMLRows(12, $locationList[$k], $default, PARAM_LOCATION . $k, FALSE);
	}
	echo "         </table>\n";
	echo "      </td>\n";
	echo "   </tr>\n";
	echo "</table>\n";
	echo "<p />\n";
	echo "<table>\n";
	echo "   <tr>\n";
	echo "      <td class=\"fieldTitle\"><h2>How many hours can you work:&nbsp;</h2></td>\n";
	echo "      <td>\n";
	$maxhours = $_POST[PARAM_MAXHOURS] = $author->selectMaxHours($expo->expoid);
	$optionArray = array();
	for ($k = 0; $k < $maxhours; $k++)
	{
		$optionArray[$k] = array(($maxhours - $k), "&nbsp;".($maxhours - $k)."&nbsp;");
	}
	swwat_createSelect(6, PARAM_MAXHOURS, $optionArray, $maxhours);
	echo "      </td>\n";
	echo "   </tr>\n";
	echo "</table>\n";
	echo "</div><!-- shiftpreferencepage_data -->\n";
	echo "</div><!-- content -->\n";
}

function parsePreferenceNumber($param, $k)
{
	$value = html_entity_decode($_POST[$param . $k]);
	if (0 == strcmp(PARAM_UNAVAILABLE, $value))
	{
		$value = NULL;
	}
	else
	{
		$value = swwat_parse_number($value, FALSE);
	}
	return $value;
}

function wizardActionContent($author, $expo)
{
	if (isset($_POST[PARAM_MAXHOURS]) && !is_null($_POST[PARAM_MAXHOURS]))
	{
		$author->updateMaxHours($expo->expoid, swwat_parse_string(html_entity_decode($_POST[PARAM_MAXHOURS])));
	}

	$dateSpanList = $_SESSION[PARAM_DATETIME];
	$locationList = $_SESSION[PARAM_LOCATION];

	// Location Preference
	$k = 0;
	while (isset($_POST[PARAM_LOCATION . $k]))
	{
		$desire = parsePreferenceNumber(PARAM_LOCATION, $k);
		$locationDesires[$locationList[$k]] = (0 == $desire) ? NULL : $desire;
		$locationTest[$k] = parsePreferenceNumber(PARAM_LOCATION, $k);
		$k += 1;
	}

	$jp = new JobPreference;
	$jp->workerid = $author->workerid;
	for ($k = 0; $k < 20; $k++)
	{
		$field = 'job'.($k + 1);
		if (isset($locationTest[$k]))
		{
			$jp->$field = $locationTest[$k];
		}
		else
		{
			$jp->$field = 0;
		}
	}

	$test = JobPreference::selectID($author->workerid);
	if (!is_null($test))
	{
		$jp->update();
	}
	else
	{
		$jp->insert();
	}

	// Time Preference
	$k = 0;
	while (isset($_POST[PARAM_DATETIME . $k]))
	{
		$desire = parsePreferenceNumber(PARAM_DATETIME, $k);
		$dateSpanDesires[$dateSpanList[$k]] = (0 == $desire) ? NULL : $desire;
		$dateSpanTest[$k] = parsePreferenceNumber(PARAM_DATETIME, $k);
		$k += 1;
	}

	$tp = new TimePreference;
	$tp->workerid = $author->workerid;
	for ($k = 0; $k < 20; $k++)
	{
		$field = 'shift'.($k+1);
		if (isset($dateSpanTest[$k]))
		{
			$tp->$field = $dateSpanTest[$k];
		}
		else
		{
			$tp->$field = 0;
		}
	}

	$test = TimePreference::selectID($author->workerid);
	if (!is_null($test))
	{
		$tp->update();
	}
	else
	{
		$tp->insert();
	}

	//exit;
	$gp = GrossPreference::updateHelper_Location_DateSpan($expo->expoid, $author->workerid, $locationDesires, $dateSpanDesires);
}

function reviewPageContent()
{
	echo "<form method=\"POST\" name=\"preferencereview_form\" action=\"\">\n";
	echo "<div id=\"content\" style=\"clear:both\">\n";
	echo "Your preferences have been recorded. You will be contacted when your schedule is ready.\n";
	echo "<p />\n";
	echo "</div><!-- content -->\n";
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
	swwat_createBigInputSubmit("Submit", "Submit");
	echo "</div><!-- navi_next -->\n";
	echo "\n";
	echo "</form>\n";
}

function reviewPageNavi()
{
	echo "<div id=\"navi_back\" style=\"clear: both; float:left; margin-left: 5px\">\n";
	echo "    <input style=\"font-size: 28px; width: 100%; height: 48px; font-weight: bold;\" type=\"button\" id=\"Back\" name=\"Back\" value=\"Back\" onclick=\"window.location='PreferenceWizardPage.php'\" />\n";
	echo "</div><!-- navi_back -->\n";
	echo "\n";
	echo "</form>\n";
}

?>
