<?php  // $Id: authenticateSupervisor.php 2264 2012-09-26 15:31:49Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('util/session.php');
require_once('db/Worker.php');

include('util/authenticate.php');

if (getWorkerAuthenticated()->isCrewMember())
{
    logMessage('authorization', 'page requires Supervisor');
    header('Location: WorkerLoginPage.php');
    include('WorkerLoginPage.php');
    return;
}

?>
