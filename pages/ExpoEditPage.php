<?php // $Id: ExpoEditPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('section/ExpoData.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');
require_once('util/session.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<title><?php echo(SITE_NAME); ?> - Expo Edit Page</title>
	<link href="css/site.css" rel="stylesheet" type="text/css">
	<link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script src="jquery/jquery-1.7.2.min.js"></script>
	<script src="jquery/jquery-ui-1.8.20.custom.min.js"></script>

	<script type="text/javascript">
		var DISPLAY_FORMAT = 'DD dd, MM yy';
		var DB_FORMAT = 'yy-mm-dd';

		function init()
		{
			var title       = $('[name=<?php echo PARAM_TITLE;?>]').val();
			var description = $('[name=<?php echo PARAM_DESCRIPTION;?>]').val();
			var maxhours    = $('[name=<?php echo PARAM_MAXHOURS;?>]').val();
			var starttime   = $('#<?php echo PARAM_STARTTIME;?>').val();
			var stoptime    = $('#<?php echo PARAM_STOPTIME;?>').val();

			if (title.length === 0 || description.length === 0 || maxhours.length === 0 || starttime.length === 0 || stoptime.length === 0)
			{
				$('#<?php echo PARAM_SAVE;?>').attr("disabled", "disabled");
			}
			return;
		} // init

		function titleCheck(param1, param2)
		{
			var description = param1.value;
			var logic = (description.length === 0 || description.length > param2);

			$('#title').remove();
			if (logic)
			{
				if ($('#title').length === 0)
				{
					if (description.length === 0)
					{
						$('[name=<?php echo PARAM_TITLE;?>]').after('  <span id="title" class="fieldError">Title is a required field.</span>');
					}
					else
					{
						$('[name=<?php echo PARAM_TITLE;?>]').after('  <span id="title" class="fieldError">Title is over 255 characters.</span>');
					}
				}
			}
			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // titleCheck

		function descriptionCheck(param1, param2)
		{
			var description = param1.value;
			var logic = (description.length === 0 || description.length > param2);

			$('#description').remove();
			if (logic)
			{
				if ($('#description').length === 0)
				{
					if (description.length === 0)
					{
						$('[name=<?php echo PARAM_DESCRIPTION;?>]').after('  <span id="description" class="fieldError">Description is a required field.</span>');
					}
					else
					{
						$('[name=<?php echo PARAM_DESCRIPTION;?>]').after('  <span id="description" class="fieldError">Description is over 255 characters.</span>');
					}
				}
			}
			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // descriptionCheck

		function maxhoursCheck(param1, param2)
		{
			var maxhours = param1.value;
			var logic = (maxhours.length === 0 || Math.ceil(maxhours, 10) != Math.floor(maxhours, 10) || parseInt(maxhours, 10) < 0);

			$('#maxhours').remove();
			if (logic)
			{
				if ($('#maxhours').length === 0)
				{
					if (maxhours.length === 0)
					{
						$('[name=<?php echo PARAM_MAXHOURS;?>]').after('  <span id="maxhours" class="fieldError">Max Hours is a required field.</span>');
					}
					else
					{
						$('[name=<?php echo PARAM_MAXHOURS;?>]').after('  <span id="maxhours" class="fieldError">Max Hours is not a positive integer.</span>');
					}
				}
			}
			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // maxhoursCheck

		function dateCheck()
		{
			var earlier = $('#<?php echo PARAM_STARTTIME;?>').datepicker("getDate");
			var later   = $('#<?php echo PARAM_STOPTIME;?>').datepicker("getDate");
			var logic   = (earlier.getTime() > later.getTime());

			$('#dates').remove();
			if (logic)
			{
				$('[name=<?php echo PARAM_STOPTIME;?>]').after('  <span id="dates" class="fieldError">Stop Time is before Start Time.</span>');
			}

			// disable if true
			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // dateCheck

		$(document).ready(function()
		{
			var starttime = $('#<?php echo PARAM_STARTTIME;?>');
			var stoptime = $('#<?php echo PARAM_STOPTIME;?>');

			var start = $.datepicker.parseDate(DB_FORMAT, starttime.val());
			var stop  = $.datepicker.parseDate(DB_FORMAT, stoptime.val());
			var cdate = new Date();

			var minimum = 0;
			if (cdate > start && cdate < stop)
			{
				minimum = start;
			}

			$.datepicker.setDefaults({ defaultDate:null });
			$.datepicker.setDefaults({ minDate:minimum });
			$.datepicker.setDefaults({ dateFormat:DISPLAY_FORMAT });

			var stoptime = $('#<?php echo PARAM_STOPTIME;?>');

			starttime.datepicker();
			starttime.datepicker("setDate", start);

			stoptime.datepicker();
			stoptime.datepicker("setDate", stop);

			$('#expoeditpage_expodata_save').submit(function()
			{
				var d = starttime.datepicker("getDate");
				starttime.val($.datepicker.formatDate(DB_FORMAT, d));

				var d = stoptime.datepicker("getDate");
				stoptime.val($.datepicker.formatDate(DB_FORMAT, d));
			}); // submit

			starttime.change(dateCheck); // change
			stoptime.change(dateCheck); // change

		}); // ready

</script>
</head>

<body onload="init()">
<div id="container">

<?php

$expo = getExpoCurrent();
$editFlag = TRUE;
if (is_null($expo)) // - we are creating an expo
{
    $editFlag = FALSE;
    $expo = new Expo();
    setExpoCurrent($expo);
}

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php
    if ($editFlag && !is_null($expo->expoid))
    {
        include('section/LinkExpo.php');
    }
    ?>

    <div id="expoeditpage_expodata">
        <form method="POST" id="expoeditpage_expodata_save" action="ExpoEditAction.php">
		<table>
            <tr><td><?php createExpoDataHTMLRows($expo, "expoeditpage_expodata_save", FALSE); ?></td></tr>
			<tr><td><?php swwat_createInputSubmit(PARAM_SAVE, "Save"); ?></td></tr>
		</table>
        </form>
		<br />
    </div><!-- expoeditpage_expodata -->

</div><!-- main -->

<?php
    $menuItemArray = array();
    $menuItemArray[] = MENU_VIEW_SITEADMIN;
    $menuItemArray[] = MENU_VIEW_WORKERLIST;
    $menuItemArray[] = MENU_JOBTITLE;
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- continaer -->
</body></html>
