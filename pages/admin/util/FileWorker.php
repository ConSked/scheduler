<?php  // $Id: FileWorker.php 1414 2012-08-24 20:54:34Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('swwat/gizmos/parse.php');
require_once('util/log.php');

abstract class FileWorker // extends Worker?? - better to keep separate
{
public $firstName;
public $lastName;
public $phone;
public $email;
public $index;

public function getError() // hopefully returns NULL for no error
{
    if (is_null($this->email))
    {
        return "line " . $this->index . " has no properly formatted email";
    }
    return NULL;
} // getError

public function getWarning() // hopefully returns NULL for no error
{
    if (is_null($this->phone) || is_null($this->firstName) || is_null($this->lastName))
    {
        $warning = "line " . $this->index . " is missing";
        if (is_null($this->firstName))
        {
            $warning .= " firstname,";
        }
        if (is_null($this->lastName))
        {
            $warning .= " lastName,";
        }
        if (is_null($this->phone))
        {
            $warning .= " phone,";
        }
        return substr($warning, 0, -1); // remove last comma
    }
    return NULL;
} // getWarning

// must be form of 10 digits somewhere
public static function parsePhone($value)
{
    try
    {
        return swwat_parse_phone($value);
    }
    catch (SWWATException $ex)
    {
        // ignore $ex
        return NULL;
    }
} // parsePhone

// must be form of something@somewhere.someextension
public static function parseEmail($value)
{
    $atIndex = strpos($value, '@');
    $pIndex = strrpos($value, '.');
    return ((FALSE != $atIndex) && (FALSE != $pIndex) && ($atIndex < $pIndex)) ? $value : NULL;
} // parseEmail

} // FiveDegreesCSV

?>
