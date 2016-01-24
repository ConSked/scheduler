<?php // $Id: WorkerEditAction.php 914 2012-07-14 13:26:32Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

include('util/authenticate.php');

require_once('properties/constants.php');
require_once('db/Worker.php');
require_once('util/log.php');
require_once('util/session.php');
require_once('util/SMSEnum.php');
require_once('swwat/gizmos/parse.php');

$worker = getWorkerCurrent();

if (isset($_POST[PARAM_SAVE]))
{
    $worker->firstName  = swwat_parse_string(html_entity_decode($_POST[PARAM_FIRSTNAME]), true);
    $worker->middleName = swwat_parse_string(html_entity_decode($_POST[PARAM_MIDDLENAME]), true);
    $worker->lastName   = swwat_parse_string(html_entity_decode($_POST[PARAM_LASTNAME]), true);
    $worker->email      = swwat_parse_string(html_entity_decode($_POST[PARAM_EMAIL]), true);
    $worker->phone      = swwat_parse_phone(html_entity_decode($_POST[PARAM_PHONE]), true);

    $sms = swwat_parse_enum(html_entity_decode($_POST[PARAM_SMS_SERVICE]), SMSEnum::$SMS_ARRAY, true);
    if (0 == strcmp(SMSEnum::$NONE, $sms))
    {
        $sms = NULL;
    }
    $worker->smsemail   = (is_null($worker->phone) || is_null($sms)) ? NULL : $worker->phone . "@" . $sms;

    $worker = $worker->update();
    if ($worker->workerid == getWorkerAuthenticated()->workerid)
    {
        $_SESSION[AUTHENTICATED] = $worker; // unusual; but I believe the only place required to do so
    }
    else
    {
        setWorkerCurrent($worker);
    }
}

// in all cases
header('Location: WorkerViewPage.php');
include('WorkerViewPage.php');

?>
