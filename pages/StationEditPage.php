<?php // $Id: StationEditPage.php 2437 2012-12-11 19:34:03Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/StationJob.php');
require_once('section/StationData.php');
require_once('section/Menu.php');
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

    <title><?php echo(SITE_NAME); ?> - Station Edit Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
	<link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script src="jquery/jquery-1.7.2.min.js"></script>
	<script src="jquery/jquery-ui-1.8.20.custom.min.js"></script>

	<script type="text/javascript">
		var DISPLAY_FORMAT = 'DD dd, MM yy';
		var DB_FORMAT = 'yy-mm-dd';

		String.prototype.capitalize = function()
		{
			return this.charAt(0).toUpperCase() + this.slice(1);
		}; // capitalize

		function init()
		{
			var description   = $('[name=<?php echo PARAM_DESCRIPTION;?>]').val();
			var location      = $('[name=<?php echo PARAM_LOCATION;?>]').val();
			var date          = $('#<?php echo PARAM_DATE;?>').val();
			var starthour     = $('[name=<?php echo PARAM_STARTHOUR;?>]').val();
			var stophour      = $('[name=<?php echo PARAM_STOPHOUR;?>]').val();
			var mincrew       = $('[name=<?php echo PARAM_MINCREW;?>]').val();
			var maxcrew       = $('[name=<?php echo PARAM_MAXCREW;?>]').val();
			var minsupervisor = $('[name=<?php echo PARAM_MINSUPERVISOR;?>]').val();
			var maxsupervisor = $('[name=<?php echo PARAM_MAXSUPERVISOR;?>]').val();
			var title         = $('[name=<?php echo PARAM_TITLE;?>]').val();
			var instruction   = $('[name=<?php echo PARAM_INSTRUCTION;?>]').val();

			if (title.length === 0 || description.length === 0 || location.length === 0 || date.length === 0 ||
			    starthour.length === 0 || stophour.length === 0 || mincrew.length === 0 ||
			    maxcrew.length === 0 || minsupervisor.length === 0 || maxsupervisor.length === 0)
			{
				$('#<?php echo PARAM_SAVE;?>').attr("disabled", "disabled");
			}

			var description0   = $('[name=<?php echo PARAM_DESCRIPTION;?>]')[0].defaultValue;
			var location0      = $('[name=<?php echo PARAM_LOCATION;?>]')[0].defaultValue;
			var date0          = $('#<?php echo PARAM_DATE;?>')[0].defaultValue;
			var starthour0     = $('[name=<?php echo PARAM_STARTHOUR;?>]')[0].defaultValue;
			var stophour0      = $('[name=<?php echo PARAM_STOPHOUR;?>]')[0].defaultValue;
			var mincrew0       = $('[name=<?php echo PARAM_MINCREW;?>]')[0].defaultValue;
			var maxcrew0       = $('[name=<?php echo PARAM_MAXCREW;?>]')[0].defaultValue;
			var minsupervisor0 = $('[name=<?php echo PARAM_MINSUPERVISOR;?>]')[0].defaultValue;
			var maxsupervisor0 = $('[name=<?php echo PARAM_MAXSUPERVISOR;?>]')[0].defaultValue;
			var title0         = $('[name=<?php echo PARAM_TITLE;?>]')[0].defaultValue;
			var instruction0   = $('[name=<?php echo PARAM_INSTRUCTION;?>]')[0].defaultValue;

			if (date.length != 0)
			{
				date = $.datepicker.parseDate('DD dd, MM yy', date).toDateString();
			}
			if (date0.length != 0)
			{
				date0 = $.datepicker.parseDate('yy-mm-dd', date0).toDateString();
			}

			if (document.location.search == "?copy")
			{
				if (title == title0 && date == date0 &&
 				    starthour == starthour0 && stophour == stophour0)
				{
					$('#<?php echo PARAM_SAVE;?>').attr("disabled", "disabled");
				}
			}
			else
			{
				if (description == description0 && location == location0 && date == date0 &&
				    starthour == starthour0 && stophour == stophour0 && mincrew == mincrew0 &&
				    maxcrew == maxcrew0 && minsupervisor == minsupervisor0 && maxsupervisor == maxsupervisor0 &&
				    title == title0 && instruction == instruction0)
				{
					$('#<?php echo PARAM_SAVE;?>').attr("disabled", "disabled");
				}
			}

			return;
		} // init

		function textCheck(param1, param2, param3)
		{
			var text = param1.value;
			var logic = (text.length === 0 || text.length > param2);

			$('#'+param3).remove();
			if (logic)
			{
				if ($('#'+param3).length === 0)
				{
					if (text.length === 0)
					{
						$('[name='+param3+']').after('  <span id="'+param3+'" class="fieldError">'+param3.capitalize()+' is a required field.</span>');
					}
					else
					{
						$('[name='+param3+']').after('  <span id="'+param3+'" class="fieldError">'+param3.capitalize()+' is over 255 characters.</span>');
					}
				}
			}
			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // textCheck

		function descriptionCheck(param1, param2)
		{
			var param3 = '<?php echo PARAM_DESCRIPTION; ?>';
			textCheck(param1, param2, param3);
			return;
		} // descriptionCheck

		function titleCheck(param1, param2)
		{
			var param3 = '<?php echo PARAM_TITLE; ?>';
			textCheck(param1, param2, param3);
			return;
		} // titleCheck

		function locationCheck(param1, param2)
		{
			var param3 = '<?php echo PARAM_LOCATION; ?>';
			textCheck(param1, param2, param3);
			return;
		} // locationCheck

		function dateCheck()
		{
			var stdate_val    = $('#<?php echo PARAM_DATE;?>').val();
			var expostart_val = $('#<?php echo PARAM_EXPOSTART;?>').val();
			var expostop_val  = $('#<?php echo PARAM_EXPOSTOP;?>').val();

			var stdate = new Date(Date.parse(stdate_val));
			var expostart = new Date(Date.parse(expostart_val));
			var expostop = new Date(Date.parse(expostop_val));

			var logic = (stdate < expostart || stdate > expostop);

			$('#stdate').remove();
			if (logic)
			{
				$('#<?php echo PARAM_DATE;?>').after('  <span id="stdate" class="fieldError">Station date is not during the Expo.</span>');
			}

			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // dateCheck

		$(document).ready(function()
		{
			var expostart = $('#<?php echo PARAM_EXPOSTART;?>').val();

			$.datepicker.setDefaults({ defaultDate:null });
			$.datepicker.setDefaults({ minDate:expostart });
			$.datepicker.setDefaults({ dateFormat:DISPLAY_FORMAT });
			var stdate = $('#<?php echo PARAM_DATE;?>');
			var d = $.datepicker.parseDate(DB_FORMAT, stdate.val());
			stdate.datepicker();
			stdate.datepicker("setDate", d);
			$('#stationeditpage_stationdata_save').submit(function()
			{
				var d = stdate.datepicker("getDate");
				stdate.val($.datepicker.formatDate(DB_FORMAT, d));
			}); // submit
			stdate.change(dateCheck); // change

		}); // ready

		function starthourCheck(param1, param2)
		{
			var start = param1.value;
			var stop  = $('[name=<?php echo PARAM_STOPHOUR; ?>]').val();

			var starthour = new Date(Date.parse('1 Jan 2000 '+start));
			var stophour  = new Date(Date.parse('1 Jan 2000 '+stop));

			var logic = (start.length === 0 || isNaN(starthour) || (stop.length !== 0 && starthour > stophour));

			$('#starthour').remove();
			if (logic)
			{
				if ($('#starthour').length === 0)
				{
					if (start.length === 0)
					{
						$('[name=<?php echo PARAM_STARTHOUR;?>]').after('  <span id="starthour" class="fieldError">Start Time is a required field.</span>');
					}
					else if (isNaN(starthour))
					{
						$('[name=<?php echo PARAM_STARTHOUR;?>]').after('  <span id="starthour" class="fieldError">Start Time is not a valid time.</span>');
					}
					else
					{
						$('[name=<?php echo PARAM_STARTHOUR;?>]').after('  <span id="starthour" class="fieldError">Start Time is after Stop Time.</span>');
					}
				}
			}
			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // starthourCheck

		function stophourCheck(param1, param2)
		{
			var stop  = param1.value;
			var start = $('[name=<?php echo PARAM_STARTHOUR; ?>]').val();

			var stophour  = new Date(Date.parse('1 Jan 2000 '+stop));
			var starthour = new Date(Date.parse('1 Jan 2000 '+start));

			var logic = (stop.length === 0 || isNaN(stophour) || (starthour.length !== 0 && stophour < starthour));

			$('#stophour').remove();
			if (logic)
			{
				if ($('#stophour').length === 0)
				{
					if (stop.length === 0)
					{
						$('[name=<?php echo PARAM_STOPHOUR;?>]').after('  <span id="stophour" class="fieldError">Start Time is a required field.</span>');
					}
					else if (isNaN(stophour))
					{
						$('[name=<?php echo PARAM_STOPHOUR;?>]').after('  <span id="stophour" class="fieldError">Start Time is not a valid time.</span>');
					}
					else
					{
						$('[name=<?php echo PARAM_STOPHOUR;?>]').after('  <span id="stophour" class="fieldError">Stop Time is after Start Time.</span>');
					}
				}
			}
			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // stophourCheck

		function minCheck(param1, param2, param3, param4)
		{
			var min = param1.value;
			var max = $('[name='+param4+']').val();
			var logic = (min.length === 0 || Math.ceil(min, 10) != Math.floor(min, 10) || parseInt(min, 10) < 0 || parseInt(min, 10) > parseInt(max, 10));
			var mintext = param3.slice(0,3).capitalize() + " " + param3.slice(3).capitalize();
			var maxtext = param4.slice(0,3).capitalize() + " " + param4.slice(3).capitalize();

			$('#'+param3).remove();
			if (logic)
			{
				if ($('#'+param3).length === 0)
				{
					if (min.length === 0)
					{
						$('[name='+param3+']').after('  <span id="'+param3+'" class="fieldError">'+mintext+' is a required field.</span>');
					}
					else if (min.length === 0 || Math.ceil(min, 10) != Math.floor(min, 10) || parseInt(min, 10) < 0)
					{
						$('[name='+param3+']').after('  <span id="'+param3+'" class="fieldError">'+mintext+' is not a positive integer.</span>');
					}
					else
					{
						$('[name='+param3+']').after('  <span id="'+param3+'" class="fieldError">'+mintext+' is greater than '+maxtext+'</span>');
					}
				}
			}
			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // minCheck

		function mincrewCheck(param1, param2)
		{
			var param3 = '<?php echo PARAM_MINCREW; ?>';
			var param4 = '<?php echo PARAM_MAXCREW; ?>';
			minCheck(param1, param2, param3, param4);
			return;
		} // mincrewCheck

		function minsupervisorCheck(param1, param2)
		{
			var param3 = '<?php echo PARAM_MINSUPERVISOR; ?>';
			var param4 = '<?php echo PARAM_MAXSUPERVISOR; ?>';
			minCheck(param1, param2, param3, param4);
		} // minsupervisorCheck

		function maxCheck(param1, param2, param3, param4)
		{
			var max = param1.value;
			var min = $('[name='+param4+']').val();
			var logic = (max.length === 0 || Math.ceil(max, 10) != Math.floor(max, 10) || parseInt(max, 10) < 0 || parseInt(max, 10) < parseInt(min, 10));
			var maxtext = param3.slice(0,3).capitalize() + " " + param3.slice(3).capitalize();
			var mintext = param4.slice(0,3).capitalize() + " " + param4.slice(3).capitalize();

			$('#'+param3).remove();
			if (logic)
			{
				if ($('#'+param3).length === 0)
				{
					if (max.length === 0)
					{
						$('[name='+param3+']').after('  <span id="'+param3+'" class="fieldError">'+maxtext+' is a required field.</span>');
					}
					else if (max.length === 0 || Math.ceil(max, 10) != Math.floor(max, 10) || parseInt(max, 10) < 0)
					{
						$('[name='+param3+']').after('  <span id="'+param3+'" class="fieldError">'+maxtext+' is not a positive integer.</span>');
					}
					else
					{
						$('[name='+param3+']').after('  <span id="'+param3+'" class="fieldError">'+maxtext+' is less than '+mintext+'</span>');
					}
				}
			}
			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // maxCheck

		function maxcrewCheck(param1, param2)
		{
			var param3 = '<?php echo PARAM_MAXCREW; ?>';
			var param4 = '<?php echo PARAM_MINCREW; ?>';
			maxCheck(param1, param2, param3, param4);
		} // maxcrewCheck

		function maxsupervisorCheck(param1, param2)
		{
			var param3 = '<?php echo PARAM_MAXSUPERVISOR; ?>';
			var param4 = '<?php echo PARAM_MINSUPERVISOR; ?>';
			maxCheck(param1, param2, param3, param4);
		} // maxsupervisorCheck

		function instructionCheck(param1, param2)
		{
			var text = param1.value;
			var logic = text.length > param2;

			$('#instruction').remove();
			if (logic)
			{
				$('[name=<?php echo PARAM_INSTRUCTION; ?>]').after('  <span id="instruction" class="fieldError">Instruction is over 255 characters.</span>');
			}
			$('#<?php echo PARAM_SAVE;?>').attr("disabled", logic);
			init();
			return;
		} // instructionCheck

	</script>
</head>

<body onload="init()">
<div id="container">

<?php
$station = getStationCurrent();
$expo = getExpoCurrent();
$editFlag = TRUE;
if (isset($_REQUEST[PARAM_CREATE])) // - we are creating an station
{
    $editFlag = FALSE;
    $station = new StationJob();
    setStationCurrent($station);
}

if (is_null($station->expoid))
{
	$station->expoid = $expo->expoid;
}

$copy = NULL;
if (isset($_REQUEST['copy']))
{
	$copy = '?copy';
}

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php
    if ($editFlag)
    {
		if (!is_null($station->stationid))
		{
        	include('section/LinkStation.php');
		}
    }
    else
    {
		if (!is_null($expo->expoid))
		{
        	include('section/LinkExpo.php');
		}
    }
    ?>

	<div id="stationeditpage_stationdata">
		<form method="POST" id="stationeditpage_stationdata_save" action="StationEditAction.php<?php echo($copy); ?>">
        <table>
            <tr style="vertical-align:top">
                <td><table><?php createStationDataHTMLRows($station, $expo, "stationeditpage_stationdata_save", FALSE); ?></table></td>
                <td style="min-width:25px"><!-- spacer --></td>
                <td><table><?php createJobDataHTMLRows($station, "stationeditpage_stationdata_save", FALSE, FALSE); ?></table></td>
            </tr>
            <tr><td colspan='3'><?php swwat_createInputSubmit(PARAM_SAVE, "Save"); ?></td></tr>
        </table>
		</form>
	</div><!-- stationeditpage_stationdata -->

</div><!-- main -->

<?php
	$menuItemArray = array();
	$menuItemArray[] = MENU_VIEW_SITEADMIN;
	$menuItemArray[] = MENU_VIEW_WORKERLIST;
	$menuItemArray[] = MENU_JOBTITLE;
	Menu::addMenu($menuItemArray);
	include('section/footer.php');
?>

</div><!-- container -->
</body></html>
