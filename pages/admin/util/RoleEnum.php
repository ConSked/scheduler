<?php  // $Id: RoleEnum.php 505 2012-06-04 19:07:47Z preston $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

// NULL will represent no roles
define("CREWMEMBER", "CREWMEMBER");
define("ORGANIZER", "ORGANIZER");
define("SUPERVISOR", "SUPERVISOR");


class RoleEnum
{
public static $ROLE_ARRAY = NULL;

private static $STRING_ARRAY = NULL;
public static function getString($role)
{
    $value = RoleEnum::$STRING_ARRAY[$role];
    if (is_null($value))
    {
       throw new Exception("RoleEnum::getString(" . $role . ") - no such role");
    }
    return $value;
} // getString

public static $OPTION = NULL;
public static $OPTION_CREWMEMBER = NULL;
public static $OPTION_SUPERVISOR = NULL;
public static $OPTION_ORGANIZER = NULL;

static function PrivateStaticConstructor()
{
    // todo - should be internationalized; should be easy enough with dates!

    if (!is_null(RoleEnum::$ROLE_ARRAY))  {  return;  }

    RoleEnum::$ROLE_ARRAY = array(CREWMEMBER, SUPERVISOR, ORGANIZER);

    RoleEnum::$STRING_ARRAY = array(CREWMEMBER  => "Crew",
                                    SUPERVISOR  => "Supervisor",
                                    ORGANIZER   => "Organizer");

    RoleEnum::$OPTION_CREWMEMBER   = array(CREWMEMBER, RoleEnum::$STRING_ARRAY[CREWMEMBER]);
    RoleEnum::$OPTION_SUPERVISOR   = array(SUPERVISOR, RoleEnum::$STRING_ARRAY[SUPERVISOR]);
    RoleEnum::$OPTION_ORGANIZER    = array(ORGANIZER,  RoleEnum::$STRING_ARRAY[ORGANIZER]);

    RoleEnum::$OPTION = array(RoleEnum::$OPTION_CREWMEMBER,
                              RoleEnum::$OPTION_SUPERVISOR,
                              RoleEnum::$OPTION_ORGANIZER);
} // PrivateStaticConstructor()
} // RoleEnum

RoleEnum::PrivateStaticConstructor(); // call static constructor

?>
