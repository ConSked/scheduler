<?php // $Id: JobTitlePage.php 2434 2012-11-30 16:52:35Z ecgero $ Copyright (c) SwiftExpo, LLC. All Rights Reserved.
include('util/authenticateOrganizer.php');

require_once('db/JobTitle.php');
require_once('properties/constants.php');
require_once('section/Menu.php');
require_once('swwat/gizmos/format.php');
require_once('util/log.php');
require_once('util/session.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache"/>
	<meta http-equiv="expires" content="31 Dec 2011 12:00:00 GMT"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<title>SwiftShift - Job Title Page</title>
	<link href="css/site.css" rel="stylesheet" type="text/css">

	<script type="text/javascript">
	function jobTitleAction(id, type)
	{
		if (type == 'delete')
		{
			var ans = confirm("Do you wish to delete this job title?");
		}
		else if (type == 'edit')
		{
			var ans = confirm("Do you wish to save your edit to the job title?");
		}
		else if (type == 'add')
		{
			var ans = confirm("Do you wish to add this new job title?");
		}

		if (ans == false)
		{
			return;
		}

		var url = "JobTitleAction.php?id="+id+"&type="+type;

		document.forms['jobtitlepage_jobtitledata'].action = url;
		document.forms['jobtitlepage_jobtitledata'].submit();
	}
	</script>
</head>

<body>
<div id="container">

<?php

$expo = getExpoCurrent();

// ok, start the html
include('section/header.php');
?>

<div id="main">
    <?php
    if (!is_null($expo->expoid))
    {
        include('section/LinkExpo.php');
    }
    ?>

    <div id="jobtitlepage_jobtitledata">
		<form method="POST" id="jobtitlepage_jobtitledata">
		<table>
<?php
	$jobTitle = JobTitle::selectExpo($expo->expoid);
	$cnt = count($jobTitle);
	echo "<tr><td rowspan=\"".($cnt+1)."\" valign=\"top\">Job Title:&nbsp;</td>\n";

	for ($j = 0; $j < $cnt; $j++)
	{
		if (!strcmp($jobTitle[$j]->jobTitle, 'Crew'))
		{
			echo "<td><input type=\"text\" name=\"".PARAM_TITLE.$j."\" value=\"".$jobTitle[$j]->jobTitle."\" disabled=\"disabled\" /></td>\n";
			echo "<td></td>\n";
			echo "<td></td>\n";
			echo "</tr>\n";
		}
	}
	for ($j = 0; $j < $cnt; $j++)
	{
		if (strcmp($jobTitle[$j]->jobTitle, 'Crew'))
		{
			echo "<tr>\n";
			echo "<td><input type=\"text\" name=\"".PARAM_TITLE.$j."\" value=\"".$jobTitle[$j]->jobTitle."\" />";
			echo "<input type=\"hidden\" name=\"".PARAM_TITLE.$j."_old\" value=\"".$jobTitle[$j]->jobTitle."\" /></td>\n";
			echo "<td><input type=\"button\" name=\"edit\" value=\"Edit\" onclick=\"jobTitleAction(".$j.",'edit')\"></td>\n";
			echo "<td><input type=\"button\" name=\"delete\" value=\"Delete\" onclick=\"jobTitleAction(".$j.",'delete')\"></td>\n";
			echo "</tr>\n";
		}
	}

	echo "<tr>\n";
	echo "<td><input type=\"text\" name=\"".PARAM_TITLE.$cnt."\" value=\"\" />";
	echo "<input type=\"hidden\" name=\"".PARAM_TITLE.$cnt."_old\" value=\"\" /></td>\n";
	echo "<td><input type=\"button\" name=\"add\" value=\"Add\" onclick=\"jobTitleAction(".$cnt.",'add')\"></td>\n";
	echo "<td></td>\n";
	echo "</tr>\n";
?>
		</table>
		</form>
		<br />
    </div><!-- expoeditpage_expodata -->

</div><!-- main -->

<?php
    $menuItemArray = array();
    $menuItemArray[] = MENU_VIEW_SITEADMIN;
    $menuItemArray[] = MENU_VIEW_WORKERLIST;
    $menuItemArray[] = MENU_JOBTITLE;
    Menu::addMenu($menuItemArray);
    include('section/footer.php');
?>

</div><!-- continaer -->
</body></html>
