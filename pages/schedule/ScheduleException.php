<?php // $Id: ScheduleException.php 1521 2012-08-28 22:31:39Z preston $ Copyright (c) Preston C. Urka. All Rights Reserved.

class ScheduleException extends Exception
{
} // ScheduleException

class ScheduleImmutableException extends ScheduleException
{
} // ScheduleImmutableException

class ScheduleImpossibleException extends ScheduleException
{
} // ScheduleImpossibleException

class ScheduleOverMaxHoursException extends ScheduleException
{
} // ScheduleOverMaxHoursException

class ScheduleConflictException extends ScheduleException
{
public $conflict = NULL;
} // ScheduleConflictException

class MinMetException extends ScheduleException
{
} // MinCrewMetException

class StaffOverException extends ScheduleException  {}
class CrewOverException extends StaffOverException  {}
class SupervisorOverException extends StaffOverException  {}

class StaffUnderException extends ScheduleException  {}
class CrewUnderException extends StaffUnderException  {}
class SupervisorUnderException extends StaffUnderException  {}

?>
