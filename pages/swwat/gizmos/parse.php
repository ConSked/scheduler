<?php // $Id: parse.php 1274 2012-07-31 12:45:35Z preston $ Copyright (c) Preston C. Urka. All Rights Reserved.

require_once('swwat/exception.php');

class ParseSWWATException extends SWWATException
{
} // ParseSWWATException

class LengthSWWATException extends ParseSWWATException
{
} // LengthSWWATException

// replace PHP's substr
function swwat_substr($val, $len)
{
    if (is_null($val))
    {
        return NULL;
    }
    if (strlen($val) < abs($len))
    {
        return $val;
    }
    // If string is null, "", or less than or equal to start characters long, FALSE will be returned.
    return substr($val, $len);
} // swwat_substr

function swwat_parse_string($str, $blankOk = true)
{
    if (is_null($str))
    {
        if ($blankOk)  {  return NULL;  }
        throw new ParseSWWATException('parse_string:' . $str);
    }
    $s = trim($str);
    if ('' == $s)
    {
        if ($blankOk)  {  return NULL;  }
        throw new ParseSWWATException('parse_string:' . $str);
    }
    return $s;
} // swwat_parse_string

function swwat_parse_date($str, $blankOk = true)
{
    $str = swwat_parse_string($str, $blankOk);
    if (is_null($str))  {  return NULL;  } // note blankOk = false exception already thrown

    try
    {
        return date_create($str);
    }
    catch (Exception $e)
    {
        throw new ParseSWWATException('parse_date:' . $str);
    }
} // swwat_parse_date

function swwat_parse_datetime($str, $blankOk = true)
{
    $str = swwat_parse_string($str, $blankOk);
    if (is_null($str))  {  return NULL;  } // note blankOk = false exception already thrown

    try
    {
        return DateTime::createFromFormat("Y-m-d H:i:s", $str);
    }
    catch (Exception $e)
    {
        throw new ParseSWWATException('parse_datetime:' . $str);
    }
} // swwat_parse_datetime

function swwat_parse_time($str, $blankOk = true)
{
    $str = swwat_parse_string($str, $blankOk);
    if (is_null($str))  {  return NULL;  } // note blankOk = false exception already thrown

    try
    {
        return DateTime::createFromFormat("H:i:s", $str);
    }
    catch (Exception $e)
    {
        throw new ParseSWWATException('parse_time:' . $str);
    }
} // swwat_parse_time

// names will permit ,-'. i.e. O'Brien Smyth-Jones, the 3rd, M.D.
// // [A-z,-,',,,0-9, ] - throws exception otherwise
function swwat_parse_alpha($str, $blankOk = true)
{
    $str = swwat_parse_string($str, $blankOk);
    if (is_null($str))  {  return NULL;  } // note blankOk = false exception already thrown

    $chars = str_split($str);
    for ($j = 0; $j < count($chars); $j++)
    {
        if (!(ctype_alnum($chars[$j]) || ctype_space($chars[$j]) ||
            ('-' == $chars[$j]) || ('\'' == $chars[$j]) ||
            ('.' == $chars[$j]) || (',' == $chars[$j])))
        {
            throw new ParseSWWATException('parse_alpha:' . $str);
        }
    } // $j
    return $str;
} // swwat_parse_alpha

function swwat_parse_number($str, $blankOk = true)
{
    $str = swwat_parse_string($str, $blankOk);
    if (is_null($str))  {  return NULL;  } // note blankOk = false exception already thrown

    $anInt = 0;
    $n = sscanf($str, '%d', $anInt);
    if (1 != $n)
    {
        throw new ParseSWWATException('parse_number(int):' . $str);
    }
    return $anInt;
} // swwat_parse_number

function swwat_parse_integer($str, $len, $blankOk = true)
{
    $str = swwat_parse_string($str, $blankOk);
    if (is_null($str))  {  return NULL;  } // note blankOk = false exception already thrown

    if (strlen($str) > $len)
    {
        throw new LengthSWWATException('parse_integer:' . $str . " size:" . $len);
    }
    return swwat_parse_number($str, $blankOk);
} // swwat_parse_integer

function swwat_parse_phone($str, $blankOk = true)
{
    $str = swwat_parse_string($str, $blankOk);
    if (is_null($str))  {  return NULL;  } // note blankOk = false exception already thrown

    if (strlen($str) > 12)
    {
        throw new LengthSWWATException('parse_integer:' . $str . " size:12");
    }
    $area = 0;
    $exch = 0;
    $numb = 0;
    $n = sscanf($str, '%3d-%3d-%4d', $area, $exch, $numb);
    if (3 != $n)
    {
        throw new ParseSWWATException('parse_integer(int):' . $str);
    }
    $str = sprintf('%03d', $area) . sprintf('%03d', $exch) . sprintf('%04d', $numb);
    if (strlen($str) != 10)
    {
        throw new LengthSWWATException('parse_integer:' . $str . " size:10");
    }
    return $str;
} // swwat_parse_phone

function swwat_parse_double($str, $scrub = false, $blankOk = true)
{
    $str = swwat_parse_string($str, $blankOk);
    $inistr = $str;
    if (is_null($str))  {  return NULL;  } // note blankOk = false exception already thrown

    if ($scrub)
    {
        $str = scrub_money($str, $decimal);
    }
    $aDouble = 0.0;
    $n = sscanf($str, '%f', $aDouble);
    if (1 != $n)
    {
        throw new ParseSWWATException('parse_double:' . $inistr . " scrub:" . $scrub);
    }
    return $aDouble;
} // swwat_parse_double

function swwat_parse_enum($str, $enumArray, $blankOk = true)
{
    $str = swwat_parse_string($str, $blankOk);
    if (is_null($str))  {  return NULL;  } // note blankOk = false exception already thrown

    if (!in_array($str, $enumArray))
    {
        throw new ParseSWWATException('parse_enum:' . $str);
    }
    return $str;
} // swwat_parse_enum

?>
