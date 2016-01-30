<?php // $Id: WorkerEditPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');
require_once('properties/constants.php');
require_once('util/log.php');
require_once('util/session.php');

$author = getWorkerAuthenticated();
$worker = getWorkerCurrent();
if ($author->workerid != $worker->workerid)
{
    logMessage('authorization', 'WorkerEditPage requires self');
    header('Location: WorkerLoginPage.php');
    include('WorkerLoginPage.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title><?php echo(SITE_NAME); ?> - Worker Edit Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">

    <script src="swwat/js/validate.js"></script>
</head>

<body>
<div id="container">

<?php
require_once('db/Worker.php');
require_once('section/Menu.php');
require_once('section/WorkerData.php');
require_once('swwat/gizmos/parse.php');

//setExpoCurrent(NULL);

// ok, start the html
include('section/header.php');
?>

<div id="main">

	<div id="workereditpage_workerdata">
		<form method="POST" name="workereditpage_workerdata_save" action="WorkerEditAction.php">
		<table>
			<tr><td><?php createWorkerDataHTMLRows($worker, "workereditpage_workerdata_save", FALSE); ?></td></tr>
			<tr><td><?php swwat_createInputSubmit(PARAM_SAVE, "Save"); ?></td></tr>
		</table>
		</form>
	</div><!-- workereditpage_workerdata -->

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
