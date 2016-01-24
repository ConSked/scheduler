<?php // $Id: InvitationFileUploadPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');
require_once('db/Expo.php');
require_once('properties/constants.php');
require_once('section/FileUpload.php');
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

    <title>SwiftShift - Invitation File Upload Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
	<link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script src="jquery/jquery-1.7.2.min.js"></script>
	<script src="jquery/jquery-ui-1.8.20.custom.min.js"></script>

	<script type="text/javascript">
		var DISPLAY_FORMAT = 'DD dd, MM yy';
		var DB_FORMAT = 'yy-mm-dd';

		function init()
		{
			var stdate = $('[name=<?php echo PARAM_STOPTIME;?>]');
			var d = $.datepicker.parseDate(DB_FORMAT, stdate.val());
            stdate.val($.datepicker.formatDate(DISPLAY_FORMAT, d));
		}; // init

	</script>
</head>

<body onload="init()">
<div id="container">

<?php

$expo = getExpoCurrent();
$expDate = swwat_parse_date(html_entity_decode($_SESSION[PARAM_STOPTIME]), true);
if (is_null($expDate))
{
    $expDate = $expo->startTime; // default
    $_SESSION[PARAM_STOPTIME] = $expDate;
}
$withCode = isset($_SESSION[PARAM_WITHCODE]);
$uniqueCode = isset($_SESSION[PARAM_UNIQUE]);

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

	<div id="invitation_fileupload">
		<!-- form method="POST" id="invitationpage_invite" -->
		<table>
            <tr><td class="fieldTitle">Date Invitation Expires:</td>
                <td><input readonly="readonly" type="text" name="<?php echo PARAM_STOPTIME; ?>" value="<?php echo swwat_format_isodate($expDate); ?>" size="25"/></td>
            </tr>
            <tr><td class="fieldTitle">Require generic code:</td>
                <td><?php swwat_createRadioOption(PARAM_WITHCODE, array(PARAM_WITHCODE, ""), SWWAT_CHECKBOX, $withCode, TRUE); ?></td>
            </tr>
            <tr><td class="fieldTitle">Make code unique:</td>
                <td><?php swwat_createRadioOption(PARAM_UNIQUE, array(PARAM_UNIQUE, ""), SWWAT_CHECKBOX, $uniqueCode, TRUE); ?></td>
            </tr>
            <tr><td></td><td></td></tr>
            <tr><td class="fieldTitle">Upload File:</td><td><?php createFileUploadForm("InvitationFileUploadAction.php", PARAM_DOCUMENT); ?></td></tr>
		</table>
		<!-- /form -->
	</div><!-- invitation_fileupload -->

    <div id="invitation_fileupload_results">
        <h5>File Invitation Results</h5>
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
