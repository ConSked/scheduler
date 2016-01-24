<?php // $Id: ShiftCheckInGridPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/format.php');
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

    <title>SwiftShift - Shift Check-In Grid Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
	<link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script src="jquery/jquery-1.7.2.min.js"></script>
	<script src="jquery/jquery-ui-1.8.20.custom.min.js"></script>

	<script type="text/javascript">
		function init()
		{
			var today = $.datepicker.formatDate('M d', new Date());

			if ($('#<?php echo PARAM_DATE;?> option:contains("'+today+'")').length > 0)
			{
				$('#<?php echo PARAM_DATE;?> option:contains("'+today+'")').attr('selected', 'selected');
			}
			else
			{
				$('#<?php echo PARAM_DATE;?> option:eq(0)').attr('selected', 'selected');
			}

			hideColumns();
		}

		function hideColumns()
		{
			$(document).ready(function() {
				$('#<?php echo PARAM_DATE;?> option').each(function() {
					var dates = this.value.replace(" ", "_");

					if (this.selected == true)
					{
						if (this.value != 'All')
						{
							$('.'+dates+'').show();
						}
						else
						{
							$('td').show();
							$('tr').show();
						}
					}
					else
					{
						if (this.value != 'All')
						{
							$('.'+dates+'').hide();
						}
					}
				});
			});
		}
	</script>
</head>

<body onload="init()">
<div id="container">

<?php

$expo = getExpoCurrent();

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php include('section/LinkExpo.php'); ?>
    <?php include('section/ShiftCheckInMatrix.php'); ?>

    <div id="checkin_grid">
    	<?php
		createShiftCheckInHTMLMatrix($expo->expoid);
	    ?>
    </div><!-- checkin_grid -->
</div><!-- main -->

<?php
    $menuItemArray = array();
    if ($author->isOrganizer())
	{
    	$menuItemArray[] = MENU_VIEW_SITEADMIN;
        $menuItemArray[] = MENU_VIEW_WORKERLIST;
    }
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
