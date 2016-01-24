<?php // $Id: Menu.php 2432 2003-01-07 20:27:30Z cross $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.

require_once('properties/constants.php');
require_once('swwat/gizmos/html.php');
require_once('util/session.php');

define("MENU_LOGOUT", "logout");

define("MENU_REQUEST_SCHEDULE", "requestschedule");
define("MENU_SEND_MESSAGE", "sendmessage");
#define("MENU_WORKER_MESSAGE", "workermessage");
define("MENU_VIEW_WORKER", "viewworker");
define("MENU_VIEW_SITEADMIN", "viewsiteadmin");
define("MENU_VIEW_EXPOCURRENT", "viewexpocurrent");
define("MENU_VIEW_SCHEDULE", "viewschedule");
define("MENU_VIEW_WORKERLIST", "viewworkerlist");
define("MENU_CHECKIN_CLIENT", "checkinclient");
define("MENU_EXPO_CHECKIN_CLIENT", "expocheckinclient");
define("MENU_CHECKIN_GRID", "checkingrid");
define("MENU_CHECKIN_STATION_DASHBOARD", "checkinstationdashboard");
define("MENU_CHECKIN_WORKER_DASHBOARD", "checkinworkerdashboard");
define("MENU_SCHEDULING_REPORT", "schedulingreport");
define("MENU_JOBTITLE", "jobtitle");

define("MENU_ADD_EXPO", "addexpo");
define("MENU_ADD_STATION", "addstation");

class Menu
{

private static $MENU_ACTION_ARRAY = array(
    MENU_ADD_EXPO                   => "ExpoEditPage.php",
    MENU_ADD_STATION                => "StationEditPage.php",
    MENU_LOGOUT                     => "WorkerLoginPage.php",
    MENU_REQUEST_SCHEDULE           => "PreferenceWelcomePage.php",
    MENU_SEND_MESSAGE               => "SendMessagePage.php",
#    MENU_WORKER_MESSAGE             => "WorkerMessagePage.php",
    MENU_VIEW_WORKER                => "WorkerViewPage.php",
    MENU_VIEW_SITEADMIN             => "SiteAdminPage.php",
    MENU_VIEW_EXPOCURRENT           => "ExpoViewPage.php",
    MENU_VIEW_SCHEDULE              => "WorkerSchedulePage.php",
    MENU_VIEW_WORKERLIST            => "WorkerListPage.php",
	MENU_CHECKIN_CLIENT             => "ShiftCheckInPage.php",
	MENU_EXPO_CHECKIN_CLIENT        => "ExpoCheckInPage.php",
	MENU_CHECKIN_GRID               => "ShiftCheckInGridPage.php",
	MENU_CHECKIN_STATION_DASHBOARD  => "CheckInStationDashboardPage.php",
	MENU_CHECKIN_WORKER_DASHBOARD   => "CheckInWorkerDashboardPage.php",
	MENU_SCHEDULING_REPORT          => "SchedulingReportPage.php",
	MENU_JOBTITLE                   => "JobTitlePage.php"
); // MENU_ACTION_ARRAY

private static $MENU_LABEL_ARRAY = array(
    MENU_ADD_EXPO                   => "Create New Expo",
    MENU_ADD_STATION                => "Create New Station",
    MENU_LOGOUT                     => "Logout",
    MENU_REQUEST_SCHEDULE           => "Request Schedule",
    MENU_SEND_MESSAGE               => "Send Message",
#    MENU_WORKER_MESSAGE             => "Send Message",
    MENU_VIEW_WORKER                => "View My Account",
    MENU_VIEW_SITEADMIN             => "Site Admin",
    MENU_VIEW_EXPOCURRENT           => "View Current Expo",
    MENU_VIEW_SCHEDULE              => "View My Schedule",
    MENU_VIEW_WORKERLIST            => "View Staff",
	MENU_CHECKIN_CLIENT             => "Shift Check-In",
	MENU_EXPO_CHECKIN_CLIENT        => "Expo Check-In",
	MENU_CHECKIN_GRID               => "Shift Check-In Grid",
	MENU_CHECKIN_STATION_DASHBOARD  => "Check-In Dashboard\n (by Station)",
	MENU_CHECKIN_WORKER_DASHBOARD   => "Check-In Dashboard\n (by Worker)",
	MENU_SCHEDULING_REPORT          => "View Scheduling Report",
	MENU_JOBTITLE                   => "Edit Job Titles"
); // MENU_LABEL_ARRAY

public static function addMenuItem($menuItem, $isDisabled = FALSE)
{
    echo "<tr>\n";
	echo "  <td>\n";
	echo "    <form method=\"GET\" action=\"", Menu::$MENU_ACTION_ARRAY[$menuItem], "\" name=\"menu_item_", $menuItem, "\">\n";
    echo "    ".swwat_createMenuInputSubmit($menuItem, Menu::$MENU_LABEL_ARRAY[$menuItem], $isDisabled)."\n";
    echo "    </form>\n";
	echo "  </td>\n";
	echo "</tr>\n";
    return;
} // addMenuItem

public static function addMenu($menuItemArray)
{
    echo "<div id=\"menu\">\n<table>\n";
    // always have a logout
    Menu::addMenuItem(MENU_LOGOUT);
    for ($k = 0; $k < count($menuItemArray); $k++)
    {
        Menu::addMenuItem($menuItemArray[$k]);
    } // $k
    // always able to see self's schedule
	$author = getWorkerAuthenticated();
	$worker = getWorkerCurrent();
	$expo = getExpoCurrent($author->workerid);
	$station = getStationCurrent($author->workerid);

    if (is_null($worker))
	{
		$worker = $author;
	}
    if ($author->isOrganizer() || $author->isSupervisor())
    {
#        Menu::addMenuItem(MENU_WORKER_MESSAGE);
		if (!is_null($expo))
		{
			if (strpos($_SERVER['SCRIPT_URL'], "ShiftCheckInPage.php"))
			{
        		Menu::addMenuItem(MENU_EXPO_CHECKIN_CLIENT);
			}
			else if (!strpos($_SERVER['SCRIPT_URL'], "ExpoCheckInPage.php"))
			{
				if (!is_null($station))
				{
	        		Menu::addMenuItem(MENU_CHECKIN_CLIENT);
				}
				else
				{
	        		Menu::addMenuItem(MENU_EXPO_CHECKIN_CLIENT);
				}
			}
		}
    }
    if (!is_null($expo))
    {
		if (!strpos($_SERVER['SCRIPT_URL'], Menu::$MENU_ACTION_ARRAY[MENU_VIEW_EXPOCURRENT]))
		{
        	Menu::addMenuItem(MENU_VIEW_EXPOCURRENT);
		}
        Menu::addMenuItem(MENU_VIEW_SCHEDULE, !($expo->scheduleVisible));
    }
    Menu::addMenuItem(MENU_VIEW_WORKER);
    echo "</table>\n</div><!-- menu -->\n";
    return;
} // addMenu

} // Menu

?>
