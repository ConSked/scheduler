<?php  // $Id: parseCSV.php 1406 2012-08-24 16:37:52Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

class RegularException extends Exception
{
} // RegularException

class ParseCSVException extends RegularException
{
} // ParseCSVException

function parseCSV($fileString)
{
    $lines = explode("\n", $fileString);
    $lineArray = array();

    $numCommas = -1;
    foreach ($lines as $line)
    {
        $words = explode(",", $line);
        $numCommas = count($words);
        if ($numCommas < 1)
        {
            throw new ParseCSVException("file has no commas at all");
        }
        break; // we have a basic count to compare
    } // $line
    $lineNumber = 0;
    foreach ($lines as $line)
    {
        if (strlen($line) > 0) // ignore empty (last) line
        {
            $words = explode(",", $line);
            $lineNumber += 1;
            // first validate each line has the same number of commas
            if (count($words) != $numCommas)
            {
                throw new ParseCSVException("files lines have mismatching number of commas in line $lineNumber - $line");
            }
            // second trim each word and add to array
            $wordArray = array();
            foreach ($words as $word)
            {
                $wordArray[] = trim($word);
            } // $word
            $lineArray[] = $wordArray;
        }
    } // $line

    return $lineArray;
} // parseCSV($fileString)

?>
