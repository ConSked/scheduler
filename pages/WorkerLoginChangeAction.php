<?php // $Id: WorkerLoginChangeAction.php 2431 2003-01-07 20:24:44Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

// custom isLoggedIn such that a temp login can change pw
require_once('properties/constants.php');
require_once('util/session.php');
session_cache_limiter('nocache');
session_start();
if (!isset($_SESSION[AUTHENTICATED_TEMP]) && !isLoggedIn())
{
    logMessage('authentication', 'worker not logged in');
    header('Location: WorkerLoginPage.php');
    include('WorkerLoginPage.php');
    return;
}

require_once('db/Worker.php');
require_once('db/WorkerLogin.php');
require_once('util/log.php');
require_once('swwat/gizmos/parse.php');

$password = swwat_parse_string(html_entity_decode($_POST[PARAM_PASSWORD]), true);
if (is_null($password))
{
    header('Location: WorkerLoginChangePage.php');
    include('WorkerLoginChangePage.php');
    return;
}

// else
$worker = isset($_SESSION[AUTHENTICATED_TEMP]) ? $_SESSION[AUTHENTICATED_TEMP] : getWorkerAuthenticated();
WorkerLogin::password_change($worker->workerid, $password);
$worker = getWorkerAuthenticated();
$password = NULL;

if ($worker->isOrganizer())
{
    header('Location: SiteAdminPage.php');
    include('SiteAdminPage.php');
}
else
{
    header('Location: WorkerViewPage.php');
    include('WorkerViewPage.php');
}
return;

?>
