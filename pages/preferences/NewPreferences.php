<?php // $Id: NYCCPreferences.php 2419 2012-10-29 18:01:08Z ecgero $ Copyright (c) ConSked, LLC. All Rights Reserved.

require_once('db/GrossPreference.php');
require_once('db/Job.php');
require_once('db/JobPreference.php');
require_once('db/NewTimePreference.php');
require_once('db/Station.php');
require_once('properties/constants.php');
require_once('swwat/gizmos/html.php');
require_once('util/log.php');

function welcomePageTitle($expoTitle, $pageNum)
{
  $saved = 0;
  if (isset($_REQUEST[PARAM_SAVE]))
  {
    $saved = 1;
  }

	echo("<div class=\"center\"><h3>Welcome to the $expoTitle Crew scheduler!</h3></div>\n");
  if ($saved == 1)
  {
    echo("<div class=\"center\"><h2 style=\"color: red\">Preferences Saved!</h2></div>\n");
  }
  echo("<br>\n");
} // welcomePageTitle

function welcomePageContent($author, $expo)
{
  $position = NULL;
  if (isset($_REQUEST[PARAM_LIST_INDEX]) && !is_null($_REQUEST[PARAM_LIST_INDEX]))
  {
    $position = $_REQUEST[PARAM_LIST_INDEX];
  }

  echo("<form method=\"POST\" name=\"preferencewelcome_form\" action=\"PreferenceWelcomeAction.php?".PARAM_LIST_INDEX."=".$position."\">\n");
  $step = 1;

  $step = timePreferences($step, $author, $expo);

  $step = locationPreferences($step, $author, $expo);

  $step = maxHoursPreferences($step, $author, $expo);

  saveButton($step);
}

function timePreferences($step, $author, $expo)
{
  echo("<div class=\"center\"><b>".$step."</b>. Click on the grid below to select your time preferences.</div>\n");
  echo("<div class=\"center\">(&#x2713; = available, &#x2715; = not available)</div>\n");
  echo("<br>\n");

  $stations = StationJob::selectExpo($expo->expoid);

  $ctitles = array('12', '01', '02', '03', '04', '05', '06', '07', 
                   '08', '09', '10', '11', '12', '01', '02', '03',
                   '04', '05', '06', '07', '08', '09', '10', '11'); 

  $startTime = $expo->startTime;
  $stopTime = $expo->stopTime;
  $interval = $startTime->diff($stopTime)->d; 

  $day = clone $startTime;
  for ($i = 0; $i <= $interval; $i++)
  {
    if ($i != 0) $day = $day->add(new DateInterval('P1D'));
    $days[$i] = clone $day;
  }

  $rtitles = array();
  $colorArray = array();
  for ($i = 0; $i < count($days); $i++)
  {
    $tp_old = NewTimePreference::selectID($author->workerid, $expo->expoid, swwat_format_isodate($days[$i]));

    echo("<input type=\"hidden\" name=\"".PARAM_DAY.$i."\" value=\"".swwat_format_isodate($days[$i])."\">\n");
    $rtitles[$i] = swwat_format_preferencesdate($days[$i]);

    $hourMin = stationStartHourMin($stations, $days[$i]);
    $hourMax = stationStopHourMax($stations, $days[$i]);

    for ($j = 0; $j < count($ctitles); $j++)
    {
      if ($j >= $hourMin && $j < $hourMax)
      {
        $colorArray[$i][$j] = 'green';
        if (count($tp_old) != 0)
        {
          $field = 'hour'.($j+1);
          $tpref_old = $tp_old[0]->$field;
          if ($tpref_old == 0)
          {
            $colorArray[$i][$j] = 'red';
          }
        }
      }
      else
      {
        $colorArray[$i][$j] = 'gray';
      }
    }
  }
  include('PrefTable.php');
  echo("<br><br>\n");

  $step++;
  return $step;
}

function stationStartHourMin($stations, $day)
{
  $daystring = swwat_format_isodate($day);

  $startHourMin = 24;
  foreach($stations as $station)
  {
    $startTime = swwat_format_isodate($station->startTime);
    if ($startTime == $daystring)
    {
      $startHour = date_format($station->startTime, 'H');
      if ($startHour < $startHourMin)
      {
        $startHourMin = $startHour;
      }
    }
  }

  return (int) $startHourMin;
}

function stationStopHourMax($stations, $day)
{
  $daystring = swwat_format_isodate($day);

  $stopHourMax = 0;
  foreach($stations as $station)
  {
    $stopTime = swwat_format_isodate($station->stopTime);
    if ($stopTime == $daystring)
    {
      $stopHour = date_format($station->stopTime, 'H');
      if ($stopHour > $stopHourMax)
      {
        $stopHourMax = $stopHour;
      }
    }
  }

  return (int) $stopHourMax;
}

function locationPreferences($step, $author, $expo)
{
  $locationList = GrossPreference::selectLocation($expo->expoid);
  if (count($locationList) > 1)
  {
    echo("<div class=\"center\"><b>".$step."</b>. Select your location preferences.</div>\n");
    echo("<div class=\"center\">(0 = absolutely not, 10 = absolutely yes)</div>\n");
    echo("<br>\n");

    $jp_old = JobPreference::selectID($author->workerid, $expo->expoid);
    $default = array();
    for ($i = 0; $i < count($locationList); $i++)
    {
      echo("<input type=\"hidden\" name=\"".PARAM_LOCATION.$i."\" value=\"".$locationList[$i]."\">\n");
      if (count($jp_old) != 0)
      {
        $arg = 'job' . ($i+1);
        $default[$i] = ($jp_old->$arg)/10;
      }
      else
      {
        $default[$i] = 5;
      }
    }

    echo("<table class=\"prefs\">\n");
    swwat_spaces(2); echo("<tbody>\n");

    for ($i = 0; $i < count($locationList); $i++)
    {
      swwat_spaces(4); echo("<tr>\n");
      swwat_spaces(6); echo("<th class=\"blue\">".$locationList[$i]."</th>\n");
      swwat_spaces(6); echo("<td>\n");
      swwat_spaces(8); echo("<select name=\"".PARAM_LPREFS.$i."\">\n");
      for ($j = 0; $j < 11; $j++)
      {
        $checked = ($default[$i] == $j) ? ' selected' : '';
        swwat_spaces(10); echo("<option value=\"".($j)."\"".$checked.">".$j."</option>\n");
      }
      swwat_spaces(8); echo("</select>\n");
      swwat_spaces(6); echo("</td>\n");
      swwat_spaces(4); echo("</tr>\n");
    }

    swwat_spaces(2); echo("</tbody>\n");
    echo("</table>\n");
    echo("<br><br>\n");

    $step++;
  }
  else if (count($locationList) == 1)
  {
    $i = 0;
    echo("<input type=\"hidden\" name=\"".PARAM_LOCATION.$i."\" value=\"".$locationList[$i]."\">\n");
    echo("<input type=\"hidden\" name=\"".PARAM_LPREFS.$i."\" value=\"10\">\n");
  }
  return $step;
}

function maxHoursPreferences($step, $author, $expo)
{
  echo("<div class=\"center\"><b>".$step."</b>. How many hours can you work?</div>\n");
  echo("<br>\n");
  echo("<div class=\"center\">\n");

  $maxhours = $author->selectMaxHours($expo->expoid);
  $optionArray = array();
  for ($i = 0; $i < $maxhours; $i++)
  {
    $optionArray[$i] = array(($maxhours - $i), ($maxhours - $i));
  }
  swwat_createSelect(0, PARAM_MAXHOURS, $optionArray, $maxhours);

  echo("</div>\n");
  echo("<br><br>\n");

  $step++;
  return $step;
}

function saveButton($step)
{
  echo("<div class=\"center\"><b>".$step."</b>. Save your Preferences.</div>\n");
  echo("<br>\n");
  echo("<div class=\"center\">\n");
  echo("<button type=\"submit\" class=\"blueButton\">Save</button>\n");
  echo("</div>\n");
}

function welcomeActionContent($author, $expo)
{
  // Time Preferences
  $i = 0;
  while (isset($_POST[PARAM_DAY . $i]))
  {
    $days[$i] = parsePreferenceString(PARAM_DAY, $i);
    $tprefs[$i] = parsePreferenceString(PARAM_TPREFS, $i);

    $i++;
  }

  $tp = new NewTimePreference;
  $tp->workerid = $author->workerid;
  $tp->expoid = $expo->expoid;

  for ($i = 0; $i < count($days); $i++)
  {
    $tp->day = $days[$i];

    $hcodes = str_split($tprefs[$i]);
    for ($j = 0; $j < count($hcodes); $j++)
    {
      $hdesire = 0;
      if ($hcodes[$j] == 2) {$hdesire = 100;}

      $hour = "hour".($j+1);
      $tp->$hour = $hdesire;
    }

    $test = NewTimePreference::selectID($author->workerid, $expo->expoid, $days[$i]);
    if (count($test) != 0)
    {
      $tp->update();
    }
    else
    {
      $tp->insert();
    }
  }

  // Location Preferences
  $i = 0;
  while (isset($_POST[PARAM_LOCATION . $i]))
  {
    $locations[$i] = parsePreferenceString(PARAM_LOCATION, $i);
    $lpref[$i] = parsePreferenceNumber(PARAM_LPREFS, $i);

    $i++;
  }

  $jp = new JobPreference;
  $jp->workerid = $author->workerid;
  $jp->expoid = $expo->expoid;

  $count_jobs = $jp->number_jobs;
  for ($i = 0; $i < $count_jobs; $i++)
  {
    $field = 'job'.($i+1);
    if (isset($lpref[$i]))
    {
      $jp->$field = ($lpref[$i] * 10);
    }
    else
    {
      $jp->$field = 0;
    }
  }

  $test = JobPreference::selectID($author->workerid, $expo->expoid);
  if (count($test) != 0)
  {
    $jp->update();
  }
  else
  {
    $jp->insert();
  }

  if (isset($_POST[PARAM_MAXHOURS]) && !is_null($_POST[PARAM_MAXHOURS]))
  {
    $author->updateMaxHours($expo->expoid, swwat_parse_string(html_entity_decode($_POST[PARAM_MAXHOURS])));
  }

  setShiftPreferences($author->workerid, $expo->expoid, $locations);
  return;
}

function parsePreferenceString($param, $i)
{
  $value = html_entity_decode($_POST[$param . $i]);
  if (0 == strcmp(PARAM_UNAVAILABLE, $value))
  {
    $value = NULL;
  }
  else
  {
    $value = swwat_parse_string($value, FALSE);
  }
  return $value;
}

function parsePreferenceNumber($param, $i)
{
  $value = html_entity_decode($_POST[$param . $i]);
  if (0 == strcmp(PARAM_UNAVAILABLE, $value))
  {
    $value = NULL;
  }
  else
  {
    $value = swwat_parse_number($value, FALSE);
  }
  return $value;
}

function setShiftPreferences($workerId, $expoId, $locations)
{
  $timePrefs = NewTimePreference::selectID($workerId, $expoId);

  $locationPrefs = JobPreference::selectID($workerId, $expoId);

  $jobs = Job::selectExpo($expoId);

  foreach ($jobs as $job)
  {
    $sp = new ShiftPreference;
    $sp->workerid = $workerId;
    $sp->jobid = $job->jobid;
    $sp->stationid = $job->stationid;
    $sp->expoid = $expoId;

    $station = Station::selectID($job->stationid);

    //time preference
    $timeZone = swwat_format_timezone($station->startTime);
    $startTime = swwat_format_epoch($station->startTime);
    $stopTime = swwat_format_epoch($station->stopTime);

    $zeroFlag = FALSE;
    $tdesire = 0;
    $count = 0;
    foreach ($timePrefs as $timePref)
    {
      $epoch = date_format(new DateTime($timePref->day, new DateTimeZone($timeZone)), 'U');
      for ($i = 0; $i < 24; $i++)
      {
        $startHour = $epoch;
        $endHour = strtotime("+1 hours", $epoch);

        if ($startHour >= $startTime && $endHour <= $stopTime)
        {
          $field = 'hour'.($i+1);
          $value = $timePref->$field;
          if ($value == 0)
          {
            $zeroFlag = TRUE;
          }

          $tdesire += $value;
          $count++;
        }

        $epoch = strtotime("+1 hours", $epoch);
      }
    }

    if ($zeroFlag || $count == 0)
    {
      $tdesire = 0;
    }
    else
    {
      $tdesire = $tdesire / $count;
    }

    // location preference
    $index = array_search($station->location, $locations);
    $field = 'job'.($index+1);
    $ldesire = $locationPrefs->$field;

    $desire = 0;
    if ($tdesire != 0 && $ldesire != 0)
    {
      $desire = ($tdesire + $ldesire)/2;
    }

    $sp->desirePercent = $desire;

    $sp->update();
  }
}

function welcomePageNavi()
{
	echo("<br><br>\n");
	echo("</form>\n");
}
?>
