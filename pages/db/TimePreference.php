<?php  // $Id: TimePreference.php 2200 2012-09-22 18:26:20Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('db/dbutil.php');
require_once('util/log.php');

define("SHIFT_SELECT", "SELECT DISTINCT workerid, shift1, shift2, shift3, shift4, shift5, shift6, shift7, shift8, shift9, shift10, shift11, shift12, shift13, shift14, shift15, shift16, shift17, shift18, shift19, shift20, shift21, shift22, shift23, shift24, shift25, shift26, shift27, shift28, shift29, shift30, shift31, shift32, shift33, shift34, shift35, shift36, shift37, shift38, shift39, shift40, shift41, shift42, shift43, shift44, shift45, shift46, shift47, shift48, shift49, shift50 FROM timepreference WHERE workerid = ?");
class TimePreference
{
public $workerid;
public $shift1;
public $shift2;
public $shift3;
public $shift4;
public $shift5;
public $shift6;
public $shift7;
public $shift8;
public $shift9;
public $shift10;
public $shift11;
public $shift12;
public $shift13;
public $shift14;
public $shift15;
public $shift16;
public $shift17;
public $shift18;
public $shift19;
public $shift20;
public $shift21;
public $shift22;
public $shift23;
public $shift24;
public $shift25;
public $shift26;
public $shift27;
public $shift28;
public $shift29;
public $shift30;
public $shift31;
public $shift32;
public $shift33;
public $shift34;
public $shift35;
public $shift36;
public $shift37;
public $shift38;
public $shift39;
public $shift40;
public $shift41;
public $shift42;
public $shift43;
public $shift44;
public $shift45;
public $shift46;
public $shift47;
public $shift48;
public $shift49;
public $shift50;

public static function selectID($workerId)
{
	try
	{
		$rows = simpleSelect("TimePreference", SHIFT_SELECT, array($workerId));
		if (1 != count($rows))
		{
			return NULL;
		}
		return $rows[0];
	}
	catch (PDOException $pe)
	{
		logMessage('TimePreference::selectID(' . $workerId . ')', $pe->getMessage());
	}
}

public function insert()
{
	try
	{
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("INSERT INTO timepreference (workerid, shift1, shift2, shift3, shift4, shift5, shift6, shift7, shift8, shift9, shift10, "
		                    . "shift11, shift12, shift13, shift14, shift15, shift16, shift17, shift18, shift19, shift20, shift21, shift22, shift23, shift24, shift25, "
                            . "shift26, shift27, shift28, shift29, shift30, shift31, shift32, shift33, shift34, shift35, shift36, shift37, shift38, shift39, shift40, "
                            . "shift41, shift42, shift43, shift44, shift45, shift46, shift47, shift48, shift49, shift50) "
		                    . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute(array($this->workerid, $this->shift1, $this->shift2, $this->shift3, $this->shift4, $this->shift5, $this->shift6, $this->shift7, $this->shift8, $this->shift9, $this->shift10,
		                     $this->shift11, $this->shift12, $this->shift13, $this->shift14, $this->shift15, $this->shift16, $this->shift17, $this->shift18, $this->shift19, $this->shift20,
                              $this->shift21, $this->shift22, $this->shift23, $this->shift24, $this->shift25, $this->shift26, $this->shift27, $this->shift28, $this->shift29, $this->shift30,
                              $this->shift31, $this->shift32, $this->shift33, $this->shift34, $this->shift35, $this->shift36, $this->shift37, $this->shift38, $this->shift39, $this->shift40,
                              $this->shift41, $this->shift42, $this->shift43, $this->shift44, $this->shift45, $this->shift46, $this->shift47, $this->shift48, $this->shift49, $this->shift50));
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
		$dbh = getPDOConnection();
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("UPDATE timepreference SET shift1 = ?, shift2 = ?, shift3 = ?, shift4 = ?, shift5 = ?, shift6 = ?, shift7 = ?, shift8 = ?, shift9 = ? , shift10 = ?, "
		                    . "shift11 = ?, shift12 = ?, shift13 = ?, shift14 = ?, shift15 = ?, shift16 = ?, shift17 = ?, shift18 = ?, shift19 = ?, shift20 = ?, shift21 = ?, shift22 = ?, "
                            . "shift23 = ?, shift24 = ?, shift25 = ?, shift26 = ?, shift27 = ?, shift28 = ?, shift29 = ?, shift30 = ?, shift31 = ?, shift32 = ?, shift33 = ?, shift34 = ?, "
                            . "shift35 = ?, shift36 = ?, shift37 = ?, shift38 = ?, shift39 = ?, shift40 = ?, shift41 = ?, shift42 = ?, shift43 = ?, shift44 = ?, shift45 = ?, shift46 = ?, "
                            . "shift47 = ?, shift48 = ?, shift49 = ?, shift50 = ?  "
                            . "WHERE workerid = ?");
		$stmt->execute(array($this->shift1, $this->shift2, $this->shift3, $this->shift4, $this->shift5, $this->shift6, $this->shift7, $this->shift8, $this->shift9, $this->shift10,
		                     $this->shift11, $this->shift12, $this->shift13, $this->shift14, $this->shift15, $this->shift16, $this->shift17, $this->shift18, $this->shift19, $this->shift20,
                             $this->shift21, $this->shift22, $this->shift23, $this->shift24, $this->shift25, $this->shift26, $this->shift27, $this->shift28, $this->shift29, $this->shift30,
                             $this->shift31, $this->shift32, $this->shift33, $this->shift34, $this->shift35, $this->shift36, $this->shift37, $this->shift38, $this->shift39, $this->shift40,
                             $this->shift41, $this->shift42, $this->shift43, $this->shift44, $this->shift45, $this->shift46, $this->shift47, $this->shift48, $this->shift49, $this->shift50,
		                     $this->workerid));
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
