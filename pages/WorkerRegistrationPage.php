<?php // $Id: WorkerRegistrationPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('db/Expo.php');
require_once('db/Invitation.php');
require_once('db/Worker.php');
require_once('properties/constants.php');
require_once('section/ExpoList.php');
require_once('section/Menu.php');
require_once('util/log.php');
require_once('util/session.php');
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<title>SwiftShift - Worker Registration Page</title>
	<link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php
$author = getWorkerAuthenticated();
$invitationList = Invitation::selectWorker($author->workerid);
$expoList = array();
foreach($invitationList as $invitation)
{
    if (!is_null($invitation->expoid))
    {
        $expoList[] = Expo::selectID($invitation->expoid);
    }
}
$_SESSION[PARAM_LIST] = $expoList;
//setExpoCurrent(NULL);
$invitationList = NULL; // gc hint
// ok, start the html
include('section/header.php');
?>


<div id="main">

    <div id="workerregistrationpage_register">
        <h5>Register for Expos</h5>
        <p>After registration, you will be able to set your Expo preferences.</p>
        <form method="POST" name="workerregistrationpage_form" action="WorkerRegistrationAction.php">
            <table>
                <tr><th class='rowTitle'>Expo</th>
                    <th class='rowTitle'>Start</th>
                    <th class='rowTitle'>Stop</th>
                    <th class='rowTitle'>Accept</th></tr>
                <?php
                for ($k = 0; $k < count($expoList); $k++)
                {
                    makeExpoRegistrationRow($expoList[$k], $k); // uses PARAM_MULTIPLE_INDEX
                } // $k
                ?>
                <tr><td colspan="4"><?php swwat_createInputSubmit(PARAM_SAVE, "Save"); ?></td></tr>
            </table>
        </form>
    </div><!-- workerregistrationpage -->

</div><!-- main -->

<?php
    $menuItemArray = array();
    if ($author->isOrganizer())
    {
        $menuItemArray[] = MENU_VIEW_SITEADMIN;
        $menuItemArray[] = MENU_SEND_MESSAGE;
        $menuItemArray[] = MENU_VIEW_WORKERLIST;
    }
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
