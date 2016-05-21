<?php  // $Id: TimePreference.php 2200 2012-09-22 18:26:20Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/log.php');

define('NUMBER_SHIFTS', 50);

class TimePreference
{
public $number_shifts = NUMBER_SHIFTS;
public $workerid;
public $expoid;
public $shift1, $shift2, $shift3, $shift4, $shift5, $shift6, $shift7, $shift8, $shift9, $shift10;
public $shift11, $shift12, $shift13, $shift14, $shift15, $shift16, $shift17, $shift18, $shift19, $shift20;
public $shift21, $shift22, $shift23, $shift24, $shift25, $shift26, $shift27, $shift28, $shift29, $shift30;
public $shift31, $shift32, $shift33, $shift34, $shift35, $shift36, $shift37, $shift38, $shift39, $shift40;
public $shift41, $shift42, $shift43, $shift44, $shift45, $shift46, $shift47, $shift48, $shift49, $shift50;

public static function selectID($workerId, $expoId)
{
	try
	{
        // Create the query
        $select_query = "SELECT DISTINCT workerid, expoid, ";
        for ($i = 1; $i <= NUMBER_SHIFTS; $i++)
        {
            $select_query .= " shift".$i.",";
        }
        $select_query = rtrim($select_query, ",");
        $select_query .= " FROM timepreference WHERE workerid = ? AND expoid = ?";

        // Execute the query
		$rows = simpleSelect("TimePreference", $select_query, array($workerId, $expoId));
		if (1 != count($rows))
		{
			return NULL;
		}
		return $rows[0];
	}
	catch (PDOException $pe)
	{
		logMessage('TimePreference::selectID('.$workerId.', '.$expoId.')', $pe->getMessage());
	}
}

public function insert()
{
	try
	{
        // Create the query
        $insert_query = "INSERT INTO timepreference (workerid, expoid, ";
        for ($i = 1; $i <= NUMBER_SHIFTS; $i++)
        {
            $insert_query .= " shift".$i.",";
        }
        $insert_query = rtrim($insert_query, ",");
        $insert_query .= ") VALUES (";
        for ($i = 1; $i <= (NUMBER_SHIFTS+2); $i++)
        {
            $insert_query .= "?, ";
        }
        $insert_query = rtrim($insert_query, ", ");
        $insert_query .= ")";

        // Create the input array
        $insert_array = array($this->workerid, $this->expoid);
        for ($i = 1; $i <= NUMBER_SHIFTS; $i++)
        {
            $shift = "shift".$i;
            array_push($insert_array, $this->$shift);
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
		logMessage('TimePreference::insert()', $pe->getMessage());
	}
}

public function update()
{
	try
	{
        // Create the query
        $update_query = "UPDATE timepreference SET";
        for ($i = 1; $i <= NUMBER_SHIFTS; $i++)
        {
            $update_query .= " shift".$i." = ?,";
        }
        $update_query = rtrim($update_query, ",");
        $update_query .= " WHERE workerid = ? AND expoid = ?";

        // Create the input array
        $update_array = array();
        for ($i = 1; $i <= NUMBER_SHIFTS; $i++)
        {
            $shift = "shift".$i;
            array_push($update_array, $this->$shift);
        }
        array_push($update_array, $this->workerid, $this->expoid);

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
		logMessage('TimePreference::update()', $pe->getMessage());
	}
}

}
?>
