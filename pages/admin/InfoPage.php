<?php // $Id: InfoPage.php 1626 2012-08-31 20:36:53Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');
require_once('properties/constants.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('db/Worker.php');

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Technical Info Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

// ok, start the html
include('section/header.php');
?>

<div id="main">

<?php phpinfo(); ?>

</div><!-- main -->

<?php
    $menuItemArray = array();
    $menuItemArray[] = MENU_VIEW_ExpoLIST;
    $menuItemArray[] = MENU_VIEW_WORKERLIST;
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
