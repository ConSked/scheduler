<?php
include('util/authenticate.php');

require_once('preferences/'.PREF.'Preferences.php');
require_once('properties/constants.php');
require_once('util/log.php');
require_once('util/session.php');

$position = NULL;
if (isset($_REQUEST[PARAM_LIST_INDEX]) && !is_null($_REQUEST[PARAM_LIST_INDEX]))
{
  $position = $_REQUEST[PARAM_LIST_INDEX];
}

$author = getWorkerAuthenticated();

$expo = getExpoCurrent();

welcomeActionContent($author, $expo);

header('Location: PreferenceWelcomePage.php?'.PARAM_LIST_INDEX.'='.$position.'&'.PARAM_SAVE);
include('PreferenceWelcomePage.php');

?>
