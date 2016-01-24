<?php // $Id: StationDeletePage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/StationJob.php');
require_once('section/StationData.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/parse.php');
require_once('util/log.php');
require_once('util/session.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Station Delete Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
    <link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php

$expo = getExpoCurrent();
$station = getStationCurrent();
if (is_null($expo) || is_null($station)) // - someone goofed
{
    header('Location: WorkerLoginPage.php');
    include('WorkerLoginPage.php');
    return;
}

// ok, start the html
include('section/header.php');
?>

<div id="main">

    <?php include('section/LinkStation.php'); ?>

    <div id="stationdeletepage_stationdata">
        <form method="POST" name="stationdeletepage_stationdata_save" action="StationDeleteAction.php">
        <table>
            <tr style="vertical-align:top">
                <td><table><?php createStationDataHTMLRows($station, $expo, "stationdeletepage_stationdata_save", TRUE); ?></table></td>
                <td style="min-width:25px"><!-- spacer --></td>
                <td><table><?php createJobDataHTMLRows($station, "stationdeletepage_stationdata_save", FALSE, TRUE); ?></table></td>
            </tr>
            <tr><td colspan='3'>Are you sure you want to delete this station?&nbsp;&nbsp;
                    <input type="submit" name="<?php echo(PARAM_SAVE); ?>" value="Yes"/></td></tr>
        </table>
        </form>
    </div><!-- stationdeletepage_stationdata -->

</div><!-- main -->

<?php
    $menuItemArray = array();
    $menuItemArray[] = MENU_VIEW_SITEADMIN;
    $menuItemArray[] = MENU_VIEW_WORKERLIST;
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
