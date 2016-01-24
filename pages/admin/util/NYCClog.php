<?php  // $Id: NYCClog.php 2121 2012-09-21 16:13:12Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

function logMessage($code, $message)
{
    $date = strftime("%c", time());
    $stringdata = $date . " - " . $code . ": " . $message;
    //echo $stringdata . "\n";

    $sdr = NULL;
    if (isset($_SERVER['DOCUMENT_ROOT']))
    {
        $sdr = $_SERVER['DOCUMENT_ROOT'];
    }
    if (is_null($sdr) || (0 == strlen($sdr)))
    {
        $sdr = ".";
    }
    $sdr .= "/NYCC/swiftshift.log";

    $fwhandle = fopen($sdr, "a+");
    fwrite($fwhandle, $stringdata . "\n");
    fclose($fwhandle);
} // logMessage($code, $message)

?>
