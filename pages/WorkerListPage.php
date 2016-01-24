<?php // $Id: WorkerListPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');
require_once('properties/constants.php');
require_once('util/session.php');
require_once('util/log.php');

$author = getWorkerAuthenticated();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Worker List Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php
require_once('db/Worker.php');
require_once('section/Menu.php');
require_once('section/WorkerList.php');
require_once('swwat/gizmos/format.php');

//setExpoCurrent(NULL);
//setStationCurrent(NULL);

$workerList = Worker::selectMultiple();
// should be in order for display
usort($workerList, "WorkerCompare");
$_SESSION[PARAM_LIST] = $workerList;
setWorkerCurrent(NULL); // set null wherever param_list set to workers
$_REQUEST[PARAM_LIST_INDEX] = NULL;

// ok, start the html
include('section/header.php');
?>

<div id="main">

    <div id="workerlistpage_filters">
    </div><!-- workerlistpage_filters -->

    <?php
        createWorkerHTMLList($workerList, $author);
    ?>
</div><!-- main -->

<?php
    $menuItemArray = array();
    $menuItemArray[] = MENU_VIEW_SITEADMIN;
    $menuItemArray[] = MENU_SEND_MESSAGE;

    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
