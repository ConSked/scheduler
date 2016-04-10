<?php // $Id: StationData.php 1912 2012-09-12 07:28:58Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('db/Expo.php');
require_once('db/Job.php');
require_once('db/StationJob.php');
require_once('db/JobTitle.php');
require_once('properties/constants.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');

/*
 * This module is executed as a function (rather than call-outs from HTML)
 * in order to pass-in the $isEditableFlag as a variable (rather than lookup some $_SESSION variable)
 */
function createStationDataHTMLRows(StationJob $station, Expo $expo, $formName, $isDisabledFlag = TRUE)
{
    if (is_null($station) || is_null($expo))
    {
		return;
    }
	if (!is_null($station->startTime))
	{
		$_POST[PARAM_DATE]           = htmlspecialchars(swwat_format_isodate($station->startTime));
    	$_POST[PARAM_STARTHOUR]      = htmlspecialchars(swwat_format_isotime($station->startTime));
    	$_POST[PARAM_STARTTIME]      = htmlspecialchars(swwat_format_isodatetime($station->startTime));
	}
	else
	{
		$_POST[PARAM_DATE]           = htmlspecialchars($station->startTime);
		$_POST[PARAM_STARTHOUR]      = htmlspecialchars($station->startTime);
		$_POST[PARAM_STARTTIME]      = htmlspecialchars($station->startTime);
	}
	if (!is_null($station->stopTime))
	{
    	$_POST[PARAM_STOPHOUR]       = htmlspecialchars(swwat_format_isotime($station->stopTime));
    	$_POST[PARAM_STOPTIME]       = htmlspecialchars(swwat_format_isodatetime($station->stopTime));
	}
	else
	{
    	$_POST[PARAM_STOPHOUR]       = htmlspecialchars($station->stopTime);
    	$_POST[PARAM_STOPTIME]       = htmlspecialchars($station->stopTime);
	}
    $_POST[PARAM_LOCATION]           = htmlspecialchars($station->location);
    $_POST[PARAM_INSTRUCTION]        = htmlspecialchars($station->instruction);
    $_POST[PARAM_DESCRIPTION]        = htmlspecialchars($station->description);
    $_POST[PARAM_TITLE]              = htmlspecialchars($station->title);

	$_POST[PARAM_EXPOSTART] = htmlspecialchars(date_format($expo->startTime, 'l j, F Y'));
	$_POST[PARAM_EXPOSTOP]  = htmlspecialchars(date_format($expo->stopTime, 'l j, F Y'));

	echo '<tr style="display:none"><td><input type="hidden" id="'.PARAM_EXPOSTART.'" name="'.PARAM_EXPOSTART.'" value="', $_POST[PARAM_EXPOSTART], '"/></td></tr>'."\n";
	echo '<tr style="display:none"><td><input type="hidden" id="'.PARAM_EXPOSTOP.'" name="'.PARAM_EXPOSTOP.'" value="', $_POST[PARAM_EXPOSTOP], '"/></td></tr>'."\n";

    echo "  <tr><td class='fieldTitle'>Title:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_TITLE, $formName, 'titleCheck', 255, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Description:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_DESCRIPTION, $formName, 'descriptionCheck', 2048, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Location:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_LOCATION, $formName, 'locationCheck', 255, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Instruction:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_INSTRUCTION, $formName, 'instructionCheck', 255, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Date:</td>\n<td>";
	echo '  <input type="text" id="', PARAM_DATE, '" name="', PARAM_DATE, '" value="', $_POST[PARAM_DATE], '" size="25" ';
    if ($isDisabledFlag)
    {
        echo ' disabled="disabled" ';
    }
    echo "/></td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Start Time:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_STARTHOUR, $formName, 'starthourCheck', 11, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Stop Time:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_STOPHOUR, $formName, 'stophourCheck', 11, $isDisabledFlag);
    echo "</td></tr>\n";
} // createStationDataHTMLRows

function createJobDataHTMLRows(StationJob $job, $formName, $showDates, $isDisabledFlag = TRUE)
{
    if (is_null($job))
    {
		return;
    }

    $_POST[PARAM_MAXCREW]            = htmlspecialchars($job->maxCrew);
    $_POST[PARAM_MINCREW]            = htmlspecialchars($job->minCrew);
    $_POST[PARAM_ASSIGNEDCREW]       = htmlspecialchars($job->assignedCrew);
    $_POST[PARAM_MAXSUPERVISOR]      = htmlspecialchars($job->maxSupervisor);
    $_POST[PARAM_MINSUPERVISOR]      = htmlspecialchars($job->minSupervisor);
    $_POST[PARAM_ASSIGNEDSUPERVISOR] = htmlspecialchars($job->assignedSupervisor);

    if ($showDates)
    {
        // remember these are READ-only
        echo "<tr><td class='fieldTitle'>Start Time:</td>\n<td>";
        echo "<input type='text' value='" . swwat_format_isodatetime($job->startTime) . "' disabled='disabled'/>";
        echo "</td></tr>\n";
        echo "<tr><td class='fieldTitle'>Start Time:</td>\n<td>";
        echo "<input type='text' value='" . swwat_format_isodatetime($job->stopTime) . "' disabled='disabled'/>";
        echo "</td></tr>\n";
    }

    // $optionArray = 2D array {{name, value}, {name, value}, ...}
    $jobOptionArray = JobTitle::titleOptions($job->expoid);
    if (is_null($job->jobTitle))
    {
        $job->jobTitle = $jobOptionArray[0][0];
    }
    $_POST[PARAM_JOB]                = $job->jobTitle;
    echo "  <tr><td class='fieldTitle'>Job:</td>\n<td>";
    swwat_createSelect(0, PARAM_JOB, $jobOptionArray, $_POST[PARAM_JOB], $isDisabledFlag);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Min Crew:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_MINCREW, $formName, 'mincrewCheck', 11, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Max Crew:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_MAXCREW, $formName, 'maxcrewCheck', 11, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Assigned Crew:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_ASSIGNEDCREW, $formName, NULL, 11, TRUE);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Min Supervisor:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_MINSUPERVISOR, $formName, 'minsupervisorCheck', 11, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Max Supervisor:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_MAXSUPERVISOR, $formName, 'maxsupervisorCheck', 11, $isDisabledFlag);
    echo "</td></tr>\n";

    echo "  <tr><td class='fieldTitle'>Assigned Supervisor:</td>\n<td>";
    swwat_createInputValidateLength(PARAM_ASSIGNEDSUPERVISOR, $formName, NULL, 11, TRUE);
    echo "</td></tr>\n";
} // createStationDataHTMLRows

?>
