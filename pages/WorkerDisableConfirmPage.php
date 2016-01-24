<?php // $Id: WorkerDisableConfirmPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/ShiftAssignmentView.php');
require_once('db/Worker.php');
require_once('section/ExpoData.php');
require_once('section/Menu.php');
require_once('section/WorkerData.php');
require_once('section/WorkerStation.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');
require_once('util/session.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Worker Disable Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php

$worker = getWorkerCurrent();


// ok, start the html
include('section/header.php');
?>

<div id="main">
    <div id="workerdisableconfirmpage_workerdata">
        <?php
        createWorkerDataHTMLRows($worker, "", TRUE);
        ?>
		<br />
    </div><!-- workerdisableconfirmpage_workerdata -->

    <div id="workerdisableconfirmpage_confirm">
            <table class="fieldValue">
            <tr class="rowTitle">
                <th>Expo</th><th>Station</th><th>Job</th><th>Start</th><th>Stop</th><th>Supers</th><th>Crew</th>
            </tr>
            <?php
                $assnArray = ShiftAssignmentView::selectWorker(NULL, $worker->workerid);
                createWorkerStationHTMLRows($worker, $assnArray, FALSE, TRUE);
            ?>
            </table>
            <form method="POST" name="workerdisableconfirmpage_disable_form" action="WorkerDisableAction.php">
            <?php
                // maintain compatibility with WorkerViewPage
                swwat_createInputSubmit(PARAM_DISABLED, "Confirm Disable Login");
            ?>
            </form>
    </div><!-- workerdisableconfirmpage_confirm -->

</div><!-- main -->

<?php
    $menuItemArray = array();
    $menuItemArray[] = MENU_VIEW_SITEADMIN;
    $menuItemArray[] = MENU_SEND_MESSAGE;
    $menuItemArray[] = MENU_VIEW_WORKERLIST;

    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
