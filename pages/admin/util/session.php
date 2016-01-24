<?php  // $Id: session.php 2119 2012-09-21 16:10:11Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
/*
 * http://www.brainbell.com/tutors/php/php_mysql/Using_Session_Variables.html
 *
 * Session variables can be of the type Boolean, integer, double, string,
 * object, or arrays of those variable types. Care must be taken when using
 * object session variables, because PHP needs access to the class definitions
 * of registered objects when initializing an existing session. If objects are
 * to be stored as session variables, you should include class definitions for
 * those objects in all scripts that initialize sessions, whether the scripts
 * use the class or not.
 */
/**
 * these imports are required here in case we serialize the object in a $_SESSION variable;
 * i.e. the import is before the session_start!
 */
require_once('db/Document.php');
require_once('db/Expo.php');
require_once('db/Job.php');
require_once('db/ShiftAssignment.php');
require_once('db/ShiftAssignmentView.php');
require_once('db/ShiftPreference.php');
require_once('db/ShiftStatus.php');
require_once('db/Station.php');
require_once('db/StationJob.php');
require_once('db/Worker.php');

function isLoggedIn()
{
    session_cache_limiter('nocache');
    if (!isset($_SESSION)) // get rid of annoying notice
    {
        session_start();
    }
    return (isset($_SESSION[AUTHENTICATED]));
} // isLoggedIn

function logout()
{
    session_start();
    $_SESSION[AUTHENTICATED] = NULL;
    $_SESSION[AUTHENTICATED_TEMP] = NULL;
    session_unset();
    session_destroy();
    session_regenerate_id(TRUE);
    return;
} // logout

function getWorkerAuthenticated()
{
    return isset($_SESSION[AUTHENTICATED]) ? $_SESSION[AUTHENTICATED] : NULL;
}

function setWorkerCurrent(Worker $worker = NULL)
{
    $_SESSION[WORKERCURRENT] = $worker;
}

function getWorkerCurrent()
{
    return isset($_SESSION[WORKERCURRENT]) ? $_SESSION[WORKERCURRENT] : NULL;
}

function setExpoCurrent(Expo $expo = NULL)
{
    $_SESSION[EXPOCURRENT] = $expo;
    setStationCurrent(NULL); // changing the expo ALWAYS resets the station
}
function getExpoCurrent()
{
    return isset($_SESSION[EXPOCURRENT]) ? $_SESSION[EXPOCURRENT] : NULL;
}

function setExpoOne(Expo $expo = NULL)
{
    $expo = Expo::selectID(1);
    $_SESSION[EXPOCURRENT] = $expo;
    setStationCurrent(NULL); // changing the expo ALWAYS resets the station
}

function getExpoOne()
{
    $expo = Expo::selectID(1);
    return $expo;
    //return isset($_SESSION[EXPOCURRENT]) ? $_SESSION[EXPOCURRENT] : NULL;
}

function setStationCurrent(StationJob $station = NULL)
{
    $_SESSION[STATIONCURRENT] = $station;
}

function getStationCurrent()
{
    return isset($_SESSION[STATIONCURRENT]) ? $_SESSION[STATIONCURRENT] : NULL;
}

function getParamItem($listName = PARAM_LIST, $listIndexName = PARAM_LIST_INDEX)
{
    $item = NULL;
    if (isset($_SESSION[$listName]) && isset($_REQUEST[$listIndexName]))
    {
        $list = $_SESSION[$listName];
        if (is_array($list))
        {
            $index = html_entity_decode($_REQUEST[$listIndexName]);
            if (is_numeric($index))
            {
                $item = $list[intval($index)]; // intval ensures not float
            }
        }
    }
    return $item;
} // getParamItem

/**
 * This function is used for <SELECT> elements with multiple options.
 */
function getSelectList()
{
    $paramList  = $_SESSION[PARAM_LIST];
    // note MULTIPLE used in HTML generation
    if (!isset($_REQUEST[PARAM_LIST_INDEX]) || is_null($_REQUEST[PARAM_LIST_INDEX]))
    {
        $indexArray = array();
    }
    else if (!is_array($_REQUEST[PARAM_LIST_INDEX]))
    {
        $indexArray = array($_REQUEST[PARAM_LIST_INDEX]);
    }
	else
	{
    	$indexArray = $_REQUEST[PARAM_LIST_INDEX];
	}

    $newParamList = array();
    foreach ($indexArray as $k)
    {
        $k = swwat_parse_number(html_entity_decode($k), FALSE);
        $newParamList[] = $paramList[$k];
    } // $k

    return $newParamList;
} // getSelectList


?>
