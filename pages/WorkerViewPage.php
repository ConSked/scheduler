<?php // $Id: WorkerViewPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('section/ExpoList.php');
require_once('section/Menu.php');
require_once('section/WorkerData.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');
require_once('util/RoleEnum.php');
require_once('util/session.php');

$author = getWorkerAuthenticated();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title><?php echo(SITE_NAME); ?> - Worker View Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php

//setStationCurrent(NULL);

if (isset($_REQUEST[MENU_VIEW_WORKER]))
{
    setWorkerCurrent($author);
}
// use REQUEST as may be a GET
else if (isset($_REQUEST[PARAM_LIST_INDEX]))
{
    $worker = getParamItem(PARAM_LIST, PARAM_LIST_INDEX);
    setWorkerCurrent($worker);
}
$_SESSION[PARAM_LIST] = NULL;
$worker = getWorkerCurrent();

if (!$author->isOrganizer())
{
    $worker = $author;
}
if (is_null($worker)) // was not set; indicates 'self' is seeking account; need AGAIN because of && above
{
    $worker = $author;
}
setWorkerCurrent($worker); // paranoia about some included section
$isAuthor = ($worker->workerid == $author->workerid); // login == current worker

logMessage("Worker:", $worker->workerid);
logMessage("Author:", $author->workerid);
// logMessage("phpinfo:", phpinfo());

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <table><tr style="vertical-align:top"><td>

    <div id="workerviewpage_workerdata">
        <?php
        createWorkerDataHTMLRows($worker, "", TRUE);
        if ($isAuthor)
        {
            // you can edit yourself
            echo '<form method="GET" name="workerviewpage_workerdata_form" action="WorkerEditPage.php">';
            echo '<input class="fieldValue" type="Submit" value="Edit"/></form>';
        }
        ?>
		<br />
    </div><!-- workerviewpage_workerdata -->


    </td><td style="min-width:25px"><!-- spacer --></td><td>
    <div id="workerviewpage_topright">

    <div id="workerviewpage_roles">
        <form method="POST" name="workerviewpage_roles_form" action="WorkerViewUpdateRoles.php">
        <table>
            <tr><td>
                <?php swwat_createRadioOption(PARAM_AUTHROLE, RoleEnum::$OPTION_CREWMEMBER, SWWAT_RADIO, $worker->isCrewMember(), !$author->isOrganizer()); ?>
            </td></tr>
            <tr><td>
                <?php swwat_createRadioOption(PARAM_AUTHROLE, RoleEnum::$OPTION_SUPERVISOR, SWWAT_RADIO, $worker->isSupervisor(), !$author->isOrganizer()); ?>
            </td></tr>
            <tr><td>
                <?php swwat_createRadioOption(PARAM_AUTHROLE, RoleEnum::$OPTION_ORGANIZER, SWWAT_RADIO, $worker->isOrganizer(), !$author->isOrganizer()); ?>
            </td></tr>
            <tr><td>
            <?php
            // ignore an empty row
            if ($author->isOrganizer() && ($author->workerid != $worker->workerid))
            {
                swwat_createInputSubmit(PARAM_UPDATEROLE, "Update Roles");
            }
            ?>
            </td></tr>
        </table>
        </form>
		<br />
    </div><!-- workerviewpage_roles -->

    <div id="workerviewpage_disable">
        <table><tr><td>
        <?php if ($author->isOrganizer() && ($author->workerid != $worker->workerid))
            {
                if ($worker->isDisabled) /* enable*/
                {
                    echo '<form method="GET" name="workerviewpage_enable_form" action="WorkerDisableAction.php">';
                    swwat_createInputSubmit(PARAM_DISABLED, "Enable Login");
                    echo '</form>';
                }
                else /* disable */
                {
                    echo '<form method="GET" name="workerviewpage_disable_form" action="WorkerDisableConfirmPage.php">';
                    swwat_createInputSubmit(PARAM_DISABLED, "Disable Login");
                    echo '</form>';
                }
            } /* isOrganizer */
        ?>
        </td></tr></table>
		<br />
    </div><!-- workerviewpage_disable -->

    <div id="workerviewpage_password">
        <table><tr>
            <td>
                <?php
                if ($isAuthor) // implicit and if isOrganizer! due to else below
                {
                    echo '<form method="GET" name="workerviewpage_pwchange_form" action="WorkerLoginChangePage.php">
                          <input type="submit" value="Change Password"/></form>';
                }
                else if ($author->isOrganizer())
                {
                    echo '<form method="POSGETT" name="workerviewpage_pwreset_form" action="WorkerViewResetAction.php">
                          <input type="submit" name="pwreset" value="Reset Password"/></form>';
                }
                ?>
            </td></tr>
            <tr><td>
                <?php echo htmlspecialchars($worker->lastLoginTime); ?>
            </td></tr>
        </tr></table>
		<br />
    </div><!-- workerviewpage_password -->

    </div><!-- workerviewpage_topright -->
    </td></tr></table>

    <?php if ($author->isOrganizer() && ($author != $worker)) { ?>
    <div id="workerviewpage_schedule">
        <table><tr><td>
            <form method="GET" name="workerviewpage_schedule_form" action="WorkerSchedulePage.php">
                <input type="submit" name="schedule" value="View Schedule"/>
            </form>
        </td></tr></table>
		<br />
    </div><!-- workerviewpage_schedule -->
    <?php } ?>

    <div id="workerviewpage_expohistory">
        <h5>Expo History</h5>
        <?php
            $expoList = Expo::selectWorker($worker->workerid); // needed for createExpoHTMLList
            usort($expoList, "ExpoCompare");
            createWorkerExpoHTMLList($expoList, $worker, $author->isOrganizer());
        ?>
    </div><!-- workerviewpage_expohistory -->
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
