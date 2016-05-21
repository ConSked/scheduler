<?php

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/log.php');

define('NUMBER_HOURS', 24);

class NewTimePreference
{
public $number_hours = NUMBER_HOURS;
public $workerid;
public $expoid;
public $day;
public $hour1, $hour2, $hour3, $hour4, $hour5, $hour6, $hour7, $hour8;
public $hour9, $hour10, $hour11, $hour12, $hour13, $hour14, $hour15, $hour16;
public $hour17, $hour18, $hour19, $hour20, $hour21, $hour22, $hour23, $hour24;

public static function selectID($workerId, $expoId, $day = NULL)
{
  try
  {
    // Create the query
    $select_query = "SELECT DISTINCT workerid, expoid, day,";
    for ($i = 1; $i <= NUMBER_HOURS; $i++)
    {
      $select_query .= " hour".$i.",";
    }
    $select_query = rtrim($select_query, ',');
    $select_query .= " FROM newtimepreference WHERE workerid = ? AND expoid = ?";
    if (!is_null($day))
    {
      $select_query .= " AND day = ?";
    }

    // Create the input array
    $select_array = array($workerId, $expoId);
    if (!is_null($day))
    {
      array_push($select_array, $day);
    }

    // Execute the query
    $rows = simpleSelect("NewTimePreference", $select_query, $select_array);
    return $rows;
  }
  catch (PDOException $pe)
  {
    logMessage('NewTimePreference::selectID('.$workerId.', '.$expoId.')', $pe->getMessage());
  }
}

public function insert()
{
  try
  {
    // Create the query
    $insert_query = "INSERT INTO newtimepreference (workerid, expoid, day,"; 
    for ($i = 1; $i <= NUMBER_HOURS; $i++)
    {
      $insert_query .= " hour".$i.",";
    }
    $insert_query = rtrim($insert_query, ",");
    $insert_query .= ") VALUES (";
    for ($i = 1; $i <= (NUMBER_HOURS+3); $i++)
    {
      $insert_query .= "?, ";
    }
    $insert_query = rtrim($insert_query, ", ");
    $insert_query .= ")";

    // Create the input array
    $insert_array = array($this->workerid, $this->expoid, $this->day);
    for ($i = 1; $i <= NUMBER_HOURS; $i++)
    {
      $hour = "hour".$i;
      array_push($insert_array, $this->$hour);
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
    logMessage('NewTimePreference::insert()', $pe->getMessage());
  }
}

public function update()
{
  try
  {
    // Create the query
    $update_query = "UPDATE newtimepreference SET";
    for ($i = 1; $i <= NUMBER_HOURS; $i++)
    {
      $update_query .= " hour".$i." = ?,";
    }
    $update_query = rtrim($update_query, ",");
    $update_query .= " WHERE workerid = ? AND expoid = ? AND day = ?";

    // Create the input array
    $update_array = array();
    for ($i = 1; $i <= NUMBER_HOURS; $i++)
    {
      $hour = "hour".$i;
      array_push($update_array, $this->$hour);
    }
    array_push($update_array, $this->workerid, $this->expoid, $this->day);

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
    logMessage('NewTimePreference::update()', $pe->getMessage());
  }
}

}
?>
