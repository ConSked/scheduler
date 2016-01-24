<?php 
require_once('util/fpdf.php');
require_once('properties/constants.php');
require_once('db/Job.php');
require_once('db/ShiftAssignmentView.php');
require_once('db/Worker.php');
require_once('swwat/gizmos/format.php');
require_once('util/session.php');
require_once('util/log.php');

$font_family = 'Arial';
$font_style = 'B';
$font_size = 8;

$pdf = new FPDF();
$pdf->Addpage();
$pdf->SetFont($font_family, $font_style, $font_size);
$pdf->SetFillCOlor(200);

// gather data
$expoId = 2;

$jobList = Job::selectExpo($expoId);
usort($jobList, "JobCompare");

$j = 0;
// Todo: Make shift/job name span the relevant fields.
foreach ($jobList as $job)
{
	$matrix[$j][0] = 'Date';
	$matrix[$j][1] = 'Day of Week';
	$matrix[$j][2] = 'Shift Time';
	$matrix[$j][3] = 'Volunteer Role';
	$matrix[$j][4] = 'Volunteer Name';
        $matrix[$j][5] = 'Shift Name';
	$matrix[$j][6] = true;
	$j++;

	$assignedWorkerList = ShiftAssignmentView::selectJob($expoId, $job->jobid);

	foreach ($assignedWorkerList as $aw)
	{
		$date = date_format($aw->startTime, 'd-M');
		$weekDay = date_format($aw->startTime, 'l');
		$awDateTime = swwat_format_shift($aw->startTime, $aw->stopTime);
		list($awDate, $shiftTime, $start) = explode(';', $awDateTime);
		$shiftName = $aw->stationTitle . ' - ' . $aw->location;

		$worker = Worker::selectID($aw->workerid);

		$matrix[$j][0] = $date;
		$matrix[$j][1] = $weekDay;
		$matrix[$j][2] = $shiftTime;
		$matrix[$j][3] = $worker->roleString();
		$matrix[$j][4] = $worker->nameString2();
                $matrix[$j][5] = $shiftName;
		$matrix[$j][6] = false;
		$j++;
	}
}

//find column widths
$colwidth = array(0, 0, 0, 0, 0, 0);
for ($j = 0; $j < count($matrix); $j++)
{
	for ($k = 0; $k < 6; $k++)
	{
		if ($colwidth[$k] < $pdf->GetStringWidth($matrix[$j][$k]))
		{
			$colwidth[$k] = $pdf->GetStringWidth($matrix[$j][$k]);
		}
	}
}

//output data
$stretch = 1.2;
for ($j = 0; $j < count($matrix); $j++)
{
	$pdf->Cell($stretch*$colwidth[0], $font_size, $matrix[$j][0], 1, 0, 'L', $matrix[$j][6]);
	$pdf->Cell($stretch*$colwidth[1], $font_size, $matrix[$j][1], 1, 0, 'L', $matrix[$j][6]);
	$pdf->Cell($stretch*$colwidth[2], $font_size, $matrix[$j][2], 1, 0, 'L', $matrix[$j][6]);
	$pdf->Cell($stretch*$colwidth[3], $font_size, $matrix[$j][3], 1, 0, 'L', $matrix[$j][6]);
	$pdf->Cell($stretch*$colwidth[4], $font_size, $matrix[$j][4], 1, 0, 'L', $matrix[$j][6]);
	$pdf->Cell($stretch*$colwidth[5], $font_size, $matrix[$j][5], 1, 1, 'L', $matrix[$j][6]);
}

$pdf->Output('doc.pdf', 'I');

?>
