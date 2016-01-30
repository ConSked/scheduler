<?php // $Id: SchedulingReportPage.php 1774 2012-09-07 01:30:56Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/StationJob.php');
require_once('section/SchedulingReportList.php');
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

    <title><?php echo(SITE_NAME); ?> - Scheduling Report Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
	<link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script src="jquery/jquery-1.7.2.min.js"></script>
	<script src="jquery/jquery-ui-1.8.20.custom.min.js"></script>

	<script type="text/javascript">
	$(function() {
		$(".research tr:not(.accordion)").hide();
		$(".research tr:first-child").show();
		$(".research tr.accordion").click(function(){
			$(this).nextAll("tr").fadeToggle();
			if ($(this).find("img").attr("src") == "<?php echo(PARAM_EXPAND_ICON); ?>")
			{
				$(this).find("img").attr("src", "<?php echo(PARAM_COLLAPSE_ICON); ?>");
			}
			else
			{
				$(this).find("img").attr("src", "<?php echo(PARAM_EXPAND_ICON); ?>");
			}
		});
	});

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

		hideRows();
	}

	function hideRows()
	{
		$(document).ready(function() {
			$('#<?php echo PARAM_DATE;?> option').each(function() {
				var dates = this.value.replace(" ", "_");

				if (this.value != 'All')
				{
					if (this.selected == true)
					{
						$('.'+dates+'').show();
					}
					else
					{
						$('.'+dates+'').hide();
					}
				}
				else
				{
					if (this.selected == true)
					{
						$('.All').show();
					}
				}
			});
		});
	}

	function hideRoles()
	{
		$(document).ready(function() {
			var role = $('input:radio[name=role]:checked').val();

			if (role == 'Supervisors')
			{
				$('.Supervisors').show();
				$('.Crew').hide();
			}
			else if (role == 'Crew')
			{
				$('.Supervisors').hide();
				$('.Crew').show();
			}
			else
			{
				$('.Supervisors').show();
				$('.Crew').show();
			}

			$('.Supervisors').nextAll('tr').hide();
			$('.Crew').nextAll('tr').hide();
			$('.alldiv').text('Expand All');
			$('#allicon').attr("src", "<?php echo(PARAM_EXPAND_ICON); ?>");
			$(".research tr.accordion").find("img").attr("src", "<?php echo(PARAM_EXPAND_ICON); ?>");
		});
	}

	function ExpandCollapseAll()
	{
		$(document).ready(function() {
			var role = $('input:radio[name=role]:checked').val();

			if ($('.alldiv').text() == 'Expand All')
			{
				$('.alldiv').text('Collapse All');
				$('#allicon').attr("src", "<?php echo(PARAM_COLLAPSE_ICON); ?>");
				$(".research tr:not(.accordion)").show();
				$(".research tr:first-child").show();
				$(".research tr.accordion").find("img").attr("src", "<?php echo(PARAM_COLLAPSE_ICON); ?>");
			}
			else
			{
				$('.alldiv').text('Expand All');
				$('#allicon').attr("src", "<?php echo(PARAM_EXPAND_ICON); ?>");
				$(".research tr:not(.accordion)").hide();
				$(".research tr:first-child").show();
				$(".research tr.accordion").find("img").attr("src", "<?php echo(PARAM_EXPAND_ICON); ?>");
			}

			if (role == 'Supervisors')
			{
				$('.Crew').hide();
				$('.Crew').nextAll('tr').hide();
			}
			else if (role == 'Crew')
			{
				$('.Supervisors').hide();
				$('.Supervisors').nextAll('tr').hide();
			}
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

	<div id="checkindashboard_stationlist">
		<?php createSchedulingReportHTMLList($expo->expoid); ?>
	</div><!-- checkindashboard_stationlist -->
</div><!-- main -->

<?php
    $menuItemArray = array();
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
