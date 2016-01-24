<?php // $Id: ScheduleExpoPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('util/ScheduleEnum.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title>SwiftShift - Schedule Expo Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
	<link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script type="text/javascript">
		function scheduleExpoDelete()
		{
			var ans = confirm("Do you wish to delete all shifts (that are in the future) for this expo?");
			if (ans == true)
			{
				window.location.replace("ScheduleExpoDeleteAction.php");
			}
		}
	</script>
</head>

<body>
<div id="container">

<?php

if (isset($_SESSION[PARAM_SCHEDULE_PUBLISH]))
{
    $algorithm = $_SESSION[PARAM_SCHEDULE_ALGO];
    $keepFlag = isset($_SESSION[PARAM_SCHEDULE_KEEP]);
}
else // defaults
{
    $algorithm = ASSIGNANDSUBTRACT;
    $keepFlag = TRUE;
}
unset($_SESSION[PARAM_SCHEDULE_ALGO]);
unset($_SESSION[PARAM_SCHEDULE_KEEP]);

// ok, start the html
include('section/header.php');

?>

<div id="main">

    <?php include('section/LinkExpo.php'); ?>

    <div id="scheduleexpopage_parameters">
        <h5>Schedule Parameters</h5>
        <form method="POST" name="scheduleexpopage_scheduleexpo_form" action="ScheduleExpoAction.php">
            <table>
            <tr>
                <td class='fieldValueFirst'>Algorithm</td>
                <td class='fieldValue'><?php swwat_createSelect(PARAM_SCHEDULE_ALGO, ScheduleEnum::$OPTIONS, $algorithm, FALSE); ?></td>
            </tr>
            </table>

            <input class="fieldValue" type="Submit" value="Run Algorithm"/>
        </form>
    </div><!-- scheduleexpopage_parameters -->

    <div id="scheduleexpopage_results">
        <h5>Schedule Results</h5>
        <form method="POST" name="scheduleexpopage_scheduleexpo_form" action="ScheduleExpoAction.php">
            <?php
                $disableAttr = isset($_SESSION[PARAM_SCHEDULE_PUBLISH]) ? "" : " disabled='disabled'";
                echo "<input class='fieldValue' type='Submit' name='" . PARAM_SCHEDULE_PUBLISH . "' value='Save Schedule'$disableAttr/>\n";
            ?>
        </form>
        <?php
        if (isset($_SESSION[PARAM_PAGE_MESSAGE]))
        {
            echo $_SESSION[PARAM_PAGE_MESSAGE];
            unset($_SESSION[PARAM_PAGE_MESSAGE]);
        }
        ?>
		<p />
        <input class="fieldValue" type="Submit" name="<?php echo(PARAM_DELETE); ?>" value="Delete Schedule" onclick = "scheduleExpoDelete()"/>
		<?php
		if (isset($_SESSION[PARAM_DELETE]))
		{
			echo("&nbsp;&nbsp;<span class=\"fieldError\">Shifts deleted</span>");
			unset($_SESSION[PARAM_DELETE]);
		}
		?>
    </div><!-- scheduleexpopage_results -->

    <div id="scheduleexpopage_list">
        <h5>Algorithm Descriptions</h5>
        <table>
            <tr>
                <th class='rowTitle'>Algorithm</th>
                <th class='rowTitle'>Explanation</th>
            </tr>
            <tr>
                <td class='fieldValueFirst'>Assign and Subtract</td>
                <td class='fieldValue'>Station-centric assignment which attempts to reach an optimal happiness balance across stations.</td>
            </tr>
            <tr>
                <td class='fieldValueFirst'>First-come, First Serve</td>
                <td class='fieldValue'>Worker-centric assignment in simple order of station preference.</td>
            </tr>
            <tr>
                <td class='fieldValueFirst'>First-come, Location Locked</td>
                <td class='fieldValue'>Worker-centric assignment in simple order of station preference, but 'locks' the location.</td>
            </tr>
            <tr>
                <td class='fieldValueFirst'>First-come, Soft Location Locked</td>
                <td class='fieldValue'>Worker-centric assignment in simple order of station preference, but gives preferential assignment to the same the location.</td>
            </tr>
        </table>
    </div><!-- scheduleexpopage_parameters -->

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
