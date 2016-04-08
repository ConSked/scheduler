<?php // $Id: WorkerData.php 2100 2012-09-19 05:09:40Z cross $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('util/log.php');
require_once('util/SMSEnum.php');
require_once('properties/constants.php');
require_once('swwat/gizmos/format.php');
require_once('swwat/gizmos/html.php');
require_once('db/Worker.php');

/*
 * This module is executed as a function (rather than call-outs from HTML)
 * in order to pass-in the $isEditableFlag as a variable (rather than lookup some $_SESSION variable)
 */

function createWorkerDataHTMLRows(Worker $worker, $formName, $isDisabledFlag = TRUE, $code = FALSE)
{
    if (is_null($worker))
    {
        return;
    }
    $_POST[PARAM_FIRSTNAME] = htmlspecialchars($worker->firstName);
    $_POST[PARAM_MIDDLENAME] = htmlspecialchars($worker->middleName);
    $_POST[PARAM_LASTNAME] = htmlspecialchars($worker->lastName);
    $_POST[PARAM_EMAIL] = htmlspecialchars($worker->email);
    $_POST[PARAM_PHONE] = swwat_format_phone($worker->phone);
    if (is_null($worker))
    {
        $service = NULL;
    }
    else
    {
        $service = strstr($worker->smsemail, "@");
        $service = substr($service, 1);
    }
    $_POST[PARAM_SMS_SERVICE] = $service;

    echo "<table>\n";
    echo '  <tr><td class="fieldTitle">First Name:</td><td>';
    swwat_createInputValidateLength(PARAM_FIRSTNAME, $formName, 'swwat_ValidateLength', 255, $isDisabledFlag);
    echo "</td></tr>\n";

    echo '  <tr><td class="fieldTitle">Middle Name:</td><td>';
    swwat_createInputValidateLength(PARAM_MIDDLENAME, $formName, 'swwat_ValidateLength', 255, $isDisabledFlag);
    echo "</td></tr>\n";

    echo '  <tr><td class="fieldTitle">Last Name:</td><td>';
    swwat_createInputValidateLength(PARAM_LASTNAME, $formName, 'swwat_ValidateLength', 255, $isDisabledFlag);
    echo "</td></tr>\n";

    echo '  <tr><td class="fieldTitle">Email:</td><td>';
    swwat_createInputValidateLength(PARAM_EMAIL, $formName, 'swwat_ValidateLength', 255, $isDisabledFlag);
    echo "</td></tr>\n";

    echo '  <tr><td class="fieldTitle">Phone:</td><td>';
    swwat_createInputValidatePhone(PARAM_PHONE, $formName, 12, $isDisabledFlag);
    echo "</td></tr>\n";

//    echo '  <tr><td class="fieldTitle">Texting Service:</td><td>';
//    swwat_createSelect(0, PARAM_SMS_SERVICE, SMSEnum::$OPTION, "none", $isDisabledFlag);
//    echo "</td></tr>\n";

    if (FALSE != $code)
    {
        $code = is_null($code) ? "" : $code;
        $_POST[PARAM_WITHCODE] = $code;
        echo '  <tr><td class="fieldTitle">Registration Code:</td><td>';
        swwat_createInputValidateLength(PARAM_WITHCODE, $formName, 'swwat_ValidateLength', 255, $isDisabledFlag);
        echo "</td></tr>\n";
    }

    echo "</table>\n";
} // createWorkerDataHTMLRows

?>
