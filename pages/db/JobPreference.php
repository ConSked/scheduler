<?php  // $Id: JobPreference.php 2200 2012-09-22 18:26:20Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/log.php');

define('NUMBER_JOBS', 100);

class JobPreference
{
public $number_jobs = NUMBER_JOBS;
public $workerid;
public $job1, $job2, $job3, $job4, $job5, $job6, $job7, $job8, $job9, $job10;
public $job11, $job12, $job13, $job14, $job15, $job16, $job17, $job18, $job19, $job20;
public $job21, $job22, $job23, $job24, $job25, $job26, $job27, $job28, $job29, $job30;
public $job31, $job32, $job33, $job34, $job35, $job36, $job37, $job38, $job39, $job40;
public $job41, $job42, $job43, $job44, $job45, $job46, $job47, $job48, $job49, $job50;
public $job51, $job52, $job53, $job54, $job55, $job56, $job57, $job58, $job59, $job60;
public $job61, $job62, $job63, $job64, $job65, $job66, $job67, $job68, $job69, $job70;
public $job71, $job72, $job73, $job74, $job75, $job76, $job77, $job78, $job79, $job80;
public $job81, $job82, $job83, $job84, $job85, $job86, $job87, $job88, $job89, $job90;
public $job91, $job92, $job93, $job94, $job95, $job96, $job97, $job98, $job99, $job100;

public static function selectID($workerId)
{
	try
	{
        // Create the query
        $select_query = "SELECT DISTINCT workerid,";
        for ($i = 1; $i <= NUMBER_JOBS; $i++)
        {
            $select_query .= " job".$i.",";
        }
        $select_query = rtrim($select_query, ",");
        $select_query .= " FROM jobpreference WHERE workerid = ?";

        // Execute the query
       	$rows = simpleSelect("JobPreference", $select_query, array($workerId));
		if (1 != count($rows))
		{
			return NULL;
		}
		return $rows[0];
	}
	catch (PDOException $pe)
	{
		logMessage('JobPreference::selectID(' . $workerId . ')', $pe->getMessage());
	}
}

public function insert()
{
	try
	{
        // Create the query
        $insert_query = "INSERT INTO jobpreference (workerid,";
        for ($i = 1; $i <= NUMBER_JOBS; $i++)
        {
            $insert_query .= " job".$i.",";
        }
        $insert_query = rtrim($insert_query, ",");
        $insert_query .= ") VALUES (";
        for ($i = 1; $i <= (NUMBER_JOBS+1); $i++)
        {
            $insert_query .= "?, ";
        }
        $insert_query = rtrim($insert_query, ", ");
        $insert_query .= ")";

        // Create the input array
        $insert_array = array($this->workerid);
        for ($i = 1; $i <= NUMBER_JOBS; $i++)
        {
                $job = "job".$i;
                array_push($insert_array, $this->$job);
        }
 
        // Execute the query
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare($insert_query);
		$stmt->execute($insert_array);
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('JobPreference::insert()', $pe->getMessage());
	}
}

public function update()
{
	try
	{
        // Create the query
        $update_query = "UPDATE jobpreference SET";
        for ($i = 1; $i <= NUMBER_JOBS; $i++)
        {
            $update_query .= " job".$i." = ?,";
        }
        $update_query = rtrim($update_query, ",");
        $update_query .= " WHERE workerid = ?";

        // Create the input array
        $update_array = array();
        for ($i = 1; $i <= NUMBER_JOBS; $i++)
        {
            $job = "job".$i;
            array_push($update_array, $this->$job);
        }
        array_push($update_array, $this->workerid);

        // Execute the query
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare($update_query);
		$stmt->execute($update_array);
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('JobPreference::update()', $pe->getMessage());
	}
}

}
?>
