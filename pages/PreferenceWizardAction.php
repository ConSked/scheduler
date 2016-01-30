<?php // $Id: PreferenceWizardAction.php 2418 2012-10-28 19:23:53Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.
include('util/authenticate.php');

require_once('preferences/'.PREF.'Preferences.php');
require_once('properties/constants.php');
require_once('util/log.php');
require_once('util/session.php');

$author = getWorkerAuthenticated();

$expo = getExpoCurrent();

wizardActionContent($author, $expo);

header('Location: PreferenceReviewPage.php');
include('PreferenceReviewPage.php');

?>
