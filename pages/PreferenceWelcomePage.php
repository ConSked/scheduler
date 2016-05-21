<?php // $Id: PreferenceWelcomePage.php 2418 2012-10-28 19:23:53Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('preferences/'.PREF.'Preferences.php');
require_once('properties/constants.php');
require_once('section/Menu.php');
require_once('util/log.php');
require_once('util/session.php');

$author = getWorkerAuthenticated();

if (isset($_REQUEST[PARAM_LIST_INDEX]))
{
    $expo = getParamItem(PARAM_LIST, PARAM_LIST_INDEX);
    if (!is_null($expo))
    {
        setExpoCurrent($expo);
    }
    $_SESSION[PARAM_LIST] = NULL;
}
$expo = getExpoCurrent();
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<title><?php echo(SITE_NAME); ?> - Shift Preference Welcome</title>
	<link href="css/site.css" rel="stylesheet" type="text/css">
  <script src="https://fb.me/react-15.0.1.js"></script>
  <script src="https://fb.me/react-dom-15.0.1.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js"></script>
</head>

<body>
<div id="container">

<?php
// ok, start the html
include('section/header.php');
?>

<div id="main">
<?php include('section/LinkExpo.php'); ?>

<div id="preferencewelcomepage">

<?php
  welcomePageTitle($expo->title, 1);
  if (PREF == 'New')
  {
	  welcomePageContent($author, $expo);
  }
  else
  {
	  welcomePageContent();
  }
	welcomePageNavi();
?>

</div><!-- preferencewelcomepage -->

</div><!-- main -->

<?php
	$menuItemArray = array();
	Menu::addMenu($menuItemArray);
	include('section/footer.php');
?>

</div><!-- container -->
</body></html>
