<?php  // $Id: date.php 1096 2012-07-19 08:12:47Z swash $ Copyright (c) ConSked, LLC. All Rights Reserved.
class timePeriod
{
	public $startTime;
	public $stopTime;

	public function __construct($start, $stop)
	{
		$this->startTime = $start;
		$this->stopTime = $stop;
	}

	public function getDifference()
	{
		return abs($this->stopTime - $this->startTime);
	}

	public function slice($maxSlices, $minSliceSize)
	{
		$sliceSize = $this->getDifference() / $maxSlices;

		if ($sliceSize < $minSliceSize)
			$sliceSize = $minSliceSize;

		$slices[0] = $this->startTime;

		$slice = $this->startTime;
		for($i = 1; $i < $maxSlices+1 && $slice < $this->stopTime; $i++)
		{
			$slices[$i] = $slice + $sliceSize;
			//Round this slice to the nearest multiple of $minSliceSize
			$slices[$i] = ceil($slices[$i] / $minSliceSize) * $minSliceSize;
			if($slices[$i] > $this->stopTime)
				$slices[$i] = $this->stopTime;
			$slice = $slices[$i];
		}

		return $slices;
	}
}

function datetimeBetween($d1, $d2, $between)
{
    return ((($d1 < $between) && ($between < $d2)) || ($d1 == $between) || ($d2 == $between));
} // datetimeBetween

function datetimeCompare($d1, $d2)
{
    if ($d1 > $d2)  {  return  1;  }
    if ($d1 < $d2)  {  return -1;  }
    return 0;
} // datetimeCompare($d1, $d2)

function dateCompare($d1, $d2)
{
    $d1 = clone $d1; // shallow copy
    $d1->setTime(0,0,0);
    $d2 = clone $d2; // shallow copy
    $d2->setTime(0,0,0);
    return datetimeCompare($d1, $d2);
} // dateCompare($d1, $d2)

/**
 * @return minutes of ($d1-$d2)
 */
function minuteDiff($d1, $d2)
{
    $d1 = $d1->diff($d2);
	$d1 = $d1->format("%i") + $d1->format("%H")*60;
    return $d1;
} // minuteDiff

?>
