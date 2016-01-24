<?php // $Id: SiteAdminPage.php 2433 2012-11-30 16:27:39Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');
require_once('properties/constants.php');
require_once('util/log.php');
require_once('util/session.php');

$author = getWorkerAuthenticated();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Site Admin Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php
require_once('db/Expo.php');
require_once('db/Worker.php');
require_once('section/ExpoList.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/format.php');
require_once('util/date.php');

setWorkerCurrent(NULL);

$expoList = Expo::selectMultiple();
// should be in order for display
usort($expoList, "ExpoCompare");
$_SESSION[PARAM_LIST] = $expoList;
setExpoCurrent(NULL);
$_REQUEST[PARAM_LIST_INDEX] = NULL;

setStationCurrent(NULL);

// ok, start the html
include('section/header.php');
?>

<div id="main">

    <div id="expolistpage_filters">
    </div><!-- expolistpage_filters -->

    <?php
		echo "<form method=\"GET\" name=\"expoviewpage_newstation_form\" action=\"ExpoEditPage.php\">\n";
		echo "<input class=\"fieldValue\" type=\"Submit\" value=\"Create New Expo\"/>\n</form>\n";
        createExpoHTMLList($expoList, $author->isOrganizer());
    ?>

</div><!-- main -->

<?php
    $menuItemArray = array();
    $menuItemArray[] = MENU_VIEW_WORKERLIST;
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
