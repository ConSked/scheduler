<?php  // $Id: dbutil.php 2369 2012-10-10 20:10:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');

function getPDOConnection()
{
	if (DBPORT == 3306)
	{
    	$dbh = new PDO('mysql:host=localhost;dbname=' . DATABASE, DBUSERNAME, DBPASSWORD,
		               array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}
	else
	{
    	$dbh = new PDO('mysql:host=' . DBHOST . ';port=' . DBPORT . ';dbname=' . DATABASE, DBUSERNAME, DBPASSWORD,
		               array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}
    return $dbh;
} // getPDOConnection


/**
 * This method opens a connection, sets up the sql, injects the paramArray, and creates className instances.
 * Note it still throws the PDOException for handling.
 *
 * Usage: simpleSelect("Worker", "SELECT * FROM worker WHERE workerid = ?", array($workerid));
 */
function simpleSelect($className, $sql, $paramArray = NULL)
{
    $dbh = getPDOConnection();
    $stmt = $dbh->prepare($sql);
    if (is_null($paramArray))
    {
        $stmt->execute();
    }
    else
    {
        $stmt->execute($paramArray);
    }
    $rows = $stmt->fetchAll(PDO::FETCH_CLASS, $className);
    return (0 != count($rows)) ? $rows : array();
} // simpleSelect

?>
