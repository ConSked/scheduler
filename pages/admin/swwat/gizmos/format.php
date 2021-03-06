<?php // $Id: format.php 1071 2012-07-18 02:28:26Z preston $ Copyright (c) Preston C. Urka. All Rights Reserved.


function swwat_format_usdate($date)
{
    return date_format($date, 'd F, Y');
} // swwat_format_usdate

function swwat_format_usdatetime($date)
{
    return date_format($date, 'd F, Y  H:i  T');
} // swwat_format_usdatetime

function swwat_format_ussimpledatetime($date)
{
    return date_format($date, 'd F  H:i');
} // swwat_format_ussimpledatetime

function swwat_format_ustime($date)
{
    return date_format($date, 'H:i  T');
} // swwat_format_ustime

function swwat_format_ussimpletime($date)
{
    return date_format($date, 'H:i');
} // swwat_format_ustime

function swwat_format_isodate($date)
{
    return date_format($date, 'Y-m-d');
} // swwat_format_isodate

function swwat_format_isotime($date)
{
    return date_format($date, 'H:i:s');
} // swwat_format_isotime

function swwat_format_isodatetime($date)
{
    return date_format($date, 'Y-m-d H:i:s  T');
} // swwat_format_isodatetime

function swwat_format_expodate($startTime, $stopTime)
{
	$date1 = date_format($startTime, 'M j');
	$date2 = date_format($stopTime, 'M j');
	$year  = date_format($startTime, 'Y');

	return $date1.' - '.$date2.', '.$year;
} // swwat_format_expodate

function swwat_format_shift($startTime, $stopTime)
{
	$date1 = date_format($startTime, 'M j');
	$min1 = date_format($startTime, 'i');
	if (strcmp($min1,'00'))
	{
		$time1 = date_format($startTime, 'g:i A');
	}
	else
	{
		$time1 = date_format($startTime, 'g A');
	}
	$date2 = date_format($stopTime, 'M j');
	$min2 = date_format($stopTime, 'i');
	if (strcmp($min2,'00'))
	{
		$time2 = date_format($stopTime, 'g:i A');
	}
	else
	{
		$time2 = date_format($stopTime, 'g A');
	}
	$start = date_format($startTime, 'Y-m-d H:i:s');

	if ($date1 == $date2)
		return $date1.';'.$time1.' - '.$time2.';'.$start;
	else
		return $date1.' '.$time1.';'.' - '.$date2.' '.$time2.';'.$start;
} // swwat_format_shift

function swwat_format_shifttime($startTime, $stopTime)
{
	$stationDateTime = swwat_format_shift($startTime, $stopTime);
	list($stationDate, $stationTime, $start) = explode(';', $stationDateTime);

	return "(".$stationTime.", ".$stationDate.")";
} // swwat_format_shifttime

function swwat_format_phone($number)
{
    $area = 0;
    $exch = 0;
    $numb = 0;
    $n = sscanf('' . $number, '%3d%3d%4d', $area, $exch, $numb);
    return sprintf('%03d-%03d-%04d', $area, $exch, $numb);
} // swwat_format_phone

?>
