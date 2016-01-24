<?php // $Id: SendMessagePage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
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

    $originExpoFlag = FALSE;
    $originStationFlag = FALSE;
    $originWorkerFlag = FALSE;
    $originWorkerListFlag = FALSE;
    $author = getWorkerAuthenticated();
    $station = getStationCurrent();
    $expo = getExpoCurrent();
    $worker = getWorkerCurrent();

    if (is_null($station))
    {
        if (is_null($expo))
        {
            if (is_null($worker))
            {
                // done this way as PARAM_LIST may be reused
                $workerList = Worker::selectMultiple();
                // remove disabled
                $k = count($workerList);
                while ($k > 0)
                {
                    $k -= 1;
                    $worker = $workerList[$k];
                    if ($worker->isDisabled)
                    {
                        unset($workerList[$k]); // remove
                    }
                }
                $workerList = array_values($workerList); // reindex
                $originWorkerListFlag = TRUE;
            }
            else // from WorkerViewPage
            {
                $workerList = array();
                $workerList[0] = $worker; // currentworker
                $originWorkerFlag = TRUE;
            }
        }
        else
        {
            // done this way as PARAM_LIST may be reused
            $workerList = Worker::selectExpo($expo->expoid);
            $originExpoFlag = TRUE;
        }
    }
    else
    {
        // done this way as PARAM_LIST may be reused
        $workerList = Worker::selectStation($station->stationid);
        $originStationFlag = TRUE;
    }
    // this is the organizer get; need to finish with super/crew get
    if ($author->isCrewMember())
    {
        // remove all workers
        // remove Organizers if Station
        // remove Supervisors if Expo
        for ($k = 0; $k < count($workerList); $k--)
        {
            $w = $workerList[$k];
            if (($originStationFlag && $w->isSupervisor()) ||
                ($originExpoFlag    && $w->isOrganizer()))
            {
                continue;
            }
            unset($workerList[$k]); // remove in all other cases
        } // $k
    } // isCrewMember
    else if ($author->isSupervisor())
    {
        // remove all workers
        // remove Organizers if Station
        // remove Crew if Expo
        for ($k = 0; $k < count($workerList); $k--)
        {
            $w = $workerList[$k];
            if (($originStationFlag && $w->isStaff()) ||
                ($originExpoFlag    && $w->isOrganizer()))
            {
                continue;
            }
            unset($workerList[$k]); // remove in all other cases
        } // $k
    } // isSupervisorOnly
    // else Organizers already done
    $workerList = array_values($workerList); // re-index
    usort($workerList, "WorkerCompare");
    $_SESSION[PARAM_LIST] = $workerList;
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Send Message Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">

    <script src="swwat/js/validate.js"></script>

    <script type="text/javascript">
        /**
         * when SMS checked; change button to Send SMS Text, turn on 160 char check, hide subject line, de-select all without text addresses
         * when email checked; change button to Send Enauk, turn off 160 char check, display subject line, de-select all without email addresses
         */
        function selectType()
        {
            var select = document.sendmessagepage_form.elements['message_type'];
            alert(select);
        } // selectType
    </script>

</head>

<body>
<div id="container">

<?php

// ok, start the html
include('section/header.php');
?>

<div id="main">

    <?php
    if ($originExpoFlag)
    {
        include('section/LinkExpo.php');
    }
    if ($originStationFlag)
    {
        include('section/LinkStation.php');
    }
    if ($originWorkerFlag)
    {
        include('section/LinkWorker.php');
    }
    ?>

    <div id="sendmessagepage_send">
        <form method="POST" name="sendmessagepage_form" action="SendMessageAction.php">
            <?php
                echo '<input type="hidden" name="', PARAM_TYPE_MESSAGE, '" value="', PARAM_EMAIL, '"/>';
                //echo '<input type="radio" name="', PARAM_TYPE_MESSAGE, '" value="', PARAM_EMAIL, '" checked="checked"/>';
                //echo "Email Message<br/>\n";
                //echo '<input type="radio" name="', PARAM_TYPE_MESSAGE, '" value="', PARAM_SMS_SERVICE, '"/>';
                //echo "SMS Message<br/><br/>\n";
				echo "Subject: ";
                echo '<input type="text" name="', PARAM_SUBJECT_MESSAGE, '" value="" length="30"/>';
                echo "<p/>\n";
                echo '<textarea name="', PARAM_MESSAGE, '" rows="3" cols="40"></textarea>';
                echo "<br/><br/>\n";
                // todo - add javascript such that when sms is checked, the sendmessage button is greyed out if >160 characters
                // todo - hava javascript show error message in sms_overlength or similar
                // todo - create css errortext
                swwat_createInputSubmit(PARAM_SEND_MESSAGE, "Send Message");
                echo "<br/><br/>\n";
                createWorkerHTMLList($workerList, $author, "To:", $author->isOrganizer());
            ?>
        </form>
    </div><!-- sendmessagepage_send -->

</div><!-- main -->

<?php
    $menuItemArray = array();
    if ($author->isOrganizer())
    {
        $menuItemArray[] = MENU_VIEW_SITEADMIN;
    }
// todo - fix
//    $menuItemArray[] = MENU_XYZExpo_MESSAGE;
//    $menuItemArray[] = MENU_XYZStation_MESSAGE;
//    $menuItemArray[] = MENU_XYZWorker_MESSAGE;
    if ($author->isOrganizer())
    {
        $menuItemArray[] = MENU_VIEW_WORKERLIST;
    }
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
