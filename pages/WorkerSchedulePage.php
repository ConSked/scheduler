<?php // $Id: WorkerSchedulePage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/Worker.php');
require_once('section/Menu.php');
require_once('section/WorkerScheduleList.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');
require_once('util/session.php');

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Worker Schedule Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php

$author = getWorkerAuthenticated();
$worker = getWorkerCurrent();
if (isset($_REQUEST[MENU_VIEW_SCHEDULE]))
{
    // must have come from menu
    $worker = NULL;
}
if (is_null($worker))
{
    $worker = $author; // can always get self schedule
    setWorkerCurrent($worker);
}

$expo = getExpoCurrent();
if (is_null($expo))
{
    $expo = Expo::selectActive($worker->workerid); // note may be NULL
}

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php
    include('section/LinkExpoWorker.php');
    if (is_null($expo))
    {
        echo "<h4 class='fieldError'>You are not assigned to any future expos.</h4>";
    }
    else
    {
        setExpoCurrent($expo);
    ?>
	<div id="workerschedule_list">
        <h5>Expo Stations</h5>
        <div id="workerlist_table">
            <table>
                <tr>
                    <th class="rowTitle">Shift</th>
                    <th class="rowTitle">Location</th>
                    <th class="rowTitle">Date</th>
                    <th class="rowTitle">Time</th>
                    <th class="rowTitle">Instructions</th>
                    <th class="rowTitle">Hours</th>
                    <th class="rowTitle">Like</th>
                </tr>
                <?php createWorkerScheduleHTMLList($expo, $worker); ?>
            </table>
        </div><!-- workerlist_table -->
    </div><!-- workerschedule_list -->
    <?php
    }
    ?>
</div><!-- main -->

<?php
    $menuItemArray = array();
    if ($author->isOrganizer())
    {
        $menuItemArray[] = MENU_VIEW_SITEADMIN;
        $menuItemArray[] = MENU_VIEW_WORKERLIST;
    }
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
