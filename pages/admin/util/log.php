<?php  // $Id: log.php 2382 2012-10-15 23:04:08Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');

function logMessage($code, $message)
{
    $date = strftime("%c", time());
    $stringdata = $date . " - " . $code . ": " . $message;
    //echo $stringdata . "\n";

    $sdr = NULL;
    if(is_null(parse_url(BASE_URL, PHP_URL_PATH)))
    {
      if (isset($_SERVER['DOCUMENT_ROOT']))
      {
        $sdr = $_SERVER['DOCUMENT_ROOT'];
      }
    }
    else
    {
        $iurlinfo = parse_url($url);
        $host = $info['host'];
//        $sdr = parse_url(BASE_URL, PHP_URL_PATH);
//if($host=="swiftexpos.com")
{
  $sdr = "/home/swifts6/public_html/SwiftExpos.com/";
}
        $sdr .= parse_url(BASE_URL, PHP_URL_PATH);

    }
    if (is_null($sdr) || (0 == strlen($sdr)))
    {
        $sdr = ".";
    }
    $sdr .= "/swiftshift.log";

    $fwhandle = fopen($sdr, "a+");
    fwrite($fwhandle, $stringdata . "\n");
    fclose($fwhandle);
} // logMessage($code, $message)

?>
