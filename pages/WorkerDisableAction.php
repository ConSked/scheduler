<?php // $Id: WorkerDisableAction.php 604 2012-06-07 21:11:57Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('db/WorkerLogin.php');
require_once('util/log.php');
require_once('util/session.php');

$worker = getWorkerCurrent();
if (isset($_REQUEST[PARAM_DISABLED]))
{
    // if enabled, we DISable; if disabled, we ENable
    WorkerLogin::set_isDisabled($worker->workerid, !($worker->isDisabled));
    $worker->isDisabled = !($worker->isDisabled); // set after in case of DB error
}

// in all cases
header('Location: WorkerViewPage.php');
include('WorkerViewPage.php');

?>
