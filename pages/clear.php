<?php  // $Id: clear.php 2421 2012-10-30 19:37:58Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('util/log.php');


function getPDOConnection()
{
	if (DBPORT == 3306)
	{
		$dbh = new PDO('mysql:host=localhost;dbname=' . DATABASE, DBUSERNAME, DBPASSWORD,
		               array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}
	else
	{
		if (DBUSERNAME == "phpserver")
		{
			$dbnametemp = "dbowner";
			$dbpasstemp = "ownerpass";
		}
     	
		$dbh = new PDO('mysql:host=' . DBHOST . ';port=' . DBPORT . ';dbname=' . DATABASE, $dbnametemp, $dbpasstemp,
		               array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}
	echo "dbname = ".DATABASE;
	echo "<br/>";
	return $dbh;
} // getPDOConnection


$fileString = array();
// created with a simple grep createTable createDB.sh | awk
// $ grep createTable ../../src/db/scripts/createDB.sh | grep sql | grep -v '#' | awk '{print "$fileString[] = \"" $2 "\";"}' >> dbschema.php
//
$fileString[] = "cleartriggers.sql";
$fileString[] = "clear.sql";
$fileString[] = "clear2.sql";
$fileString[] = "clear3.sql";
//
$dir = "sql/"; // change as appropriate for deployment

try
{
	$dbh = getPDOConnection();
	foreach ($fileString as $fs)
	{
		$file = file_get_contents($dir . $fs);
		echo "<br>";
		echo $file;
		$dbh->exec($file);
		// presumably just keep one if deployed on webserver
		// logMessage("dbschema success", "file:$fs");
		echo ("\ndbschema success -- file:$fs");
		echo "<br>";
	} // $fs
	$dbh = NULL;
}
catch (PDOException $err)
{
	// presumably just keep one if deployed on webserver
	// logMessage("dbcreate failed", "file:$fs   error:" . $ex->getMessage());
	//echo ("\ndbcreate failed -- file:$fs   error:" . $ex->getMessage());
	// Catch Expcetions from the above code for our Exception Handling
	$trace = ' ';
	foreach ($err->getTrace() as $a => $b)
	{
		foreach ($b as $c => $d)
		{
			if ($c == 'args')
			{
				foreach ($d as $e => $f)
				{
					$trace .= '' . strval($a) . 'args: ' . $e . ' ' . $f . ''.PHP_EOL;
				}
			}
			else
			{
				$trace .= '' . strval($a) . '' . $c . '' . $d . ' '.PHP_EOL;
			}
		}
	}
	echo ' '.PHP_EOL;
	echo 'PHP PDO Error ' . strval($err->getCode()) . ' '.PHP_EOL;
	echo 'Message: ' . $err->getMessage() . ' '.PHP_EOL;
	echo 'Code: ' . strval($err->getCode()) . ' '.PHP_EOL;
	echo 'File: ' . $err->getFile() . ' '.PHP_EOL;
	echo 'Line: ' . strval($err->getLine()) . ' '.PHP_EOL;
	echo 'Trace: ' . $trace . ' '.PHP_EOL;
}
echo "\n";

?>
