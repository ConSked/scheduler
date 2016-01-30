<?php // $Id: ScheduleExpoAction.php 2298 2012-10-01 19:16:12Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/ShiftAssignment.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('util/ScheduleEnum.php');
require_once('schedule/AbstractScheduler.php');
require_once('schedule/FirstComeFirstServed.php');
require_once('schedule/FirstComeLocationLocked.php');
require_once('schedule/FirstComeSoftLocationLocked.php');
require_once('schedule/AssignAndSubtract.php');
require_once('schedule/JobSchedule.php');
require_once('schedule/WorkerSchedule.php');
require_once('swwat/gizmos/parse.php');

$expo = getExpoCurrent();
if (isset($_POST[PARAM_SCHEDULE_PUBLISH]) &&
    isset($_SESSION[PARAM_SCHEDULE_PUBLISH]))
{
    $keepFlag = isset($_SESSION[PARAM_SCHEDULE_KEEP]);
    AbstractScheduler::commitSchedule($expo->expoid, $keepFlag, $_SESSION[PARAM_SCHEDULE_PUBLISH]);
    unset($_SESSION[PARAM_SCHEDULE_PUBLISH]);
    unset($_SESSION[PARAM_SCHEDULE_ALGO]);
    unset($_SESSION[PARAM_PAGE_MESSAGE]);
    unset($_SESSION[PARAM_SCHEDULE_KEEP]);

    header('Location: SchedulingReportPage.php');
    include('SchedulingReportPage.php');
    return;
}

$algorithm = $_POST[PARAM_SCHEDULE_ALGO];
try
{
    $algorithm = swwat_parse_enum($algorithm, ScheduleEnum::$ENUM_ARRAY, FALSE);
}
catch (ParseSWWATException $ex)
{
    header('Location: WorkerLoginPage.php');
    include('WorkerLoginPage.php');
    return;
}
$keepFlag = isset($_POST[PARAM_SCHEDULE_KEEP]);


$_SESSION[PARAM_SCHEDULE_ALGO] = $_POST[PARAM_SCHEDULE_ALGO];
$_SESSION[PARAM_SCHEDULE_KEEP] = $_POST[PARAM_SCHEDULE_KEEP];

$workerList = NULL;
$stationList = NULL;
$assignmentList = NULL;

try
{
    // needed for all
    $workerList = WorkerSchedule::selectExpo($expo->expoid);
    // needed for all excepting PREFERENCEASSIGN
    $stationList = JobSchedule::selectExpo($expo->expoid);
    $assignmentList = ShiftAssignment::selectExpo($expo->expoid);
}
catch (PDOException $ex)
{
    $_POST[PARAM_PAGE_MESSAGE] = "Please try to schedule at a later time.";
    logMessage("ScheduleExpoAction", "Database screwup" . $ex->getMessage());
    header('Location: ScheduleExpoPage.php');
    include('ScheduleExpoPage.php');
    return;
}

if (!is_null($algorithm))
{
    $aas = NULL;
    if (0 == strcmp(ASSIGNANDSUBTRACT, $algorithm))
    {
        $aas = new AssignAndSubtract($expo->expoid, $stationList, $workerList, $assignmentList, $keepFlag);
    }
    else if (0 == strcmp(FIRSTCOMEFIRSTSERVE, $algorithm))
    {
        $aas = new FirstComeFirstServed($expo->expoid, $stationList, $workerList, $assignmentList, $keepFlag);
    }
    else if (0 == strcmp(FIRSTCOMELOCATIONLOCKED, $algorithm))
    {
        $aas = new FirstComeLocationLocked($expo->expoid, $stationList, $workerList, $assignmentList, $keepFlag);
    }
    else if (0 == strcmp(FIRSTCOMESOFTLOCATIONLOCKED, $algorithm))
    {
        $aas = new FirstComeSoftLocationLocked($expo->expoid, $stationList, $workerList, $assignmentList, $keepFlag);
    }
    else // default
    {
        $aas = new FirstComeFirstServed($expo->expoid, $stationList, $workerList, $assignmentList, $keepFlag);
    }
    $workerList = NULL;
    $stationList = NULL;
    $assignmentList = NULL;

    $d1 = new DateTime();
    logMessage("ScheduleExpoAction", "**** $algorithm - assignSchedule(".$expo->expoid.") ****", $d1->format('H:i'), "\n");
    $aas->assignSchedule($expo->expoid);
    $d2 = new DateTime();
    logMessage("ScheduleExpoAction", "**** $algorithm - assignSchedule(".$expo->expoid.") ****", $d2->format('H:i'), "  elapsed: ", $d2->getTimestamp() - $d1->getTimestamp(), "\n");

    $aas->logJobListState($algorithm, "jobs after assignment");
    $aas->logWorkerListState($algorithm, "workers after assignment");

    // $_SESSION[PARAM_SCHEDULE_PUBLISH] = $aas; - this will not work due to the recursion
    // i.e. stations->workers->stations->.....
    $_SESSION[PARAM_SCHEDULE_PUBLISH] = $aas->getSchedule();
    $_SESSION[PARAM_PAGE_MESSAGE] = "$algorithm finished";
}

session_write_close();
header('Location: ScheduleExpoPage.php');
include('ScheduleExpoPage.php');

?>
