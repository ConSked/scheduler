<?php // $Id: ExpoAssignConfirmPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Expo.php');
require_once('db/Worker.php');
require_once('section/ExpoData.php');
require_once('section/Menu.php');
require_once('section/WorkerStation.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');
require_once('util/session.php');

$author = getWorkerAuthenticated();

$expo = getExpoCurrent();

$workerListNew = NULL;
if (isset($_SESSION['workerList']))
{
	$workerListNew = $_SESSION['workerList'];
}

$workerListOld = Worker::selectExpo($expo->expoid);

$workerListRemove = $workerListOld;
for ($k = 0; $k < count($workerListRemove); $k++)
{
	$worker = $workerListRemove[$k];
	if (in_array($worker, $workerListNew))
	{
		$workerListRemove[$k] = NULL;
	}
}
$workerListRemove = array_filter($workerListRemove);
$_SESSION['workerListRemove'] = $workerListRemove;

$workerListAdd = $workerListNew;
for ($k = 0; $k < count($workerListAdd); $k++)
{
	$worker = $workerListAdd[$k];
	if (in_array($worker, $workerListOld))
	{
		$workerListAdd[$k] = NULL;
	}
}
$workerListAdd = array_filter($workerListAdd);
$_SESSION['workerListAdd'] = $workerListAdd;
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title><?php echo(SITE_NAME); ?> - Expo Assign Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">

    <script type="text/javascript">
		function saveYes()
		{
			window.location.href = "ExpoAssignConfirmAction.php";
		}

		function saveNo()
		{
			window.location.href = "ExpoAssignPage.php";
		}
    </script>
</head>

<body>
<div id="container">

<?php
// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php include('section/LinkExpo.php'); ?>

    <div id="expoassignpage">
	    <table>
			<tr>
				<th>Staff to be removed</th><th>Staff to be added</th>
			</tr>
			<tr>
				<td width="50%" style="vertical-align: top">
					<table align="center">
						<?php
							if (count($workerListRemove) != 0)
							{
								foreach ($workerListRemove as $worker)
								{
									echo "<tr><td>".$worker->assignString()."</td></tr>";
								}
							}
							else
							{
								echo "<tr><td><span class=\"fieldError\">None</span></td></tr>";
							}
						?>
					</table>
				</td>
				<td width="50%" style="vertical-align: top">
					<table align="center">
						<?php
							if (count($workerListAdd) != 0)
							{
								foreach ($workerListAdd as $worker)
								{
									echo "<tr><td>".$worker->assignString()."</td></tr>";
								}
							}
							else
							{
								echo "<tr><td><span class=\"fieldError\">None</span></td></tr>";
							}
						?>
					</table>
				</td>
			</tr>
			<?php
			if (count($workerListRemove) != 0)
			{
			?>
			<tr>
				<td align="center" colspan="2">
					<br/>
					<div style="font-weight: bold">Shifts of staff to be removed</div>
					<table class="fieldValue">
				<?php
				if (count($workerListRemove) != 0)
				{
					echo "<tr class=\"rowTitle\">";
					echo "<th>Name</th><th>Role</th><th>Station</th><th>Job</th><th>Start</th><th>Stop</th><th>Supers</th><th>Crew</th>";
					echo "</tr>";
					$cnt = 0;
					foreach ($workerListRemove as $worker)
					{
						$assnArray = ShiftAssignmentView::selectWorker($expo->expoid, $worker->workerid);
						createWorkerStationHTMLRows($worker, $assnArray, TRUE, FALSE);
						$cnt += count($assnArray);
					}
					if ($cnt == 0)
					{
						echo "<tr><td align=\"center\" colspan=\"8\"><span class=\"fieldError\">None</span></td></tr>";
					}
				}
				else
				{
					echo "<tr><td colspan=\"8\"><span class=\"fieldError\">None</span></td></tr>";
				}
				?>
					</table>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td align="center" colspan="2"><br/>Do you wish to save these changes?</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<input type="button" value="Yes" onclick="saveYes()">
					&nbsp;&nbsp;
					<input type="button" value="No" onclick="saveNo()">
                </td>
			</tr>
        </table>
    </div><!-- expoassignpage -->

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
