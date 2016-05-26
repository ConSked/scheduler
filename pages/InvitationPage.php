<?php // $Id: InvitationPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');
require_once('db/Expo.php');
require_once('properties/constants.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/html.php');
require_once('swwat/gizmos/parse.php');
require_once('util/log.php');
require_once('util/session.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title><?php echo(SITE_NAME); ?> - Invitation Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
	<link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script src="jquery/jquery-1.7.2.min.js"></script>
	<script src="jquery/jquery-ui-1.8.20.custom.min.js"></script>

	<script type="text/javascript">
		var DISPLAY_FORMAT = 'DD dd, MM yy';
		var DB_FORMAT = 'yy-mm-dd';

		function init()
		{
			var date          = $('[name=<?php echo PARAM_STOPTIME;?>]').val();
			date = $.datepicker.parseDate('DD dd, MM yy', date).toDateString();
			return;
		} // init

		function descriptionCheck(param1, param2)
		{
			var param3 = '<?php echo PARAM_DESCRIPTION; ?>';
			textCheck(param1, param2, param3);
			return;
		} // descriptionCheck

		$(document).ready(function()
		{
			var today = new Date();

			$.datepicker.setDefaults({ defaultDate:null });
			$.datepicker.setDefaults({ minDate:today });
			$.datepicker.setDefaults({ dateFormat:DISPLAY_FORMAT });
			var stdate = $('[name=<?php echo PARAM_STOPTIME;?>]');
			var d = $.datepicker.parseDate(DB_FORMAT, stdate.val());
			stdate.datepicker();
			stdate.datepicker("setDate", d);
			$('#invitationpage_invite').submit(function()
			{
				var d = stdate.datepicker("getDate");
				stdate.val($.datepicker.formatDate(DB_FORMAT, d));
			}); // submit
		}); // ready

	</script>
</head>

<body onload="init()">
<div id="container">

<?php

$expo = getExpoCurrent();
unset($_SESSION[PARAM_UPLOAD]);
if (!isset($_POST[PARAM_SAVE])) // set defaults
{
    $expDate = $expo->startTime; // default
    $_POST[PARAM_WITHCODE] = PARAM_WITHCODE;
    unset($_POST[PARAM_UNIQUE]);
}
else
{
    $expDate = swwat_parse_date(html_entity_decode($_POST[PARAM_STOPTIME]), true);
}
$email = isset($_POST[PARAM_EMAIL]) ? swwat_parse_string(html_entity_decode($_REQUEST[PARAM_EMAIL]), true) : NULL;
$withCode = isset($_POST[PARAM_WITHCODE]);
$uniqueCode = isset($_POST[PARAM_UNIQUE]);

if (is_null($expDate))
{
    $expDate = $expo->startTime; // default
}

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php
    if (!is_null($expo->expoid))
    {
        include('section/LinkExpo.php');
    }
    ?>

	<div id="invitationpage_data">
		<form method="POST" id="invitationpage_invite" action="InvitationAction.php">
		<table>
            <tr><td class="fieldTitle">Date Invitation Expires:</td>
                <td><input type="text" name="<?php echo PARAM_STOPTIME; ?>" value="<?php echo swwat_format_isodate($expDate); ?>" size="25"/></td>
            </tr>
            <tr><td class="fieldTitle">Require generic code:</td>
                <td>
                    <?php swwat_createInputHidden(PARAM_WITHCODE, FALSE); ?>
                    <?php swwat_createRadioOption(PARAM_WITHCODE, array(PARAM_WITHCODE, ""), SWWAT_CHECKBOX, $withCode, FALSE); ?>
                </td>
            </tr>
            <tr><td class="fieldTitle">Make code unique:</td>
                <td>
                    <?php swwat_createInputHidden(PARAM_UNIQUE, FALSE); ?>
                    <?php swwat_createRadioOption(PARAM_UNIQUE, array(PARAM_UNIQUE, ""), SWWAT_CHECKBOX, $uniqueCode, FALSE); ?>
                </td>
            </tr>
			<tr><td><?php swwat_createInputSubmit(PARAM_UPLOAD, "Upload File"); ?></td></tr>
            <tr><td></td></tr>
            <tr><td class="fieldTitle">List of emails</td><td>(white-space separated)</td></tr>
                        <tr><td colspan="2">
                    <textarea name="<?php echo PARAM_EMAIL; ?>"
                              value="<?php echo htmlspecialchars($email); ?>"
                              rows="5"></textarea></td></tr>            <tr><td></td></tr>
                        <tr><td><?php swwat_createInputSubmit(PARAM_SAVE, "Invite"); ?></td></tr>
		</table>
		</form>
	</div><!-- invitationpage_data -->

    <div id="invitation_results">
        <h5>Invitation Results</h5>
        <table>
            <tr><th class='rowTitle'>line#</th><th class='rowTitle'>email</th><th class='rowTitle'>data</th><th class='rowTitle'>message</th><th class='rowTitle'>note</th></tr>
            <?php
            // get rid of annoying notice
            if (isset($_SESSION[PARAM_MESSAGE]))
            {
                echo $_SESSION[PARAM_MESSAGE];
            }
            ?>
        </table>
    </div>

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
