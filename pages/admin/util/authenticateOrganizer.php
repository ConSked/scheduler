<?php  // $Id: authenticateOrganizer.php 1294 2012-08-09 23:44:40Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('util/session.php');
require_once('db/Worker.php');

include('util/authenticate.php');

if (!(getWorkerAuthenticated()->isOrganizer()))
{
    logMessage('authorization', 'page requires Organizer');
    header('Location: WorkerLoginPage.php');
    include('WorkerLoginPage.php');
    return;
}

?>
