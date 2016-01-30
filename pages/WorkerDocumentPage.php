<?php // $Id: WorkerDocumentPage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');
require_once('properties/constants.php');
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

    <title><?php echo(SITE_NAME); ?> - Worker Document Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="container">

<?php
require_once('section/DocumentList.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/format.php');

$expo = getExpoCurrent();
$workerList = Worker::selectExpo($expo->expoid);
$documentList = Document::selectExpo($expo->expoid);

// ok, start the html
include('section/header.php');
?>

<div id="main">
<?php
	include('section/LinkExpo.php');
?>
	<div id="documentlistpage_list">
	<?php
	createDocumentHTMLList($documentList, $workerList, !$expo->isPast());
	?>
	</div><!-- documentlistpage_list -->
</div><!-- main -->

<?php
    $menuItemArray = array();
    if ($author->isOrganizer())
    {
        $menuItemArray[] = MENU_VIEW_SITEADMIN;
    }
    $menuItemArray[] = MENU_SEND_MESSAGE;
    if ($author->isOrganizer())
    {
        $menuItemArray[] = MENU_VIEW_WORKERLIST;
    }
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- continaer -->
</body></html>
