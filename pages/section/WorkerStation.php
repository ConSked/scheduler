<?php // $Id: WorkerStation.php 2363 2012-10-09 17:37:23Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('swwat/gizmos/format.php');
require_once('db/Expo.php');
require_once('db/ShiftAssignmentView.php');
require_once('db/Worker.php');

/**
 * for ExpoAssignPage: createWorkerStationHTMLRows($worker, $stationArray, TRUE, FALSE)
 * for WorkerDisablePage: createWorkerStationHTMLRows($worker, $stationArray, FALSE, TRUE)
 */
function createWorkerStationHTMLRows(Worker $worker, array $assignmentViewArray, $showName, $showExpo)
{
    if (is_null($worker) || is_null($assignmentViewArray) || (0 == count($assignmentViewArray)))
    {
        return;
    }

    foreach ($assignmentViewArray as $assignmentView)
    {
        echo "<tr>\n<td style='padding-right: 10px; padding-left: 10px'>";
        if ($showName)
        {
            echo htmlspecialchars($worker->nameString()), "</td>\n<td style='padding-right: 10px; padding-left: 10px'>";
            echo htmlspecialchars($worker->roleString()), "</td>\n<td style='padding-right: 10px; padding-left: 10px'>";
        }
        if ($showExpo)
        {
            echo htmlspecialchars($assignmentView->expoTitle), "</td>\n<td style='padding-right: 10px; padding-left: 10px'>";
        }
        echo htmlspecialchars($assignmentView->stationTitle), "</td>\n<td style='padding-right: 10px; padding-left: 10px'>";
        echo htmlspecialchars($assignmentView->jobTitle), "</td>\n<td style='padding-right: 10px; padding-left: 10px'>";
        echo swwat_format_ussimpledatetime($assignmentView->startTime), "</td>\n<td style='padding-right: 10px; padding-left: 10px'>";
        echo swwat_format_ussimpledatetime($assignmentView->stopTime), "</td>\n<td style='text-align:right; padding-right: 10px; padding-left: 10px'>";
        echo htmlspecialchars($assignmentView->assignedSupervisor), "</td>\n<td style='text-align:right; padding-right: 10px; padding-left: 10px'>";
        echo htmlspecialchars($assignmentView->assignedCrew), "</td>\n</tr>\n";
    }
} // createWorkerStationHTMLRows

?>
