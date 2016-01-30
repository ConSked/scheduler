<?php  // $Id: ReviewEnum.php 1646 2012-09-01 15:28:43Z preston $ Copyright (c) ConSked, LLC. All Rights Reserved.

define("UNREVIEWED", "UNREVIEWED");
define("APPROVED", "APPROVED");
define("DECLINED", "DECLINED");
define("DELETE", "DELETE");

class ReviewEnum
{
public static $REVIEW_ARRAY = NULL;

private static $STRING_ARRAY = NULL;
public static function getString($value)
{
    $value = self::$STRING_ARRAY[$value];
    if (is_null($value))
    {
       throw new Exception("ReviewEnum::getString($value) - no such status");
    }
    return $value;
} // getString

public static $OPTIONS_UNREVIEWED = array();
public static $OPTIONS_APPROVED = array();
public static $OPTIONS_DECLINED = array();
public static $OPTIONS_STAFF = array();
private static $OPTIONS_STATUS = array(); // getList only

public static function getList($isOrganizer, $currentStatus)
{
    if ($isOrganizer)
    {
        return self::$OPTIONS_STATUS[$currentStatus];
    }
    return self::$OPTIONS_STAFF;
} // getList

static function PrivateStaticConstructor()
{
    // todo - should be internationalized; should be easy enough with dates!

    if (!is_null(self::$REVIEW_ARRAY))  {  return;  }

    self::$REVIEW_ARRAY = array(UNREVIEWED, APPROVED, DECLINED, DELETE);

    self::$STRING_ARRAY = array(UNREVIEWED  => "Unreviewed",
                                APPROVED    => "Approved",
                                DECLINED    => "Declined",
                                DELETE      => "Delete");

    // set up options
    // if Organizer: UnReviewed->Approve, Decline, Delete; Approve->UnReviewed, Decline; Decline->UnReviewed, Approve
    // repeat if Organizer: Delete only from UnReviewed
    // if Worker: UnReviewed->Delete
    self::$OPTIONS_UNREVIEWED[] = array(UNREVIEWED, self::$STRING_ARRAY[UNREVIEWED]);
    self::$OPTIONS_UNREVIEWED[] = array(APPROVED,   self::$STRING_ARRAY[APPROVED]);
    self::$OPTIONS_UNREVIEWED[] = array(DECLINED,   self::$STRING_ARRAY[DECLINED]);
    self::$OPTIONS_UNREVIEWED[] = array(DELETE,     self::$STRING_ARRAY[DELETE]);

    self::$OPTIONS_APPROVED[] = array(UNREVIEWED,   self::$STRING_ARRAY[UNREVIEWED]);
    self::$OPTIONS_APPROVED[] = array(APPROVED,     self::$STRING_ARRAY[APPROVED]);
    self::$OPTIONS_APPROVED[] = array(DECLINED,     self::$STRING_ARRAY[DECLINED]);

    self::$OPTIONS_DECLINED[] = array(UNREVIEWED,   self::$STRING_ARRAY[UNREVIEWED]);
    self::$OPTIONS_DECLINED[] = array(APPROVED,     self::$STRING_ARRAY[APPROVED]);
    self::$OPTIONS_DECLINED[] = array(DECLINED,     self::$STRING_ARRAY[DECLINED]);

    // make getList simple
    self::$OPTIONS_STATUS[UNREVIEWED] = self::$OPTIONS_UNREVIEWED;
    self::$OPTIONS_STATUS[APPROVED]   = self::$OPTIONS_APPROVED;
    self::$OPTIONS_STATUS[DECLINED]   = self::$OPTIONS_DECLINED;

    self::$OPTIONS_STAFF[] = array(UNREVIEWED, self::$STRING_ARRAY[UNREVIEWED]);
    self::$OPTIONS_STAFF[] = array(DELETE,     self::$STRING_ARRAY[DELETE]);

    // self::$OPTION_UNREVIEWED = array(UNREVIEWED, self::$STRING_ARRAY[UNREVIEWED]); ...
    // self::$OPTION = array(self::$OPTION_UNREVIEWED, ...
} // PrivateStaticConstructor()
} // ReviewEnum

ReviewEnum::PrivateStaticConstructor(); // call static constructor

?>
