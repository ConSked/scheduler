<?php  // $Id: parseCSV.php 1406 2012-08-24 16:37:52Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

class RegularException extends Exception
{
} // RegularException

class ParseCSVException extends RegularException
{
} // ParseCSVException

function parseCSV($fileString)
{
    $numCommas = substr_count($fileString, ',');
    if ($numCommas == 0)
    {
        throw new ParseCSVException("file has no commas at all");
    }

    $lines = str_getcsv($fileString, "\n");

    $lineNumber = 0;
    $countWords = 0;
    $lineArray = array();
    foreach($lines as $line)
    {
        $lineNumber += 1;
        $words = str_getcsv($line);
        if ($lineNumber == 1)
        {
            $countWords = count($words);
        }

        if (count($words) != $countWords)
        {
            throw new ParseCSVException("files lines have mismatching number of commas in line $lineNumber - $line");
        }

        array_push($lineArray, $words);
    }

    return $lineArray;
} // parseCSV($fileString)

?>
