<?php // $Id: PreferenceWizardPage.php 2419 2012-10-29 18:01:08Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('preferences/'.PREF.'Preferences.php');
require_once('properties/constants.php');
require_once('section/Menu.php');
require_once('util/log.php');
require_once('util/session.php');

$author = getWorkerAuthenticated();

$expo = getExpoCurrent();

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

    <title><?php echo(SITE_NAME); ?> - Shift Preference Page</title>
    <link href="css/site.css" rel="stylesheet" type="text/css">
	<link href="jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">

	<script src="jquery/jquery-1.7.2.min.js"></script>
	<script src="jquery/jquery-ui-1.8.20.custom.min.js"></script>

<?php wizardPageJavascript(); ?>
</head>

<body onload="init()">
<div id="container">

<?php
// ok, start the html
include('section/header.php');
?>

<div id="main">
<?php include('section/LinkExpo.php'); ?>

<div id="preferencewizardpage">

<?php
	wizardPageTitle($expo->title, 2);
	wizardPageContent($author, $expo);
	wizardPageNavi();
?>

</div><!-- preferencewizardpage -->

</div><!-- main -->

<?php
    $menuItemArray = array();
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- container -->
</body></html>
