<?php  // $Id: ScheduleEnum.php 2289 2012-09-28 01:58:39Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

define("ASSIGNANDSUBTRACT", "AssignAndSubtract");
define("FIRSTCOMEFIRSTSERVE", "FirstComeFirstServed");
define("FIRSTCOMELOCATIONLOCKED", "FirstComeLocationLocked");
define("FIRSTCOMESOFTLOCATIONLOCKED", "FirstComeSoftLocationLocked");

class ScheduleEnum
{
public static $ENUM_ARRAY = NULL;

private static $STRING_ARRAY = NULL;
public static function getString($value)
{
    $value = self::$STRING_ARRAY[$value];
    if (is_null($value))
    {
       throw new Exception("ScheduleEnum::getString($value) - no such algorithm");
    }
    return $value;
} // getString

public static $OPTIONS = array();

static function PrivateStaticConstructor()
{
    // todo - should be internationalized; should be easy enough with dates!

    if (!is_null(self::$ENUM_ARRAY))  {  return;  }

    self::$ENUM_ARRAY = array(ASSIGNANDSUBTRACT, FIRSTCOMEFIRSTSERVE, FIRSTCOMELOCATIONLOCKED,FIRSTCOMESOFTLOCATIONLOCKED);

    self::$STRING_ARRAY = array(ASSIGNANDSUBTRACT    => "AssignAndSubtract",
                                FIRSTCOMEFIRSTSERVE  => "FirstComeFirstServed",
                                FIRSTCOMELOCATIONLOCKED => "FirstComeLocationLocked",
                                FIRSTCOMESOFTLOCATIONLOCKED => "FirstComeSoftLocationLocked");
           

    self::$OPTIONS[] = array(ASSIGNANDSUBTRACT, self::$STRING_ARRAY[ASSIGNANDSUBTRACT]);
    self::$OPTIONS[] = array(FIRSTCOMEFIRSTSERVE,  self::$STRING_ARRAY[FIRSTCOMEFIRSTSERVE]);
    self::$OPTIONS[] = array(FIRSTCOMELOCATIONLOCKED,  self::$STRING_ARRAY[FIRSTCOMELOCATIONLOCKED]);
    self::$OPTIONS[] = array(FIRSTCOMESOFTLOCATIONLOCKED,  self::$STRING_ARRAY[FIRSTCOMESOFTLOCATIONLOCKED]);

} // PrivateStaticConstructor()
} // ScheduleEnum

ScheduleEnum::PrivateStaticConstructor(); // call static constructor

?>
