<?php // $Id: ExpoData.php 2426 2003-01-02 20:17:58Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('db/Expo.php');
require_once('db/JobTitle.php');
require_once('properties/constants.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');

/*
 * This module is executed as a function (rather than call-outs from HTML)
 * in order to pass-in the $isEditableFlag as a variable (rather than lookup some $_SESSION variable)
 */

function createExpoDataHTMLRows(Expo $expo, $formName, $isDisabledFlag = TRUE)
{
    if (!is_null($expo))
    {
		if (!is_null($expo->startTime))
		{
        	$_POST[PARAM_STARTTIME] = htmlspecialchars(swwat_format_isodate($expo->startTime));
		}
		else
		{
        	$_POST[PARAM_STARTTIME] = htmlspecialchars($expo->startTime);
		}
		if (!is_null($expo->stopTime))
		{
        	$_POST[PARAM_STOPTIME]  = htmlspecialchars(swwat_format_isodate($expo->stopTime));
		}
		else
		{
        	$_POST[PARAM_STOPTIME]  = htmlspecialchars($expo->stopTime);
		}
        $_POST[PARAM_MAXHOURS]      = htmlspecialchars($expo->expoHourCeiling);
        $_POST[PARAM_TITLE]         = htmlspecialchars($expo->title);
        $_POST[PARAM_DESCRIPTION]   = htmlspecialchars($expo->description);

        $_POST[PARAM_SCHEDULE_ALGO]    = $expo->scheduleAssignAsYouGo;
        $_POST[PARAM_SCHEDULE_PUBLISH] = $expo->scheduleVisible;
		$_POST[PARAM_SCHEDULE_TIME_CONFLICT] = $expo->allowScheduleTimeConflict;
		$_POST[PARAM_NEWUSER_ADDED_ON_REGISTRATION] = $expo->newUserAddedOnRegistration;
    }

    echo "<table>\n";
	echo "  <tr>\n";
	echo "    <td>\n";
    echo "      <table>\n";
    echo "         <tr><td class='fieldTitle'>Title:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_TITLE, $formName, 'titleCheck', 255, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "         <tr><td class='fieldTitle'>Description:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_DESCRIPTION, $formName, 'descriptionCheck', 255, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "         <tr><td class='fieldTitle'>Max Hours:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_MAXHOURS, $formName, 'maxhoursCheck', 255, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "         <tr><td class='fieldTitle'>Start:</td>\n<td>";
    echo '<input type="text" id="', PARAM_STARTTIME, '" name="', PARAM_STARTTIME, '" value="', $_POST[PARAM_STARTTIME], '" readonly="readonly" size="25" ';
    if ($isDisabledFlag)
    {
        echo ' disabled="disabled" ';
    }
    echo "/></td></tr>\n";

    echo "         <tr><td class='fieldTitle'>Stop:</td>\n<td>";
    echo '<input type="text" id="', PARAM_STOPTIME, '" name="', PARAM_STOPTIME, '" value="', $_POST[PARAM_STOPTIME], '" readonly="readonly" size="25" ';
    if ($isDisabledFlag)
    {
        echo ' disabled="disabled" ';
    }
    echo "/></td></tr>\n";

    echo "         <tr><td class='fieldTitle'>Assign As You Go:</td>\n<td>\n";
    swwat_createRadioOption(PARAM_SCHEDULE_ALGO, array(PARAM_SCHEDULE_ALGO, ""), SWWAT_CHECKBOX, $_POST[PARAM_SCHEDULE_ALGO], $isDisabledFlag);
    echo "</td></tr>\n";

    echo "         <tr><td class='fieldTitle'>Publish Schedule:</td>\n<td>";
    swwat_createRadioOption(PARAM_SCHEDULE_PUBLISH, array(PARAM_SCHEDULE_PUBLISH, ""), SWWAT_CHECKBOX, $_POST[PARAM_SCHEDULE_PUBLISH], $isDisabledFlag);
    echo "</td></tr>\n";

    echo "         <tr><td class='fieldTitle'>Allow Time Conflicts:</td>\n<td>\n";
	swwat_createRadioOption(PARAM_SCHEDULE_TIME_CONFLICT, array(PARAM_SCHEDULE_TIME_CONFLICT, ""), SWWAT_CHECKBOX, $_POST[PARAM_SCHEDULE_TIME_CONFLICT], $isDisabledFlag);
    echo "</td></tr>\n";

    echo "         <tr><td class='fieldTitle'>New User Added on Registration:</td>\n<td>\n";
	swwat_createRadioOption(PARAM_NEWUSER_ADDED_ON_REGISTRATION, array(PARAM_NEWUSER_ADDED_ON_REGISTRATION, ""), SWWAT_CHECKBOX, $_POST[PARAM_NEWUSER_ADDED_ON_REGISTRATION], $isDisabledFlag);
    echo "</td></tr>\n";

    echo "      </table>\n";
	echo "    </td>\n";
	echo "    <td valign=\"top\" style=\"padding-left: 150px;\">\n";
    echo "      <table>\n";

	$jobTitle = JobTitle::selectExpo($expo->expoid);
	for ($j = 0; $j < count($jobTitle); $j++)
	{
		if ($j == 0)
		{
			echo "        <tr><td class='fieldTitle'>Job Titles:</td><td style=\"font-size: 10pt;\">".$jobTitle[$j]->jobTitle."</td></tr>\n";
		}
		else
		{
			echo "        <tr><td></td><td style=\"font-size: 10pt;\">".$jobTitle[$j]->jobTitle."</td></tr>\n";
		}
	}

    echo "      </table>\n";
	echo "    </td>\n";
	echo "  </tr>\n";
	echo "</table>\n";
} // createExpoDataHTMLRows

?>
