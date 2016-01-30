<?php  // $Id: authenticate.php 1294 2012-08-09 23:44:40Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('util/session.php');
require_once('db/Worker.php');
require_once('db/WorkerLogin.php');

// require session login
if (!isLoggedIn())
{
    logMessage('authentication', 'worker not logged in');
    header('Location: WorkerLoginPage.php');
    include('WorkerLoginPage.php');
    return;
}
if (WorkerLogin::isDisabled(getWorkerAuthenticated()->workerid))
{
    logMessage('authentication', 'worker is being forced to log out per disabling');
    header('Location: WorkerLoginPage.php');
    include('WorkerLoginPage.php');
    return;
}
?>
