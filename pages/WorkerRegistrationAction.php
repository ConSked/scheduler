<?php // $Id: WorkerRegistrationAction.php 1498 2012-08-27 01:28:30Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/Worker.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('swwat/gizmos/parse.php');

$author = getWorkerAuthenticated();
$indexArray = $_POST[PARAM_LIST_INDEX];
$expoList = $_SESSION[PARAM_LIST];
unset($_SESSION[PARAM_LIST]); // not needed anymore

foreach ($indexArray as $index)
{
    try
    {
        $index = swwat_parse_number(html_entity_decode($index), FALSE);
        $expo = $expoList[$index];
        if (is_null($expo))  {  continue;  } // try next
        $author->assignToExpo($expo->expoid);
    }
    catch (ParseSWWATException $ex)
    {
        // ignore; but means they aren't using the client
        header('Location: WorkerLoginPage.php');
        include('WorkerLoginPage.php');
        return;
    }
}
$expoList = NULL; // gc hint

header('Location: WorkerViewPage.php');
include('WorkerViewPage.php');
return;
?>
