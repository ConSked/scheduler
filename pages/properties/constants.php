<?php  // $Id: constants.php 2418 2012-10-28 19:23:53Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

define("DBHOST", "localhost");
define("DBPORT", "3306");
define("DATABASE", "consked");
define("DBUSERNAME", "cskeuser");
define("DBPASSWORD", "cskepass");
define("PREF", "NYCC");

define("SITE_NAME", "ConSked");
define("BASE_URL", "https://dev.consked.com");
define("LOGIN_URL",        BASE_URL . "/pages/WorkerLoginPage.php");
define("REGISTRATION_URL", BASE_URL . "/pages/RegistrationPage.php");
define("OPENREGISTRATION", "true");

define("EXPOCURRENT",    "expocurrent");
define("EXPOLIST",       "expolist");
define("SHIFTCURRENT",   "shiftcurrent");
define("STATIONCURRENT", "stationcurrent");
define("WORKERCURRENT",  "workercurrent"); // worker which is being 'worked' on
define("AUTHENTICATED",  "authenticated"); // worker which is logged in
define("AUTHENTICATED_TEMP",  "authenticated_temp"); // worker which is logged for pw reset only


define("PARAM_SAVE", "save");
define("PARAM_COPY", "copy");
define("PARAM_DELETE", "delete");

define("PARAM_PAGE_MESSAGE", "page_message");

define("PARAM_COMMENT", "comment");
define("PARAM_COMMENT_MESSAGE", "comment_message");

define("PARAM_FIRSTNAME", "firstname");
define("PARAM_MIDDLENAME", "middlename");
define("PARAM_LASTNAME", "lastname");
define("PARAM_EMAIL", "email");
define("PARAM_PHONE", "phone");
define("PARAM_SMS_SERVICE", "sms");

define("PARAM_AUTHROLE", "authrole");
define("PARAM_UPDATEROLE", "updaterole"); // see css/site.css
define("PARAM_DISABLED", "disable"); // see css/site.css
define("PARAM_PWRESET", "pwreset"); // see css/site.css
define("PARAM_PASSWORD", "password");

define("PARAM_PHONE_MESSAGE", "phone_message");

define("PARAM_MESSAGE", "message");
define("PARAM_SUBJECT_MESSAGE", "message_subject");
define("PARAM_SEND_MESSAGE", "message_send"); // see PARAM_EMAIL, PARAM_SMS
define("PARAM_TYPE_MESSAGE", "message_type"); // see PARAM_EMAIL, PARAM_SMS

define("PARAM_STARTTIME", "starttime");
define("PARAM_STOPTIME", "stoptime");
define("PARAM_EXPOSTART", "expostart");
define("PARAM_EXPOSTOP", "expostop");
define("PARAM_STARTHOUR", "starthour");
define("PARAM_STOPHOUR", "stophour");
define("PARAM_WEEKSTART", "weekstart");
define("PARAM_MAXHOURS", "maxhours");
define("PARAM_DESCRIPTION", "description");
define("PARAM_WITHCODE", "code");
define("PARAM_UNIQUE", "unique");
define("PARAM_CREATE", "create");
define("PARAM_UPLOAD", "upload");
define("PARAM_DESIRE", "desire");

define("PARAM_DOCTYPE", "doctype");
define("PARAM_DOCUMENT", "document");

define("PARAM_SCHEDULE_ALGO", "algorithm");
define("PARAM_SCHEDULE_KEEP", "keep");
define("PARAM_SCHEDULE_PUBLISH", "publish");
define("PARAM_SCHEDULE_TIME_CONFLICT", "timeconflict");
define("ALLOW_SCHEDULE_TIME_CONFLICT", "false");
define("PARAM_NEWUSER_ADDED_ON_REGISTRATION", "newuseradd");
define("PARAM_ANTEREMINDER_TIME", "2");//Time in days before a set of shifts when a reminder is emailed

define("PARAM_MAXCREW", "maxcrew");
define("PARAM_MINCREW", "mincrew");
define("PARAM_ASSIGNEDCREW", "assignedcrew");
define("PARAM_MAXSUPERVISOR", "maxsupervisor");
define("PARAM_MINSUPERVISOR", "minsupervisor");
define("PARAM_ASSIGNEDSUPERVISOR", "assignedsuprevisor");
define("PARAM_TITLE", "title");
define("PARAM_JOB", "job");
define("PARAM_LOCATION", "location");
define("PARAM_DATE", "date");
define("PARAM_TIME", "time");
define("PARAM_DATETIME", "datetime");
define("PARAM_UNAVAILABLE", "unavailable");
define("PARAM_INSTRUCTION", "instruction");

define("PARAM_STATUSID", "statusid");
define("PARAM_STATUSDATE", "statusdate");
define("PARAM_STATUSHOUR", "statushour");
define("PARAM_STATUSTYPE", "statustype");

define("PARAM_EXPAND_ICON", "images/80-expand-collapse-icons-Shapes4FREE/PNG/plus-minus-medium/expand-medium-green-Shapes4FREE.png");
define("PARAM_COLLAPSE_ICON", "images/80-expand-collapse-icons-Shapes4FREE/PNG/plus-minus-medium/collapse-medium-green-Shapes4FREE.png");

define("PARAM_LIST", "list");
define("PARAM_LIST_INDEX", "list_index");
define("PARAM_LIST_MULTIPLE", "list_index[]");

define("PARAM_LIST2", "list2");
define("PARAM_LIST2_INDEX", "list2_index");
define("PARAM_LIST2_MULTIPLE", "list2_index[]");

define("TIMEZONE", "America/Chicago");
// ensure you require_once on imports!
// timezone setting is kind of a constant, which is why it is here
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");
try
{
    date_default_timezone_get();
}
catch (ErrorException $ex)
{
    date_default_timezone_set(TIMEZONE);
}
 // todo - remove following line (left in as will probably break lots of stuff per current fragility of system)
restore_error_handler();

?>
