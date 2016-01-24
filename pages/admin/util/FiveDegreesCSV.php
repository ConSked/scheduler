<?php  // $Id: FiveDegreesCSV.php 1680 2012-09-03 21:48:34Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('swwat/gizmos/parse.php');
require_once('util/FileWorker.php');
require_once('util/log.php');
require_once('util/parseCSV.php');

class FiveDegreesException extends ParseCSVSwiftShiftException  {  }

class FiveDegreesCSV extends FileWorker // extends Worker?? - better to keep separate
{
const VERIFIED = "VERIFIED";
public $skillArray = array();

public function getError() // hopefully returns NULL for no error
{
    $parentError = parent::getError();
    if (is_null($parentError))
    {
        foreach($this->skillArray as $skill)
        {
            if (0 == strcasecmp(self::VERIFIED, $skill))
            {
                return NULL;
            }
        }
        // else
        return "line " . $this->index . " has no #VERIFIED tag";
    }
    return $parentError;
} // getError

private static function searchHeader($needle, $haystack)
{
    $index = array_search($needle, $haystack);
    if (FALSE == $index)
    {
        $index = array_search('"' . $needle . '"', $haystack);
    }
    if (FALSE == $index)
    {
        throw new FiveDegreesException("5 Degrees CSV file is missing $needle header");
    }
    return $index;
} // searchHeader

private static function trimQ($value)
{
    // removes quotes; does not re-trim
    return str_replace('"', '', $value);
} // trimQ

public static function parse($fileString)
{
    // parse the file
    $lineArray = parseCSV($fileString);

    // get the indices per header line
    $header = $lineArray[0];
    $firstNameIndex = self::searchHeader("FirstName", $header);
    $lastNameIndex = self::searchHeader("LastName", $header);
    $tagsIndex = self::searchHeader("Tags", $header);
    $phone1Index = self::searchHeader("Phone 1", $header);
    $phone2Index = self::searchHeader("Phone 2", $header);
    $phone3Index = self::searchHeader("Phone 3", $header);
    $email1Index = self::searchHeader("Email 1", $header);
    $email2Index = self::searchHeader("Email 2", $header);
    $email3Index = self::searchHeader("Email 3", $header);

    // get each line then
    $fiveDegreesArray = array();
    for ($index = 1; $index < count($lineArray); $index++)
    {
        $line = $lineArray[$index];
        $fifthDegree = new FiveDegreesCSV();
        $fifthDegree->firstName = self::trimQ($line[$firstNameIndex]);
        $fifthDegree->lastName = self::trimQ($line[$lastNameIndex]);
        $fifthDegree->skillArray = self::parseTags(self::trimQ($line[$tagsIndex]));
        $fifthDegree->phone = self::parsePhone3(self::trimQ($line[$phone1Index]), self::trimQ($line[$phone2Index]), self::trimQ($line[$phone3Index]));
        $fifthDegree->email = self::parseEmail3(self::trimQ($line[$email1Index]), self::trimQ($line[$email2Index]), self::trimQ($line[$email3Index]));
        $fifthDegree->index = $index;

        $fiveDegreesArray[] = $fifthDegree;
    } // $index

    return $fiveDegreesArray;
} // parse

private static function parseTags($value)
{
    $hashes = explode("#", $value);
    $skills = array();
    foreach ($hashes as $word)
    {
        $skills[] = trim($word);
    }
    return $skills;
} // parseTags

private static function parsePhone3($value1, $value2, $value3)
{
    $value = self::parsePhone($value1);
    if (!is_null($value))  {  return $value;  }

    $value = self::parsePhone($value2);
    if (!is_null($value))  {  return $value;  }

    $value = self::parsePhone($value3);
    return $value;
} // parsePhone3

private static function parseEmail3($value1, $value2, $value3)
{
    $value = self::parseEmail($value1);
    if (!is_null($value))  {  return $value;  }

    $value = self::parseEmail($value2);
    if (!is_null($value))  {  return $value;  }

    $value = self::parseEmail($value3);
    return $value;
} // parseEmail3

} // FiveDegreesCSV

?>
