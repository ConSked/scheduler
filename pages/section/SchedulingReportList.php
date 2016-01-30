<?php // $Id: SchedulingReportList.php 2428 2003-01-07 18:56:20Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/Job.php');
require_once('db/ShiftAssignment.php');
require_once('db/ShiftStatus.php');
require_once('db/StationJob.php');
require_once('db/Worker.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');

function findWorker($workerid, array $workerList)
{
    for ($k = 0; $k < count($workerList); $k++)
    {
        if ($workerid == $workerList[$k]->workerid)
        {
            return $k;
        }
    } // $k
    return NULL;
} // findWorker

function makeSchedulingReportStationListHTMLRows(Job $job, array $workerList, $k, $max_name_size, $max_email_size)
{
    $stationDateTime = swwat_format_shift($job->startTime, $job->stopTime);
    list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);
    $dclass = preg_replace('/\s/', '_' , $stationDate);

    echo "<tr class='mainTitle All ".$dclass."'>\n";
    echo "<td class='fieldValue'><a href='StationViewPage.php?";
    echo PARAM_LIST2_INDEX, "=", $k, "'>";
    echo htmlspecialchars($job->stationTitle);
    echo "</a></td>\n";
    echo "<td class='fieldValue'>".htmlspecialchars($job->jobTitle)."</td>\n";
    echo "<td class='fieldValue'>".htmlspecialchars($job->location)."</td>\n";
    echo "<td class='fieldValue'>".htmlspecialchars($stationDate)."</td>\n";
    echo "<td class='fieldValue'>".htmlspecialchars($stationTime)."</td>\n";
    echo "</tr>\n";
    echo "<tr class='All ".$dclass."'>\n";
    echo "<td colspan='5' style='padding-left: 15px'>\n";
    echo "<table class='research' width='100%'>\n";

	if ($job->assignedSupervisor == 0 && $job->minSupervisor == 0 && $job->maxSupervisor == 0)
	{
		$color = 'unstaffed';
	}
	else
	{
    	if ($job->assignedSupervisor < $job->minSupervisor)
	    {
	        $color = 'understaffed';
	    }
	    else if ($job->assignedSupervisor > $job->maxSupervisor)
	    {
	        $color = 'overstaffed';
	    }
	    else
	    {
	        $color = 'rightstaffed';
	    }
	}
    $supervisor = 'Supervisors: '.$job->assignedSupervisor.' ('.$job->minSupervisor.' - '.$job->maxSupervisor.')';
    echo "<tr class='accordion Supervisors'>\n";
    echo "<td colspan='5' class='fieldValue ".$color."'>\n";
    echo "<div style='float:left'>". htmlspecialchars($supervisor)."</div>\n";
    echo "<div style='float:right'>".ucfirst($color)."&nbsp;&nbsp;<img id='icon' src='".PARAM_EXPAND_ICON."'/></div>\n";
    echo "</td>\n";
    echo "</tr>\n";

    $assignedList = ShiftAssignment::selectJob($job->expoid, $job->jobid);
    $warnFlag = ($job->assignedSupervisor < $job->minSupervisor); // if low, then maybe none - note false if 0 == 0
    if ($job->assignedSupervisor > 0)
    {
        foreach ($assignedList as $assigned)
        {
            $index = findWorker($assigned->workerid, $workerList);
            $w = $workerList[$index];
            if (!is_null($w) && $w->isSupervisor())
            {
                makeSchedulingReportWorkerListHTMLRows($w, $index, $max_name_size, $max_email_size);
                $warnFlag = FALSE;
            }
        }
    }
    if ($warnFlag)
    {
        echo "<tr><td class='fieldError' colspan='5'>There are currently no supervisors assigned to this station.</td></tr>\n";
    }
    echo "</table>\n";
    echo "<table class='research' width='100%'>\n";

	if ($job->assignedCrew == 0 && $job->minCrew == 0 && $job->maxCrew == 0)
	{
		$color = 'unstaffed';
	}
	else
	{
    	if ($job->assignedCrew < $job->minCrew)
	    {
	        $color = 'understaffed';
	    }
	    else if ($job->assignedCrew > $job->maxCrew)
	    {
	        $color = 'overstaffed';
	    }
	    else
	    {
	        $color = 'rightstaffed';
	    }
	}
    $crew = 'Crew: '.$job->assignedCrew.' ('.$job->minCrew.' - '.$job->maxCrew.')';
    echo "<tr class='accordion Crew'>\n";
    echo "<td colspan='5' class='fieldValue ".$color."'>\n";
    echo "<div style='float:left'>".htmlspecialchars($crew)."</div>\n";
    echo "<div style='float:right'>".ucfirst($color)."&nbsp;&nbsp;<img id='icon' src='".PARAM_EXPAND_ICON."'/></div>\n";
    echo "</td>\n";
    echo "</tr>\n";

    $warnFlag = ($job->assignedCrew < $job->minCrew); // if low, then maybe none - note false if 0 == 0
    if ($job->assignedCrew > 0)
    {
        foreach ($assignedList as $assigned)
        {
            $index = findWorker($assigned->workerid, $workerList);
            $w = $workerList[$index];
            if (!is_null($w) && $w->isCrewMember())
            {
                makeSchedulingReportWorkerListHTMLRows($w, $index, $max_name_size, $max_email_size);
                $warnFlag = FALSE;
            }
        }
    }
    if ($warnFlag)
    {
        echo "<tr><td class='fieldError' colspan='5'>There are currently no crew assigned to this station.</td></tr>\n";
    }

    echo "</table>\n";
    echo "</td>\n</tr>\n";
} // makeSchedulingReportListHTMLRows

function makeSchedulingReportWorkerListHTMLRows(Worker $w, $k, $max_name_size, $max_email_size)
{
    echo "<tr>\n";
    echo "<td width='".$max_name_size."' class='fieldValueFirst'>\n";
    echo "<a href='StationLockReportPage.php?".PARAM_LIST_INDEX."=".$k."'>".htmlspecialchars($w->nameString())."</a>\n";
    echo "</td>\n";
    echo "<td width='".$max_email_size."' class='fieldValue'>".htmlspecialchars($w->email)."</td>\n";
    echo "<td class='fieldValue'>".htmlspecialchars(swwat_format_phone($w->phone))."</td>\n";
    echo "</tr>\n";
} // makeSchedulingReportWorkerListHTMLRows

function createSchedulingReportHTMLList($expoid)
{
    $stationList = StationJob::selectExpo($expoid);

    $dates = array();
    foreach ($stationList as $s)
    {
        $stationDateTime = explode(';', swwat_format_shift($s->startTime, $s->stopTime));
        $dates[]  = $stationDateTime[0];
    }
    $dates = array_values(array_unique($dates));

    echo "Select Date: <select id='".PARAM_DATE."' name='".PARAM_DATE."' onchange='hideRows()'>\n";
    for ($k = 0; $k < count($dates); $k++)
    {
        echo "<option value='".$dates[$k]."'>&nbsp;".$dates[$k]."&nbsp;</option>\n";
    }
    echo "<option value='All'>&nbsp;All Dates&nbsp;</option>\n";
    echo "</select>\n";
    echo "<p />\n";

    echo "<input type='radio' name='role' value='Supervisors' onclick='hideRoles()' /> Supervisors";
    echo "&nbsp;&nbsp;&nbsp;<input type='radio' name='role' value='Crew' onclick='hideRoles()' /> Crew";
    echo "&nbsp;&nbsp;&nbsp;<input type='radio' name='role' value='Both' checked='checked' onclick='hideRoles()' /> Both\n";
    echo "<p />\n";

    $workerList = Worker::selectExpo($expoid);
    $_SESSION[PARAM_LIST] = $workerList;

    $max_name_size = 0;
    $max_email_size = 0;
    foreach ($workerList as $worker)
    {
        $name = $worker->nameString();
        if (strlen($name) > $max_name_size)
        {
            $max_name_size = strlen($name);
        }
        $email = $worker->email;
        if (strlen($email) > $max_email_size)
        {
            $max_email_size = strlen($email);
        }
    }
    $max_name_size = 10*$max_name_size;
    $max_email_size = 10*$max_email_size;
    $table_size = "75%";

    echo "<div id='stationlist_table'>\n";
    echo "<table width='".$table_size."'>\n";
    echo "<tr class='mainTitle'>\n";
    echo "<td class='fieldValue' colspan='5' onclick='ExpandCollapseAll()'>\n";
    echo "<div style='float:right'><div class='alldiv' style='display:inline'>Expand All</div>&nbsp;&nbsp;&nbsp;<img id='allicon' src='".PARAM_EXPAND_ICON."'/></div>\n";
    echo "</td>\n</tr>\n";

    $jobList = Job::selectExpo($expoid);
	usort($jobList, "JobCompare");
    $_SESSION[PARAM_LIST2] = $jobList;

    $c = count($jobList);
    if ($c > 0)
    {
        for ($k = 0; $k < $c; $k++)
        {
            $job = $jobList[$k];
            makeSchedulingReportStationListHTMLRows($job, $workerList, $k, $max_name_size, $max_email_size);
        }
    }
    else
    {
        echo "<tr><td class='fieldError' colspan='5'>There are currently no stations assigned to this expo.</td></tr>";
    }

    echo "</table>\n</div>\n";
} // createSchedulingReportHTMLList

?>
