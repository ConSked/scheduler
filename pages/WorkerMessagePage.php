<?php // $Id: WorkerMessagePage.php 1951 2012-09-13 19:57:12Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('db/StationJob.php');
require_once('db/Expo.php');
require_once('section/Menu.php');
require_once('section/WorkerList.php');
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

    <title><?php echo(SITE_NAME); ?> - Message Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php

// ok, start the html
include('section/header.php');
?>

<div id="main">

    <div id="sendmessagepage_send">
        <form method="POST" name="sendmessagepage_form" action="WorkerMessageAction.php">
            <?php
				echo "Subject: ";
                echo '<input type="text" name="', PARAM_SUBJECT_MESSAGE, '" value="" length="30"/>';
                echo "<p/>\n";
                echo '<textarea name="', PARAM_MESSAGE, '" rows="3" cols="40"></textarea>';
                echo "<br/><br/>\n";
                swwat_createInputSubmit(PARAM_SEND_MESSAGE, "Send Message");
            ?>
        </form>
    </div><!-- sendmessagepage_send -->

</div><!-- main -->

<?php
    Menu::addMenu(array());
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
