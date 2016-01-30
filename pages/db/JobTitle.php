<?php  // $Id: JobTitle.php 2420 2012-10-30 18:59:11Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/log.php');


define("JOBTITLE_SELECT_SUFFIX", " ORDER BY jobTitle");
define("JOBTITLE_SELECT_PREFIX", "SELECT DISTINCT expoid, jobTitle FROM jobtitle ");
define("JOBTITLE_SELECT_EXPO",   JOBTITLE_SELECT_PREFIX . " WHERE expoid = ? " . JOBTITLE_SELECT_SUFFIX);

class JobTitle
{

public $expoid;
public $jobTitle;

public static function selectExpo($expoId)
{
    try
    {
        $rows = simpleSelect("JobTitle", JOBTITLE_SELECT_EXPO, array($expoId));
        return $rows;
    }
    catch (PDOException $pe)
    {
        logMessage('JobTitle::selectExpo(' . $expoId . ')', $pe->getMessage());
    }
    return array();
} // selectExpo

public static function titleEnums($expoId)
{
    $jtList = self::selectExpo($expoId);
    $enums = array();
    foreach ($jtList as $jt)
    {
        $enums[] = $jt->jobTitle;
    }
    $jtList = NULL;
    return $enums;
} // titleEnums

public static function titleOptions($expoId)
{
    $jtList = self::selectExpo($expoId);
    $options = array();
    foreach ($jtList as $jt)
    {
        $options[] = array($jt->jobTitle, $jt->jobTitle);
    }
    $jtList = NULL;
    return $options;
} // titleOptions

public function insert()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("INSERT INTO jobtitle (expoid, jobTitle) VALUES (?, ?)");
		$stmt->execute(array($this->expoid, $this->jobTitle));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('JobTitle::insert()', $pe->getMessage());
	}
} // insert

public function update($newJobTitle)
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("UPDATE jobtitle SET jobTitle = ? WHERE expoid = ? AND jobTitle = ?");
		$stmt->execute(array($newJobTitle, $this->expoid, $this->jobTitle));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('JobTitle::update()', $pe->getMessage());
	}
} // update

public function delete()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("DELETE FROM jobtitle WHERE expoid = ? AND jobtitle = ?");
		$stmt->execute(array($this->expoid, $this->jobTitle));
		$dbh->commit();
		return $this;
	}
	catch (PDOException $pe)
	{
		logMessage('JobTitle::delete()', $pe->getMessage());
	}
} //delete

} // JobTitle

?>
